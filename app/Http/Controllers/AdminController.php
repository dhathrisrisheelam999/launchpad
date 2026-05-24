<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\User;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Review;
use App\Models\Investment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ── Admin Dashboard ───────────────────────────────────
    public function index()
    {
        $totalUsers     = User::count();
        $totalStartups  = Startup::count();
        $pendingCount   = Startup::where('status','pending')->count();
        $activeCount    = Startup::where('status','active')->count();
        $totalInquiries = Inquiry::count();
        $totalReviews   = Review::count();

        // New Investment metrics
        $totalInvestmentVolume  = Investment::where('payment_status', 'success')->where('status', 'approved')->sum('amount');
        $pendingInvestmentCount = Investment::where('status', 'pending')->count();
        $totalInvestmentsCount  = Investment::count();

        $recentStartups = Startup::with('founder')
                                 ->latest()
                                 ->take(5)
                                 ->get();

        $recentUsers = User::latest()->take(5)->get();

        // Chart 1: Investment volume by startup (top 5)
        $topInvestedStartups = Investment::select('startup_id', DB::raw('SUM(amount) as total_amount'))
            ->where('payment_status', 'success')
            ->where('status', 'approved')
            ->groupBy('startup_id')
            ->with('startup')
            ->orderByDesc('total_amount')
            ->take(5)
            ->get();

        $chartStartupsLabels = [];
        $chartStartupsData = [];
        foreach ($topInvestedStartups as $item) {
            $chartStartupsLabels[] = $item->startup ? $item->startup->name : 'Unknown';
            $chartStartupsData[] = $item->total_amount;
        }

        // Chart 2: Category breakdown of startups
        $categoryBreakdown = Startup::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $chartCategoryLabels = [];
        $chartCategoryData = [];
        foreach ($categoryBreakdown as $item) {
            $chartCategoryLabels[] = $item->category ? $item->category->name : 'Uncategorized';
            $chartCategoryData[] = $item->count;
        }

        return view('admin.index', compact(
            'totalUsers','totalStartups','pendingCount',
            'activeCount','totalInquiries','totalReviews',
            'recentStartups','recentUsers',
            'totalInvestmentVolume', 'pendingInvestmentCount', 'totalInvestmentsCount',
            'chartStartupsLabels', 'chartStartupsData',
            'chartCategoryLabels', 'chartCategoryData'
        ));
    }

    // ── Manage Startups ───────────────────────────────────
    public function startups(Request $request)
    {
        $status   = $request->get('status', 'all');
        $startups = Startup::with('founder')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('admin.startups', compact('startups', 'status'));
    }

    // ── Approve Startup ───────────────────────────────────
    public function approve(Startup $startup)
    {
        $startup->update([
            'status'        => 'active',
            'reject_reason' => null,
        ]);

        // Send notification to startup owner
        if ($startup->founder) {
            Notification::create([
                'user_id' => $startup->founder->id,
                'title'   => 'Startup Approved! 🚀',
                'message' => "Congratulations! Your startup '{$startup->name}' has been approved and is now active on the platform.",
            ]);
        }

        return back()->with('success', "✅ '{$startup->name}' has been approved and is now live!");
    }

    // ── Reject Startup ────────────────────────────────────
    public function reject(Request $request, Startup $startup)
    {
        $request->validate([
            'reject_reason' => 'required|string|min:10',
        ]);

        $startup->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        // Send notification to startup owner
        if ($startup->founder) {
            Notification::create([
                'user_id' => $startup->founder->id,
                'title'   => 'Startup Listing Rejected ❌',
                'message' => "Your startup '{$startup->name}' was rejected. Reason: {$request->reject_reason}",
            ]);
        }

        return back()->with('success', "❌ '{$startup->name}' has been rejected.");
    }

    // ── Delete Startup ────────────────────────────────────
    public function deleteStartup(Startup $startup)
    {
        $name = $startup->name;
        $startup->delete();
        return back()->with('success', "🗑️ '{$name}' deleted successfully.");
    }

    // ── Manage Users ──────────────────────────────────────
    public function users(Request $request)
    {
        $roleFilter = $request->get('role', 'all');
        $kycFilter = $request->get('kyc_status', 'all');

        $users = User::withCount('startups')
            ->when($roleFilter !== 'all', fn($q) => $q->where('role', $roleFilter))
            ->when($kycFilter !== 'all', fn($q) => $q->where('kyc_status', $kycFilter))
            ->latest()
            ->paginate(10);

        return view('admin.users', compact('users', 'roleFilter', 'kycFilter'));
    }

    // ── Update User Role ──────────────────────────────────
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:founder,investor,acquirer,advisor,admin',
        ]);

        if ($user->id === session('user_id') && $request->role !== 'admin') {
            return back()->with('error', 'You cannot demote yourself from admin role.');
        }

        $user->update(['role' => $request->role]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Role Updated 👤',
            'message' => "Your user role has been updated to: " . ucfirst($request->role),
        ]);

        return back()->with('success', "Role for '{$user->name}' updated to " . ucfirst($request->role) . ".");
    }

    // ── Approve User KYC ──────────────────────────────────
    public function approveKyc(User $user)
    {
        $user->update(['kyc_status' => 'approved']);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'KYC Verified Successfully ✅',
            'message' => 'Congratulations! Your accreditation documents have been reviewed and approved. You are now fully authorized to make investments.',
        ]);

        return back()->with('success', "KYC accreditation for '{$user->name}' has been approved!");
    }

    // ── Reject User KYC ───────────────────────────────────
    public function rejectKyc(Request $request, User $user)
    {
        $request->validate([
            'kyc_reject_reason' => 'required|string|max:500',
        ]);

        $user->update([
            'kyc_status' => 'rejected'
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'KYC Document Rejected ❌',
            'message' => "Your KYC accreditation was rejected. Reason: {$request->kyc_reject_reason}. Please upload valid documents in your profile dashboard.",
        ]);

        return back()->with('success', "KYC accreditation for '{$user->name}' has been rejected.");
    }

    // ── Delete User ───────────────────────────────────────
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }
        $name = $user->name;
        $user->delete();
        return back()->with('success', "🗑️ User '{$name}' deleted.");
    }

    // ── Manage Categories ─────────────────────────────────
    public function categories()
    {
        $categories = Category::withCount('startups')->get();
        return view('admin.categories', compact('categories'));
    }

    // ── Create Category ───────────────────────────────────
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'icon' => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        Category::create([
            'name'        => $request->name,
            'slug'        => \Str::slug($request->name),
            'icon'        => $request->icon,
            'description' => $request->description,
        ]);

        return back()->with('success', '✅ Category created!');
    }

    // ── Delete Category ───────────────────────────────────
    public function deleteCategory(Category $category)
    {
        $category->delete();
        return back()->with('success', '🗑️ Category deleted.');
    }

    // ── Manage Inquiries (Admin) ──────────────────────────
    public function inquiries(Request $request)
    {
        $status = $request->get('status', 'all');

        $inquiries = Inquiry::with(['startup', 'investor'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('admin.inquiries', compact('inquiries', 'status'));
    }

    // ── Reply to Inquiry (Admin) ─────────────────────────
    public function replyInquiry(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'reply_message' => 'required|string|min:10|max:2000',
        ]);

        $inquiry->update([
            'status'        => 'replied',
            'reply_message' => $request->reply_message,
            'replied_at'    => now(),
            'replied_by'    => session('user_id'),
        ]);

        // Send notification to the investor who raised the inquiry
        Notification::create([
            'user_id' => $inquiry->user_id,
            'title'   => 'Admin Replied to Inquiry 💬',
            'message' => "The admin replied to your inquiry for '{$inquiry->startup->name}': \"" . \Str::limit($request->reply_message, 60) . "\"",
        ]);

        return back()->with('success', '✅ Reply submitted and notification sent to investor!');
    }

    // ── Close Inquiry (Admin) ─────────────────────────────
    public function closeInquiry(Inquiry $inquiry)
    {
        $inquiry->update(['status' => 'closed']);

        Notification::create([
            'user_id' => $inquiry->user_id,
            'title'   => 'Inquiry Marked Closed 🔒',
            'message' => "Your inquiry for '{$inquiry->startup->name}' has been marked as closed.",
        ]);

        return back()->with('success', '🔒 Inquiry marked as closed.');
    }

    // ── Manage Investments (Admin) ────────────────────────
    public function investments(Request $request)
    {
        $status = $request->get('status', 'all');

        $investments = Investment::with(['startup', 'user'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('admin.investments', compact('investments', 'status'));
    }

    // ── Approve Investment (Admin) ────────────────────────
    public function approveInvestment(Investment $investment)
    {
        $investment->update(['status' => 'approved']);

        // Send notification to the investor
        Notification::create([
            'user_id' => $investment->user_id,
            'title'   => 'Investment Approved 🌟',
            'message' => "Your investment pledge of $" . number_format($investment->amount) . " in '{$investment->startup->name}' has been approved by the admin. Transaction complete!",
        ]);

        // Send notification to the founder
        if ($investment->startup->founder) {
            Notification::create([
                'user_id' => $investment->startup->founder->id,
                'title'   => 'Investment Funding Approved! 💰',
                'message' => "The admin approved {$investment->user->name}'s investment of $" . number_format($investment->amount) . " in {$investment->startup->name}.",
            ]);
        }

        return back()->with('success', "✅ Investment of $" . number_format($investment->amount) . " in '{$investment->startup->name}' approved!");
    }

    // ── Reject Investment (Admin) ─────────────────────────
    public function rejectInvestment(Request $request, Investment $investment)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $investment->update(['status' => 'rejected']);

        // Send notification to the investor
        Notification::create([
            'user_id' => $investment->user_id,
            'title'   => 'Investment Rejected ❌',
            'message' => "Your investment pledge of $" . number_format($investment->amount) . " in '{$investment->startup->name}' was rejected. Reason: {$request->reject_reason}",
        ]);

        return back()->with('success', "❌ Investment of $" . number_format($investment->amount) . " rejected.");
    }
}
