<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Auth::guard('web')->user()->orders()
            ->with(['items', 'payments'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->get('status')));

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('member.orders', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_if($order->member_id !== Auth::guard('web')->id(), 403);

        $order->load(['items.product', 'payments']);

        return view('member.order-show', compact('order'));
    }

    public function cancel(Order $order, InventoryService $inventory): RedirectResponse
    {
        abort_if($order->member_id !== Auth::guard('web')->id(), 403);

        $cancellable = [
            Order::STATUS_PENDING_PAYMENT,
            Order::STATUS_PAID,
            Order::STATUS_PROCESSING,
        ];

        if (! in_array($order->status, $cancellable, true)) {
            return back()->with('error', 'ออเดอร์นี้ไม่สามารถยกเลิกได้ในสถานะปัจจุบัน กรุณาติดต่อร้าน');
        }

        $cancelled = DB::transaction(function () use ($order, $inventory, $cancellable) {
            $updated = Order::whereKey($order->id)
                ->whereIn('status', $cancellable)
                ->update(['status' => Order::STATUS_CANCELLED]);

            if ($updated === 0) {
                return false;
            }

            // คืนสต็อกให้สินค้า (ยกเว้นพรีออเดอร์) และเปิดขายกลับเป็น instock ถ้ามีของ
            $inventory->releaseForOrder($order);

            return true;
        });

        if (! $cancelled) {
            return back()->with('error', 'ไม่สามารถยกเลิกได้ เนื่องจากสถานะออเดอร์เปลี่ยนไปแล้ว');
        }

        $message = 'ยกเลิกออเดอร์เรียบร้อยแล้ว';
        if ($order->fresh()->paidAmount() > 0) {
            $message .= ' — คุณชำระเงินแล้ว กรุณาติดต่อร้านเพื่อขอคืนเงิน';
        }

        return back()->with('success', $message);
    }
}
