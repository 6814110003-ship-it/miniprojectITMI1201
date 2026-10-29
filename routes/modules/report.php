<?php

use App\Http\Controllers\Report\ReportController;

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
});