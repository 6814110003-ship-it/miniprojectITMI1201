<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly InventoryService $inventory)
    {
    }

    public function index(Request $request): View
    {
        $query = Order::with(['member', 'items'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('balance'), function ($q) use ($request) {
                if ($request->get('balance') === 'due') {
                    $q->whereNotNull('balance_due_date')
                        ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP])
                        ->whereDate('balance_due_date', '<=', today());
                } elseif ($request->get('balance') === 'upcoming') {
                    $q->whereNotNull('balance_due_date')
                        ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_READY_TO_SHIP])
                        ->whereDate('balance_due_date', '>', today());
                } elseif ($request->get('balance') === 'preorder') {
                    $q->where('deposit_total', '>', 0);
                }
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->get('q').'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('order_no', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhere('customer_phone', 'like', $term)
                        ->orWhereHas('member', function ($member) use ($term) {
                            $member->where('name', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            });

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('sale.admin.orders', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'payments', 'member']);

        return view('sale.admin.order-show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUS_LABELS))],
        ]);

        $oldStatus = $order->status;
        $status = $data['status'];

        $order->update(['status' => $status]);

        // ถ้าออเดอร์ถูกยกเลิก ให้คืนสต็อกสินค้า
        if ($status === Order::STATUS_CANCELLED && $oldStatus !== Order::STATUS_CANCELLED) {
            $this->inventory->releaseForOrder($order);
        }

        return back()->with('success', 'อัปเดตสถานะออเดอร์เป็น "'.$order->statusLabel().'" แล้ว');
    }
}