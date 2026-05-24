<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Startup;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Toggle favorite on/off
    public function toggle(Request $request, Startup $startup)
    {
        $userId = session('user_id');

        $existing = Favorite::where('user_id', $userId)
                            ->where('startup_id', $startup->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from favorites.';
            $isFav   = false;
        } else {
            Favorite::create([
                'user_id'    => $userId,
                'startup_id' => $startup->id,
            ]);
            $message = '❤️ Added to favorites!';
            $isFav   = true;
        }

        // JSON response for AJAX or redirect for normal request
        if ($request->expectsJson()) {
            return response()->json([
                'is_favorite' => $isFav,
                'message'     => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    // Show all favorites for logged in user
    public function index()
    {
        $userId    = session('user_id');
        $favorites = Favorite::where('user_id', $userId)
                             ->with('startup.founder')
                             ->latest()
                             ->get();

        return view('favorites.index', compact('favorites'));
    }
}
