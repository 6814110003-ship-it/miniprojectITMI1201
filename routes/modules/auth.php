<?php

use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\Auth\PasswordResetController;

// ===== ฝั่งพนักงาน (admin) =====
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', [EmployeeAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [EmployeeAuthController::class, 'login'])->name('admin.login.submit');
});

Route::post('/admin/logout', [EmployeeAuthController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');

// ===== ฝั่งสมาชิก (ลูกค้า) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [MemberAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [MemberAuthController::class, 'showRegister'])->name('register');
    Route::get('/register/member', [MemberAuthController::class, 'showRegister'])->name('register.member');
    Route::post('/register/member', [MemberAuthController::class, 'register'])->name('register.member.submit');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.store');
});

Route::post('/logout', [MemberAuthController::class, 'logout'])->name('logout')->middleware('auth:web');