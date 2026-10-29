<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * จองสต็อกสินค้าเมื่อสร้างออเดอร์
     * ถ้าสต็อกหมด สถานะสินค้าจะเปลี่ยนเป็น "หมด / ปิดรับจอง" อัตโนมัติ
     */
    public function reserveForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->with('product')->get() as $item) {
                if ($item->product) {
                    $this->reserve($item->product, (int) $item->qty);
                }
            }
        });
    }

    /**
     * คืนสต็อกสินค้าเมื่อออเดอร์ถูกยกเลิก
     */
    public function releaseForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items()->with('product')->get() as $item) {
                if ($item->product) {
                    $this->release($item->product, (int) $item->qty);
                }
            }
        });
    }

    private function reserve(Product $product, int $qty): void
    {
        // สินค้าพรี-ออเดอร์ ยังไม่มีของเข้าร้าน จึงไม่ตัดสต็อก
        if ($product->status === Product::STATUS_PREORDER) {
            return;
        }

        $stock = max(0, $product->stock - $qty);
        $product->stock = $stock;

        if ($stock === 0) {
            $product->status = Product::STATUS_SOLD_OUT;
        }

        $product->save();
    }

    private function release(Product $product, int $qty): void
    {
        if ($product->status === Product::STATUS_PREORDER) {
            return;
        }

        $stock = $product->stock + $qty;
        $product->stock = $stock;

        // คืนสต็อกแล้วมีของให้ขายต่อได้ กลับสถานะเป็น "มีของ / เข้าร้าน"
        if ($product->status === Product::STATUS_SOLD_OUT && $stock > 0) {
            $product->status = Product::STATUS_INSTOCK;
        }

        $product->save();
    }
}
