<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $newProducts = Product::published()
            ->where('is_new', true)
            ->with(['category', 'images'])
            ->latest()
            ->take(6)
            ->get();

        $featuredProducts = Product::published()
            ->where('is_featured', true)
            ->with(['category', 'images'])
            ->latest()
            ->take(6)
            ->get();

        return view('pages.home', compact('newProducts', 'featuredProducts'));
    }
}