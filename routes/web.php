<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('adminpages.index');
// });

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


/////////////////////////////////////////user////////////////////////////////////////

Route::get('/user/dashboard', function () {
    return view('adminpages.index');
})->name('user.dashboard');

Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
Route::get('/create/lead', [UserController::class, 'createLead'])->name('user.createLead');
Route::post('/store', [UserController::class, 'storeLead'])->name('store.user.lead');
Route::get('/pending/lead', [UserController::class, 'pendingLead'])->name('user.pendingLead');
Route::get('/policy/copy', [UserController::class, 'policyCopy'])->name('user.policyCopy');
Route::get('/wallet', [UserController::class, 'wallet'])->name('user.wallet');


