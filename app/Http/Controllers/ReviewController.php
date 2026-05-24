<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Startup;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Store a new review
    public function store(Request $request, Startup $startup)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Please select a star rating.',
            'rating.min'      => 'Rating must be at least 1 star.',
            'rating.max'      => 'Rating cannot exceed 5 stars.',
        ]);

        // Check if user already reviewed this startup
        $existing = Review::where('user_id', session('user_id'))
                          ->where('startup_id', $startup->id)
                          ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this startup.');
        }

        Review::create([
            'user_id'    => session('user_id'),
            'startup_id' => $startup->id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return back()->with('success', '⭐ Review submitted successfully!');
    }

    // Delete a review (own review only)
    public function destroy(Review $review)
    {
        if ($review->user_id !== session('user_id')) {
            return back()->with('error', 'You can only delete your own reviews.');
        }

        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
