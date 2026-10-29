<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReport;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::published()
            ->with(['category', 'images'])
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->string('category')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->search($request->get('q'));

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::active()->whereIn('name', ['Cardfight!! Vanguard', 'Figure'])->get();

        return view('stock.index', compact('products', 'categories'));
    }

    public function search(Request $request): View
    {
        return $this->index($request);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $related = Product::published()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['category', 'images'])
            ->latest()
            ->take(4)
            ->get();

        $reviews = $product->reviews()
            ->approved()
            ->with('member')
            ->latest()
            ->get();

        $reviewAvg = $reviews->avg('rating');
        $reviewCount = $reviews->count();

        $member = Auth::guard('web')->user();
        $myReview = $member
            ? $product->reviews()->where('member_id', $member->id)->first()
            : null;

        $canReview = false;
        if ($member && ! $myReview) {
            $canReview = $member->orders()
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        return view('stock.show', compact(
            'product', 'related', 'reviews', 'reviewAvg', 'reviewCount', 'myReview', 'canReview'
        ));
    }

    // ===================== Admin =====================

    public function adminIndex(Request $request): View
    {
        $types = Product::query()
            ->whereNotNull('code')
            ->pluck('code')
            ->map(fn ($code) => (string) preg_replace('/[^A-Za-z].*$/', '', (string) $code))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $products = Product::with(['category'])
            ->withCount(['reports as pending_reports_count' => fn ($q) => $q->where('status', ProductReport::STATUS_PENDING)])
            ->when($request->filled('type'), fn ($q) => $q->where('code', 'like', $request->string('type').'%'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$request->get('q')}%")
                ->orWhere('code', 'like', "%{$request->get('q')}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('stock.admin.index', compact('products', 'types'));
    }

    public function create(): View
    {
        $categories = Category::active()->get();
        $tags = Tag::all();

        return view('stock.admin.form', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->normalizeBooleans($data);
        $data['slug'] = $this->uniqueSlug($data['name']);

        $product = Product::create($data);

        $tagNames = array_merge($request->input('tags', []), $this->splitTags($request->input('new_tags')));
        $this->saveTags($product, $tagNames);
        $this->saveImages($product, $request->file('images', []));

        return redirect()->route('admin.products.index')->with('success', 'สร้างสินค้าเรียบร้อย');
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->get();
        $tags = Tag::all();

        return view('stock.admin.form', compact('product', 'categories', 'tags'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->normalizeBooleans($data);
        $data['slug'] = $this->uniqueSlug($data['name'], $product->id);

        $product->update($data);

        $tagNames = array_merge($request->input('tags', []), $this->splitTags($request->input('new_tags')));
        $this->saveTags($product, $tagNames);
        $this->saveImages($product, $request->file('images', []));

        return redirect()->route('admin.products.index')->with('success', 'บันทึกสินค้าเรียบร้อย');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'ลบสินค้าเรียบร้อย');
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        $productId = $image->product_id;
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->route('admin.products.edit', $productId)->with('success', 'ลบรูปเรียบร้อย');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'deposit' => ['required', 'integer', 'min:0', 'lte:price'],
            'status' => ['required', 'in:preorder,instock,soldout'],
            'size' => ['nullable', 'string', 'max:50'],
            'release_date' => ['nullable', 'date'],
            'preorder_deadline' => ['nullable', 'date'],
            'is_new' => ['nullable'],
            'is_featured' => ['nullable'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);

        if ($slug === '') {
            $slug = 'product-'.Str::lower(Str::random(8));
        }

        $base = $slug;
        $i = 2;

        while (Product::where('slug', $slug)->where('id', '!=', $ignoreId)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function normalizeBooleans(array $data): array
    {
        foreach (['is_new', 'is_featured', 'is_active'] as $field) {
            $data[$field] = ! empty($data[$field]);
        }

        return $data;
    }

    private function splitTags(?string $input): array
    {
        if (! $input) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $input)));
    }

    private function saveTags(Product $product, array $names): void
    {
        $ids = [];

        foreach ($names as $name) {
            if (! trim((string) $name)) {
                continue;
            }
            $tag = Tag::firstOrCreate(
                ['name' => trim($name)],
                ['slug' => Str::slug($name)]
            );
            $ids[] = $tag->id;
        }

        $product->tags()->sync($ids);
    }

    private function saveImages(Product $product, array $files): void
    {
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }
            $path = $file->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'sort_order' => $product->images()->count() + 1,
            ]);
        }
    }
}