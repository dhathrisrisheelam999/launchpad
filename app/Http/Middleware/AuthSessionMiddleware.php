<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Unit III — Controller Middleware
| Unit IV  — Session Data Access
|--------------------------------------------------------------------------
| Protects routes that require a logged-in user.
| If session('user_id') is missing, redirect to login.
*/

class AuthSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Unit IV — Check if user_id exists in session
        if (!session()->has('user_id')) {
            // Unit II — Redirect to named route
            return redirect()
                ->route('auth.login')
                ->with('error', 'Please log in to access that page.');
        }

        return $next($request);
    }
}
