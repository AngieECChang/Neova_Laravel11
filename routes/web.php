<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HClistController;
use App\Http\Controllers\CaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () { return view('welcome'); });
Route::get('/',  [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'processLogin'])->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// 受保護的頁面
Route::middleware(['auth.session'])->group(function () {
  Route::get('/dashboard', function () {
      return view('dashboard');
  })->name('dashboard');
  Route::get('/hc-openlist', [HCListController::class, 'HC_Openlist'])->name('hc-openlist');
  Route::get('/hc-create', [HCListController::class, 'HC_Create'])->name('hc-create');
  Route::get('/hc-closelist', [HCListController::class, 'HC_Closelist'])->name('hc-closelist');
  Route::put('/update-case/{id}', [CaseController::class, 'case_update']);
  Route::put('/new-case', [CaseController::class, 'case_new']);
});


