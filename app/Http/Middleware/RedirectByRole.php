<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectByRole
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next)
  {
    if (!Auth::check()) {
      return redirect()->route('auth-login-basic')->with('error', 'Silakan login terlebih dahulu.');
    }

    $role = Auth::user()->role;

    // Redirect berdasarkan role
    return match ($role) {
      'superadmin' => redirect()->route('superadmin.dashboard'),
      'admin' => redirect()->route('admin.dashboard'),
      default => redirect('/'),
    };
  }
}