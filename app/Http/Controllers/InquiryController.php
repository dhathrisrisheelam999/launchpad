<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InquiryMail;

/*
|--------------------------------------------------------------------------
| Unit IV — Sending Emails, Sessions
| Unit V  — Form Validation
| Unit VI — Eloquent Create
|--------------------------------------------------------------------------
*/

class InquiryController extends Controller
{
    // ── POST /inquiry/{startup} ───────────────────────────────
    public function store(Request $request, Startup $startup)
    {
        // Unit V — Validate inquiry form
        $validated = $request->validate([
            'investor_name'  => 'required|string|max:100',
            'organisation'   => 'required|string|max:150',
            'interest_type'  => 'required|in:investment,acquisition,acqui-hire,partnership',
            'message'        => 'required|string|min:20|max:2000',
        ], [
            'message.min' => 'Please write at least 20 characters in your message.',
        ]);

        // Attach the startup and investor session user
        $validated['startup_id'] = $startup->id;
        $validated['user_id']    = session('user_id');
        $validated['status']     = 'unread';

        // Unit VI — Save inquiry to database
        $inquiry = Inquiry::create($validated);

        // Unit IV — Send email to the startup founder
        $founder = $startup->founder;
        if ($founder) {
            Mail::to($founder->email)->send(new InquiryMail($inquiry, $startup));
        }

        // Unit IV — Store a confirmation in session
        session()->flash('inquiry_sent', true);

        // Unit II — Redirect back to startup page with flash message
        return redirect()
            ->route('startups.show', $startup)
            ->with('success', '✅ Your message has been sent to the founder!');
    }
}
