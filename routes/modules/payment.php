<?php

use App\Http\Controllers\Payment\PaymentController;

// ===== สมาชิก: แจ้งชำระเงิน =====
Route::middleware('auth:web')->prefix('member')->name('member.')->group(function () {
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/slip', [PaymentController::class, 'memberSlip'])->name('payments.slip');
});

// ===== Admin: ตรวจสอบการชำระเงิน =====
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payments/{payment}/slip', [PaymentController::class, 'adminSlip'])->name('payments.slip');
});