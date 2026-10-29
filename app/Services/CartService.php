<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    private const SESSION_KEY = 'pos_cart';

    public function items(): array
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $items = [];

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product && $product->is_active) {
                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                ];
            }
        }

        return $items;
    }

    public function add(int $productId, int $qty = 1): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $cart[$productId] = ($cart[$productId] ?? 0) + $qty;
        session()->put(self::SESSION_KEY, $cart);
    }

    public function update(int $productId, int $qty): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $qty;
        }
        session()->put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        unset($cart[$productId]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        $cart = session()->get(self::SESSION_KEY, []);

        return array_sum($cart);
    }

    public function subtotal(): int
    {
        $total = 0;

        foreach ($this->items() as $item) {
            $total += (int) $item['product']->price * $item['qty'];
        }

        return $total;
    }

    public function depositTotal(): int
    {
        $total = 0;

        foreach ($this->items() as $item) {
            $total += (int) $item['product']->deposit * $item['qty'];
        }

        return $total;
    }

    public function initialPaymentTotal(): int
    {
        $total = 0;

        foreach ($this->items() as $item) {
            $product = $item['product'];
            $unitAmount = $product->status === Product::STATUS_PREORDER
                ? (int) $product->deposit
                : (int) $product->price;

            $total += $unitAmount * $item['qty'];
        }

        return $total;
    }
}