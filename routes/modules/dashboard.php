<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
});