<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // If user doesn't have required role, redirect based on their actual role
        if ($user->isAdmin()) {
            return redirect()->route('dashboard.admin');
        } elseif ($user->isKitchenManager()) {
            return redirect()->route('dashboard.kitchen');
        } elseif ($user->isCashier()) {
            return redirect()->route('dashboard.cashier');
        }

        // If no valid role, redirect to login
        return redirect()->route('login')->with('error', 'You do not have permission to access this page.');
    }
}
