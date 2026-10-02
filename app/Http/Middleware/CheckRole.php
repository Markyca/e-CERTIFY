<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // 1. Add this import at the top

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Double-check that someone is actually logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Check if the user's role matches any of the allowed roles for this route
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request); // Access granted, proceed!
        }

        // 3. If they don't have the right role, lock them out immediately
        abort(403, 'UNAUTHORIZED: You do not have permission to access this page.');
    }
}