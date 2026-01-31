<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Check if user is logged in
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Check if the user's role is in the allowed list ($roles array)
        // This fixes the issue where only the first role (admin) was being checked.
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 3. If role not found, show 403 error
        return abort(403, 'UNAUTHORIZED ACTION.');
    }
}