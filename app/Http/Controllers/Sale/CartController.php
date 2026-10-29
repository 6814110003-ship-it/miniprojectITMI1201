<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly InventoryService $inventory,
    ) {
    }

    public function index(): View
    {
        $items = $this->cart->items();

        return view('sale.cart', [
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
            'depositTotal' => $this->cart->depositTotal(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::published()->findOrFail($data['product_id']);
        $qty = $data['qty'] ?? 1;

        if ($product->status === Product::STATUS_SOLD_OUT) {
            return back()->with('error', "สินค้า {$product->name} หมด / ปิดรับจองแล้ว");
        }

        if ($product->status !== Product::STATUS_PREORDER && $qty > $product->stock) {
            return back()->with('error', "สินค้า {$product->name} มีสต็อกเพียง {$product->stock} ชิ้น");
        }

        // รวมจำนวนในตะกร้า + ที่สั่งเพิ่ม ต้องไม่เกินสต็อก
        if ($product->status !== Product::STATUS_PREORDER) {
            $cart = session()->get('pos_cart', []);
            $inCart = $cart[$product->id] ?? 0;
            $total = $inCart + $qty;

            if ($total > $product->stock) {
                $canAdd = max(0, $product->stock - $inCart);

                if ($canAdd === 0) {
                    return back()->with('error', "สินค้า {$product->name} มีในตะกร้าเต็มจำนวนสต็อกแล้ว");
                }

                return back()->with('error', "สามารถเพิ่มได้อีกเพียง {$canAdd} ชิ้น (มีสต็อก {$product->stock} ชิ้น)");
            }
        }

        $this->cart->add($product->id, $qty);

        return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าเข้าตะกร้าแล้ว');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $qty = $request->validate(['qty' => ['required', 'integer', 'min:1', 'max:99']])['qty'];
        $this->cart->update($id, $qty);

        return back()->with('success', 'อัปเดตตะกร้าเรียบร้อย');
    }

    public function remove(int $id): RedirectResponse
    {
        $this->cart->remove($id);

        return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
    }
}