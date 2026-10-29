<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::withCount('products')->paginate(30);

        return view('stock.tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('stock.tags.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $name = $request->validate(['name' => ['required', 'string', 'max:50']])['name'];

        Tag::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'สร้างแท็กเรียบร้อย');
    }

    public function edit(Tag $tag): View
    {
        return view('stock.tags.form', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $name = $request->validate(['name' => ['required', 'string', 'max:50']])['name'];

        $tag->update([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'บันทึกแท็กเรียบร้อย');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'ลบแท็กเรียบร้อย');
    }
}