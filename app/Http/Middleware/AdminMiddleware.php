<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('auth.login')
                             ->with('error', 'Please log in first.');
        }

        if (session('user_role') !== 'admin') {
            abort(403, 'Access denied. Admins only.');
        }

        return $next($request);
    }
}
