<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    // ===================== สมาชิกแจ้งชำระ =====================

    public function create(Request $request): View|RedirectResponse
    {
        $member = Auth::guard('web')->user();

        $orders = Order::with('items')
            ->where('member_id', $member->id)
            ->whereIn('status', [
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_PAID,
                Order::STATUS_PROCESSING,
                Order::STATUS_READY_TO_SHIP,
            ])
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Order $order) => $order->dueAmount() > 0)
            ->values();

        $selectedOrder = null;
        $orderId = $request->integer('order_id');

        if ($orderId) {
            $selectedOrder = $orders->first(fn (Order $order) => $order->id === $orderId);
        }

        if (! $selectedOrder && $orders->isNotEmpty()) {
            $selectedOrder = $orders->first();
        }

        return view('payment.create', compact('orders', 'selectedOrder'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'bank' => ['required', 'string', 'max:50'],
            'transferred_at' => ['required', 'date', 'before_or_equal:now'],
            'slip' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $memberId = Auth::guard('web')->id();
        $order = Order::findOrFail($validated['order_id']);

        if ($order->member_id !== $memberId) {
            abort(403);
        }

        $payableStatuses = [
            Order::STATUS_PENDING_PAYMENT,
            Order::STATUS_PAID,
            Order::STATUS_PROCESSING,
            Order::STATUS_READY_TO_SHIP,
        ];

        if (! in_array($order->status, $payableStatuses, true)) {
            return back()->withInput()->with(
                'error',
                'ออเดอร์นี้ไม่สามารถแจ้งชำระเงินได้ (สถานะ: '.$order->statusLabel().')'
            );
        }

        $due = $order->dueAmount();

        if ($due <= 0) {
            return back()->withInput()->with('error', 'ออเดอร์นี้ชำระครบแล้ว');
        }

        if ($validated['amount'] > $due) {
            return back()->withInput()->with(
                'error',
                'จำนวนเงินที่แจ้งชำระเกินยอดคงเหลือ (ยอดคงเหลือ ฿'.number_format($due).')'
            );
        }

        if ($order->payFirstPayment() && $validated['amount'] < $order->initialPaymentAmount()) {
            return back()->withInput()->with(
                'error',
                'ยอดที่ต้องชำระตอนนี้ไม่น้อยกว่า ฿'.number_format($order->initialPaymentAmount())
            );
        }

        $slipPath = $request->file('slip')->store('slips', 'local');

        Payment::create([
            'order_id' => $order->id,
            'member_id' => $memberId,
            'type' => $order->paidAmount() === 0 ? Payment::TYPE_DEPOSIT : Payment::TYPE_BALANCE,
            'method' => 'bank_transfer',
            'bank' => $validated['bank'],
            'transferred_at' => $validated['transferred_at'],
            'amount' => $validated['amount'],
            'slip_path' => $slipPath,
            'note' => $validated['note'] ?? null,
            'status' => Payment::STATUS_PENDING,
        ]);

        return redirect()->route('member.orders.show', $order)->with('success', 'แจ้งชำระเงินเรียบร้อย รอพนักงานตรวจสอบสลิป');
    }

    public function memberSlip(Payment $payment): StreamedResponse
    {
        abort_unless($payment->member_id === Auth::guard('web')->id(), 403);

        return $this->slipResponse($payment);
    }

    public function adminSlip(Payment $payment): StreamedResponse
    {
        return $this->slipResponse($payment);
    }

    private function slipResponse(Payment $payment): StreamedResponse
    {
        abort_unless(
            $payment->slip_path && Storage::disk('local')->exists($payment->slip_path),
            404
        );

        return Storage::disk('local')->response($payment->slip_path);
    }

    // ===================== Admin ตรวจสอบ =====================

    public function index(Request $request): View
    {
        $payments = Payment::with(['order', 'member'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->get('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('payment.admin.index', compact('payments'));
    }

    public function verify(Payment $payment): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'สถานะชำระเงินนี้ถูกจัดการไปแล้ว');
        }

        if ($payment->order?->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'ออเดอร์นี้ถูกยกเลิกแล้ว ไม่สามารถอนุมัติการชำระได้');
        }

        $payment->update([
            'status' => Payment::STATUS_VERIFIED,
            'employee_id' => Auth::guard('admin')->id(),
            'verified_at' => now(),
        ]);

        $order = $payment->order;
        $paid = $order->paidAmount();
        $paidThreshold = $order->isPreorder()
            ? $order->initialPaymentAmount()
            : $order->total;

        if ($paid >= $paidThreshold && $order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->update(['status' => Order::STATUS_PAID]);
        }

        return back()->with('success', 'อนุมัติการชำระแล้ว');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return back()->with('error', 'สถานะชำระเงินนี้ถูกจัดการไปแล้ว');
        }

        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'employee_id' => Auth::guard('admin')->id(),
        ]);

        return back()->with('success', 'ปฏิเสธการชำระแล้ว');
    }
}