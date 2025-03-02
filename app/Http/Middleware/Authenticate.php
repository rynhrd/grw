<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Ensure user is authenticated
    if (!Auth::check()) {
      return redirect()->route('auth-login-basic')->with('error', 'Please log in first.');
    }

    // Check if the user has a valid role
    if (!in_array(Auth::user()->role, ['superadmin', 'admin'])) {
      return response()->json(['error' => 'Oops! You do not have permission to access.'], 403);
    }

    return $next($request);
  }
}