<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Review;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Unit III — Controller
| Unit IV  — Session Data Access
| Unit VI  — Eloquent Queries
|--------------------------------------------------------------------------
*/

class DashboardController extends Controller
{
    // ── GET /dashboard ────────────────────────────────────────
    public function index()
    {
        $role = session('user_role');
        if ($role === 'admin') {
            return redirect()->route('admin.index');
        }

        $uid = session('user_id');

        if ($role === 'founder') {
            $startups = Startup::where('user_id', $uid)
                ->withCount(['inquiries as inq_count'])
                ->orderByDesc('created_at')
                ->get();

            $startups->each(function($s) {
                $s->fmt_arr    = $s->formatted_arr;
                $s->fmt_asking = $s->formatted_asking;
            });

            $totalViews     = $startups->sum('views');
            $totalInquiries = $startups->sum('inq_count');
            $activeCount    = $startups->where('status', 'active')->count();
            $avgValuation   = $startups->avg('asking_price') ?? 0;

            $myIds = $startups->pluck('id')->toArray();
            $inquiries = [];
            if (!empty($myIds)) {
                $inquiries = \DB::table('inquiries')
                    ->join('startups', 'inquiries.startup_id', '=', 'startups.id')
                    ->whereIn('inquiries.startup_id', $myIds)
                    ->select('inquiries.*', 'startups.name as startup_name')
                    ->orderByDesc('inquiries.created_at')
                    ->limit(5)
                    ->get();
            }

            return view('dashboard.index', compact(
                'startups', 'totalViews', 'totalInquiries',
                'activeCount', 'avgValuation', 'inquiries'
            ));
        } else {
            // Investor / Acquirer / Advisor dashboard
            $favorites = Favorite::where('user_id', $uid)
                ->with('startup.founder')
                ->latest()
                ->limit(5)
                ->get();

            $totalFavorites = Favorite::where('user_id', $uid)->count();

            $sentInquiries = Inquiry::where('user_id', $uid)
                ->with('startup')
                ->latest()
                ->limit(5)
                ->get();

            $totalSentInquiries = Inquiry::where('user_id', $uid)->count();

            $reviews = Review::where('user_id', $uid)
                ->with('startup')
                ->latest()
                ->limit(5)
                ->get();

            $totalReviews = Review::where('user_id', $uid)->count();

            return view('dashboard.investor', compact(
                'favorites', 'totalFavorites', 'sentInquiries', 'totalSentInquiries', 'reviews', 'totalReviews'
            ));
        }
    }

    // ── GET /dashboard/inquiries ──────────────────────────────
    public function inquiries()
    {
        $userId = session('user_id');
        $role   = session('user_role');

        if ($role === 'founder') {
            $myIds = Startup::where('user_id', $userId)->pluck('id');
            $inquiries = Inquiry::whereIn('startup_id', $myIds)
                ->with(['startup', 'repliedBy'])
                ->orderByDesc('created_at')
                ->paginate(10);
            $isSender = false;
        } else {
            $inquiries = Inquiry::where('user_id', $userId)
                ->with(['startup.founder', 'repliedBy'])
                ->orderByDesc('created_at')
                ->paginate(10);
            $isSender = true;
        }

        return view('dashboard.inquiries', compact('inquiries', 'isSender'));
    }

    // ── POST /dashboard/inquiries/{inquiry}/reply ──────────────
    public function replyInquiry(Request $request, Inquiry $inquiry)
    {
        $userId = session('user_id');
        $role   = session('user_role');

        if ($role !== 'founder') {
            abort(403, 'Unauthorized. Only startup founders can reply to inquiries.');
        }

        // Verify that the founder owns the startup for this inquiry
        if (!$inquiry->startup || $inquiry->startup->user_id !== $userId) {
            abort(403, 'Unauthorized. You do not own the startup this inquiry was sent to.');
        }

        $request->validate([
            'reply_message' => 'required|string|min:10|max:2000',
        ]);

        $inquiry->update([
            'status'        => 'replied',
            'reply_message' => $request->reply_message,
            'replied_at'    => now(),
            'replied_by'    => $userId,
        ]);

        // Send database-backed in-app notification to the investor
        if ($inquiry->user_id) {
            \App\Models\Notification::create([
                'user_id' => $inquiry->user_id,
                'title'   => 'Founder Replied to Inquiry 💬',
                'message' => "The founder of {$inquiry->startup->name} replied: \"" . \Str::limit($request->reply_message, 60) . "\"",
            ]);
        }

        return back()->with('success', '✅ Reply submitted and notification sent to investor!');
    }

    // ── GET /dashboard/analytics ──────────────────────────────
    public function analytics()
    {
        $userId = session('user_id');
        $role   = session('user_role');

        if ($role === 'founder') {
            $startups = Startup::where('user_id', $userId)->withCount('inquiries')->get();
            return view('dashboard.analytics', compact('startups'));
        } else {
            // Get analytics data for investor
            $favoritesByCategory = Favorite::where('favorites.user_id', $userId)
                ->join('startups', 'favorites.startup_id', '=', 'startups.id')
                ->join('categories', 'startups.category_id', '=', 'categories.id')
                ->select('categories.name', \DB::raw('count(*) as count'))
                ->groupBy('categories.name')
                ->get();

            $inquiriesByType = Inquiry::where('user_id', $userId)
                ->select('interest_type', \DB::raw('count(*) as count'))
                ->groupBy('interest_type')
                ->get();

            return view('dashboard.analytics', compact('favoritesByCategory', 'inquiriesByType'));
        }
    }
}
