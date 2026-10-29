<?php

use App\Http\Controllers\Sale\CartController;
use App\Http\Controllers\Sale\CheckoutController;
use App\Http\Controllers\Sale\OrderController;

// ===== ตะกร้า (ทุกคน) =====
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// ===== เช็คเอาต์ (สมาชิกเท่านั้น) =====
Route::middleware('auth:web')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// ===== จัดการออเดอร์ฝั่ง admin =====
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
});