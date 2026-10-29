<?php

use App\Http\Controllers\Pages\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/how-to-order', 'pages.howtoorder')->name('pages.howtoorder');
Route::view('/contact', 'pages.contact')->name('pages.contact');