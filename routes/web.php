<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\pages\DashboardAdmin;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\admin\AddReceipt;

use App\Http\Controllers\JsonCrudController;

Route::get('/', function () {
  return redirect()->route('auth-login-basic');
})->middleware('redirectByRole');

Route::middleware(['guest'])->group(function () {
  Route::get('/auth/login', [LoginBasic::class, 'index'])->name('auth-login-basic');
  Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
  Route::post('/login', [LoginBasic::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
  Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
  Route::get('/superadmin', [DashboardAdmin::class, 'SuperAdminPage'])->name('superadmin.dashboard')->middleware('userakases:superadmin');
  Route::get('/addreceipt', [AddReceipt::class, 'index'])->name('addreceipt')->middleware('userakases:superadmin');
  Route::get('/get-products', [AddReceipt::class, 'getProducts'])->name('get-products');
  Route::get('/get-tipemotor', [AddReceipt::class, 'getTipe'])->name('get-tipemotor');
  Route::get('/get-ccmotor', [AddReceipt::class, 'ccMotor'])->name('get-ccmotor');
  Route::get('/get-allreceipt', [AddReceipt::class, 'getAllReceipt'])->name('get-allreceipt');
  Route::post('/post-addreceipt', [AddReceipt::class, 'addReceiptTbl'])->name('post.addreceipt');


  // save with json
  Route::get('/json', [JsonCrudController::class, 'index'])->name('get.json.swhow');
  Route::post('/json', [JsonCrudController::class, 'store'])->name('post.json.addnew');
  Route::get('/json/{id}', [JsonCrudController::class, 'show'])->name('get.json.byid');
  Route::put('/json/{id}', [JsonCrudController::class, 'update'])->name('put.json.updateid');
  Route::delete('/json/{id}', [JsonCrudController::class, 'destroy'])->name('detele.json.byid');
  // save with json


  Route::get('/admin', [DashboardAdmin::class, 'AdminPage'])->name('admin.dashboard')->middleware('userakases:admin');

  Route::post('/logout', [LoginBasic::class, 'logout'])->middleware('auth')->name('logout');
});