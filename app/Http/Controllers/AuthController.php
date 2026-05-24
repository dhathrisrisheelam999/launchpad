<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

/*
|--------------------------------------------------------------------------
| Unit IV — Sessions, Email
| Unit V  — Form Validation
|--------------------------------------------------------------------------
*/

class AuthController extends Controller
{
    // ── Show Register Form ────────────────────────────────────
    public function showRegister()
    {
        return view('auth.register');
    }

    // ── Register: POST /auth/register ────────────────────────
    public function register(Request $request)
    {
        // Unit V — Validation with custom messages
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:founder,investor,acquirer,advisor',
            'password' => 'required|string|min:8|confirmed', // confirmed = password_confirmation field
        ], [
            'email.unique'         => 'This email is already registered. Please log in.',
            'password.confirmed'   => 'Passwords do not match.',
            'password.min'         => 'Password must be at least 8 characters.',
        ]);

        // Hash the password — NEVER store plain text
        $validated['password'] = Hash::make($validated['password']);

        // Unit VI — Save new user to database
        $user = User::create($validated);

        // Unit IV — Store user in session
        session([
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
        ]);

        // Unit IV — Send welcome email via Laravel Mail
        //Mail::to($user->email)->send(new WelcomeMail($user));

        // Unit II — Redirect with flash success message
        return redirect()
            ->route('dashboard.index')
            ->with('success', "Welcome, {$user->name}! Your account has been created.");
    }

    // ── Show Login Form ───────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    // ── Login: POST /auth/login ───────────────────────────────
    public function login(Request $request)
    {
        // Unit V — Validate login inputs
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Unit VI — Find user by email (Query Builder)
        $user = User::where('email', $request->email)->first();

        // Check password hash
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Unit V — Return back with error (repopulate form)
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        // Unit IV — Store authenticated user in session
        session([
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
        ]);

        // Unit IV — Regenerate session ID to prevent fixation
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.index')
                ->with('success', "Welcome back, {$user->name}!");
        }

        return redirect()
            ->route('dashboard.index')
            ->with('success', "Welcome back, {$user->name}!");
    }

    // ── Logout: POST /auth/logout ─────────────────────────────
    public function logout(Request $request)
    {
        // Unit IV — Delete all session data
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
