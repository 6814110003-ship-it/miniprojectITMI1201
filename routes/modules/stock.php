<?php

use App\Http\Controllers\Stock\CategoryController;
use App\Http\Controllers\Stock\ProductController;
use App\Http\Controllers\Stock\ProductReportController;
use App\Http\Controllers\Stock\ProductReviewController;
use App\Http\Controllers\Stock\TagController;

Route::group([], function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/products/{product:slug}/report', [ProductReportController::class, 'store'])->name('products.report');
    Route::post('/products/{product:slug}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');

    Route::get('/product-reports', [ProductReportController::class, 'index'])->name('reports.index');
    Route::patch('/product-reports/{report}', [ProductReportController::class, 'update'])->name('reports.update');

    Route::get('/reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}', [ProductReviewController::class, 'update'])->name('reviews.update');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});