<?php

use App\Http\Controllers\Dashboard\MemberDashboardController;
use App\Http\Controllers\Member\AdminMemberController;
use App\Http\Controllers\Member\MemberOrderController;
use App\Http\Controllers\Member\MemberProfileController;

Route::middleware('auth:web')->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [MemberProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::get('/orders', [MemberOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [MemberOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [MemberOrderController::class, 'cancel'])->name('orders.cancel');
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [AdminMemberController::class, 'show'])->name('members.show');
});