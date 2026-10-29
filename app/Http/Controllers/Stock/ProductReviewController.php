<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    /** สมาชิกเขียนรีวิวสินค้าที่เคยสั่งซื้อ */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $member = Auth::guard('web')->user();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if (ProductReview::where('product_id', $product->id)->where('member_id', $member->id)->exists()) {
            return back()->with('error', 'คุณได้รีวิวสินค้านี้แล้ว');
        }

        $order = $member->orders()
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();

        if (! $order) {
            return back()->with('error', 'คุณสามารถรีวิวได้เฉพาะสินค้าที่เคยสั่งซื้อเท่านั้น');
        }

        ProductReview::create([
            'product_id' => $product->id,
            'member_id' => $member->id,
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'status' => ProductReview::STATUS_PENDING,
        ]);

        return back()->with('success', 'ส่งรีวิวเรียบร้อย ทางร้านจะตรวจสอบก่อนเผยแพร่');
    }

    // ===================== Admin =====================

    public function index(Request $request): View
    {
        $reviews = ProductReview::with(['product', 'member'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->integer('rating')))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($query) use ($request) {
                $term = '%'.$request->get('q').'%';
                $query->whereHas('product', fn ($p) => $p->where('name', 'like', $term)->orWhere('code', 'like', $term))
                    ->orWhereHas('member', fn ($m) => $m->where('name', 'like', $term));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock.admin.reviews', compact('reviews'));
    }

    public function update(Request $request, ProductReview $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(ProductReview::STATUS_LABELS))],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update($data);

        return back()->with('success', 'อัปเดตรีวิวเรียบร้อยแล้ว');
    }
}
