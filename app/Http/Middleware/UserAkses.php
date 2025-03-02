<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserAkses
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   * @param  string  $role
   */
  public function handle(Request $request, Closure $next, string $role): Response
  {
    // Check if user is authenticated
    if (!Auth::check()) {
      return redirect()->route('auth-login-basic')->with('error', 'Please log in first.');
    }

    // Check if user has the required role
    if (Auth::user()->role !== $role) {
      return redirect('/')->with('error', 'You do not have permission to access this page.');
    }

    return $next($request);
  }
}