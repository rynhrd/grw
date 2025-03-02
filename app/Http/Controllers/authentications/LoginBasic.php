<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  public function login(Request $request)
  {
    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    Log::info('Credential', $credentials);

    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();

      // Redirect berdasarkan role
      if (Auth::user()->role == 'superadmin') {
        Log::info('Credential', $credentials);
        return redirect()->route('superadmin.dashboard');
      } elseif (Auth::user()->role == 'admin') {
        return redirect()->route('admin.dashboard');
      } else {
        return redirect()->route('pages-home'); // Redirect default
      }
    }

    return back()->withErrors([
      'email' => 'Email atau password salah.',
    ])->onlyInput('email');
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('auth-login-basic');
  }
}