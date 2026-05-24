<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Unit IV — Laravel Mail + Session
| Unit V  — Form Validation
| Unit VI — Database Query Builder
|--------------------------------------------------------------------------
*/

class PasswordResetController extends Controller
{
    // ── STEP 1: Show Forgot Password Form ────────────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ── STEP 2: Send Reset Email ──────────────────────────
    public function sendResetLink(Request $request)
    {
        // Unit V — Validate email
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        // Unit VI — Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->withInput();
        }

        // Generate a secure random token
        $token = Str::random(64);

        // Unit VI — Delete old token if exists, save new one
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Unit IV — Send email with reset link
        $resetUrl = url('/auth/reset-password/' . $token . '?email=' . urlencode($request->email));

        Mail::send('emails.reset-password', [
            'user'     => $user,
            'resetUrl' => $resetUrl,
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('🔐 Reset Your LaunchPad Market Password');
        });

        // Unit II — Redirect with flash message
        return back()
            ->with('success', '✅ Password reset link sent to ' . $request->email . '! Check your inbox.')
            ->with('reset_url', $resetUrl);
    }

    // ── STEP 3: Show Reset Password Form ─────────────────
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // ── STEP 4: Update Password ───────────────────────────
    public function resetPassword(Request $request)
    {
        // Unit V — Validate all fields
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Unit VI — Find the token record
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        // Check token exists and is valid
        if (!$record) {
            return back()->withErrors([
                'email' => 'Invalid or expired reset link. Please request a new one.',
            ]);
        }

        // Check token is not older than 60 minutes
        $tokenAge = Carbon::parse($record->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors([
                'email' => 'This reset link has expired. Please request a new one.',
            ]);
        }

        // Verify the token matches
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors([
                'email' => 'Invalid reset link. Please request a new one.',
            ]);
        }

        // Unit VI — Update user password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Unit II — Redirect to login with success message
        return redirect()
            ->route('auth.login')
            ->with('success', '🎉 Password reset successfully! Please log in with your new password.');
    }
}
