<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly InventoryService $inventory,
    ) {
    }

    private const SHIPPING_FEES = ['ems' => 120, 'kerry' => 100, 'flash' => 120, 'jnt' => 100];

    private function shippingFee(string $method): int
    {
        $fee = self::SHIPPING_FEES[$method] ?? 100;

        if ($this->cartHasCard() && ! $this->cartHasFigure()) {
            $fee -= 50;
        }

        return $fee;
    }

    private function cartHasCard(): bool
    {
        foreach ($this->cart->items() as $item) {
            if ($item['product']->category?->name === 'Cardfight!! Vanguard') {
                return true;
            }
        }

        return false;
    }

    private function cartHasFigure(): bool
    {
        foreach ($this->cart->items() as $item) {
            if ($item['product']->category?->name === 'Figure') {
                return true;
            }
        }

        return false;
    }

    public function index(): View|RedirectResponse
    {
        $items = $this->cart->items();

        if (empty($items)) {
            return redirect()->route('products.index');
        }

        $member = Auth::guard('web')->user();
        $subtotal = $this->cart->subtotal();
        $depositTotal = $this->cart->depositTotal();
        $initialPayment = $this->cart->initialPaymentTotal();
        $shippingFees = $this->shippingFeeMap();
        $shippingFee = $shippingFees['kerry'];
        $total = $subtotal + $shippingFee;
        $balanceDueDate = $this->balanceDueDate($items);

        return view('sale.checkout', compact('items', 'subtotal', 'depositTotal', 'initialPayment', 'shippingFee', 'shippingFees', 'total', 'member', 'balanceDueDate'));
    }

    private function balanceDueDate(array $items): ?\Illuminate\Support\Carbon
    {
        return collect($items)
            ->filter(fn ($item) => $item['product']->status === Product::STATUS_PREORDER && $item['product']->release_date)
            ->map(fn ($item) => $item['product']->release_date->copy())
            ->max();
    }

    private function shippingFeeMap(): array
    {
        $fees = [];

        foreach (array_keys(self::SHIPPING_FEES) as $method) {
            $fees[$method] = $this->shippingFee($method);
        }

        return $fees;
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_method' => ['required', 'in:ems,kerry,flash,jnt'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $items = $this->cart->items();

        if (empty($items)) {
            return redirect()->route('products.index');
        }

        // ตรวจสต็อกอีกครั้งก่อนบันทึกออเดอร์ กันสั่งเกินจำนวนที่มี
        foreach ($items as $item) {
            $product = $item['product'];

            if ($product->status === Product::STATUS_SOLD_OUT) {
                return back()->with('error', "สินค้า {$product->name} หมด / ปิดรับจองแล้ว");
            }

            if ($product->status !== Product::STATUS_PREORDER && $item['qty'] > $product->stock) {
                return back()->with('error', "สินค้า {$product->name} มีสต็อกเพียง {$product->stock} ชิ้น");
            }
        }

        $shippingFee = $this->shippingFee($data['shipping_method']);
        $subtotal = $this->cart->subtotal();
        $depositTotal = $this->cart->depositTotal();
        $initialPayment = $this->cart->initialPaymentTotal();
        $balanceDueDate = $this->balanceDueDate($items);

        $order = DB::transaction(function () use ($data, $items, $shippingFee, $subtotal, $depositTotal, $balanceDueDate) {
            $order = Order::create([
                'order_no' => $this->generateOrderNo(),
                'member_id' => Auth::guard('web')->id(),
                'status' => Order::STATUS_PENDING_PAYMENT,
                ...$data,
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'deposit_total' => $depositTotal,
                'balance_due_date' => $balanceDueDate?->toDateString(),
                'total' => $subtotal + $shippingFee,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'price' => $product->price,
                    'deposit' => $product->deposit,
                    'qty' => $item['qty'],
                ]);
            }

            // ตัดสต็อกสินค้า ถ้าหมดจะเปลี่ยนสถานะเป็น "หมด / ปิดรับจอง" อัตโนมัติ
            $this->inventory->reserveForOrder($order);

            return $order;
        });

        $this->cart->clear();

        return redirect()->route('member.orders.show', $order)->with('success', 'สร้างคำสั่งซื้อเรียบร้อย กรุณาชำระเงินมัดจำ');
    }

    private function generateOrderNo(): string
    {
        return 'PSF'.date('Ymd').strtoupper(uniqid());
    }
}