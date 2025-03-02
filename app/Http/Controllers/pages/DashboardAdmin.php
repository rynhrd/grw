<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardAdmin extends Controller
{
  public function SuperAdminPage()
  {
    return view('content.pages.pages-superadmin-dash');
  }

  public function AdminPage()
  {
    return view('content.pages.pages-admin-dash');
  }
}