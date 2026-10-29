@extends('layouts.app')

@section('title', 'ออเดอร์ '.$order->order_no)
@section('content')

    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('member.orders') }}" class="hover:text-sky-700">ออเดอร์ของฉัน</a> ›
        <span class="text-gray-700">{{ $order->order_no }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold">ข้อมูลออเดอร์</h2>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-full
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700') }}">
                        {{ $order->statusLabel() }}
                    </span>
                </div>
                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">เลขออเดอร์</dt><dd class="font-medium">{{ $order->order_no }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">วันที่สั่ง</dt><dd>{{ $order->created_at->format('d M Y H:i') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">ผู้รับ</dt><dd>{{ $order->customer_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">เบอร์โทร</dt><dd>{{ $order->customer_phone }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">ช่องทางจัดส่ง</dt><dd>{{ strtoupper($order->shipping_method) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">ค่าส่ง</dt><dd>฿{{ number_format($order->shipping_fee) }}</dd></div>
                </dl>
                <p class="text-sm text-gray-600 mt-3"><strong>ที่อยู่:</strong> {{ $order->shipping_address }}</p>
                @if($order->note)
                    <p class="text-sm text-gray-600 mt-2"><strong>หมายเหตุ:</strong> {{ $order->note }}</p>
                @endif

                @if(in_array($order->status, ['pending_payment', 'paid', 'processing']))
                    <form action="{{ route('member.orders.cancel', $order) }}" method="POST" class="mt-4 border-t pt-4"
                          onsubmit="return confirm('ยืนยันการยกเลิกออเดอร์นี้? สต็อกสินค้าจะถูกคืนเข้าคลัง')">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">
                            ✕ ยกเลิกออเดอร์นี้
                        </button>
                        <p class="text-xs text-gray-500 mt-1">
                            ยกเลิกได้ก่อนร้านแพ็ก/จัดส่ง หากชำระเงินแล้ว กรุณาติดต่อร้านเพื่อขอคืนเงิน
                        </p>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h2 class="font-bold mb-4">รายการสินค้า</h2>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex justify-between gap-4 text-sm border-b pb-3 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">รหัส {{ $item->product_code ?? '-' }} · {{ $item->qty }} ชิ้น × ฿{{ number_format($item->price) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">฿{{ number_format($item->lineTotal()) }}</p>
                                @if($item->deposit > 0)
                                    <p class="text-xs text-amber-600">มัดจำ ฿{{ number_format($item->deposit * $item->qty) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 space-y-1 text-sm border-t pt-3">
                    <div class="flex justify-between"><span class="text-gray-500">ยอดสินค้า</span><span>฿{{ number_format($order->subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">ค่าส่ง</span><span>฿{{ number_format($order->shipping_fee) }}</span></div>
                    <div class="flex justify-between font-bold text-base"><span>ยอดรวม</span><span class="text-sky-700">฿{{ number_format($order->total) }}</span></div>
                    @if($order->paidAmount() === 0 && $order->isPreorder())
                        <div class="flex justify-between font-semibold text-amber-600">
                            <span>ยอดที่ต้องชำระตอนนี้</span>
                            <span>฿{{ number_format($order->amountDueNow()) }}</span>
                        </div>
                        @if($order->hasInStockItems())
                            <p class="text-xs text-amber-600">รวมราคาสินค้าพร้อมส่ง + ค่ามัดจำพรีออเดอร์</p>
                        @endif
                    @endif
                    <div class="flex justify-between"><span class="text-gray-500">ชำระแล้ว (ตรวจสอบแล้ว)</span><span class="text-amber-600 font-semibold">฿{{ number_format($order->paidAmount()) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">ยอดค้างชำระทั้งหมด</span><span class="font-semibold">฿{{ number_format($order->dueAmount()) }}</span></div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h2 class="font-bold mb-3">ประวัติชำระเงิน</h2>
                @forelse($order->payments->sortByDesc('created_at') as $payment)
                    <div class="text-sm border-b last:border-0 py-2">
                        <div class="flex justify-between">
                            <span class="font-medium">{{ $payment->type === 'deposit' ? ($order->hasInStockItems() ? 'ยอดแรก (สินค้า + มัดจำ)' : 'มัดจำ') : 'ส่วนที่เหลือ' }}</span>
                            <span class="font-bold">฿{{ number_format($payment->amount) }}</span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $payment->bank }} · {{ $payment->transferred_at->format('d M Y H:i') }}</p>
                        <p class="text-xs mt-1">
                            <span class="px-2 py-0.5 rounded
                                {{ $payment->status === 'verified' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $payment->statusLabel() }}
                            </span>
                            @if($payment->slip_path)
                                <a href="{{ route('member.payments.slip', $payment) }}" target="_blank" class="ml-2 text-blue-600 hover:underline">ดูสลิป</a>
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">ยังไม่มีการชำระเงิน</p>
                @endforelse

                @if(in_array($order->status, ['pending_payment', 'paid', 'processing', 'ready_to_ship']) && $order->dueAmount() > 0)
                    <a href="{{ route('member.payments.create', ['order_id' => $order->id]) }}"
                       class="block text-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-lg py-2.5 mt-4 transition">
                        💳 {{ $order->paidAmount() === 0 && $order->isPreorder() ? 'ชำระเงินมัดจำ / ชำระครั้งแรกสำหรับออเดอร์นี้' : 'แจ้งชำระยอดคงเหลือสำหรับออเดอร์นี้' }}
                    </a>
                @endif
            </div>

            @if($order->isPreorder())
                <div class="rounded-xl p-5 text-sm border {{ $order->payFirstPayment() ? 'bg-amber-50 border-amber-200 text-amber-800' : ($order->isBalanceDue() ? 'bg-red-50 border-red-200 text-red-700' : 'bg-amber-50 border-amber-200 text-amber-800') }}">
                    @if(! $order->hasOutstandingBalance())
                        <strong>✅ ชำระครบแล้ว</strong>
                        <p class="text-xs mt-1">ออเดอร์นี้ชำระเงินครบถ้วนแล้ว</p>
                    @elseif($order->payFirstPayment())
                        <strong>💰 ยอดที่ต้องชำระตอนนี้</strong>
                        <p class="text-xs mt-1">฿{{ number_format($order->amountDueNow()) }}{{ $order->hasInStockItems() ? ' (สินค้าพร้อมส่ง + มัดจำ)' : ' (มัดจำ)' }}</p>
                        <p class="text-xs mt-1">ยอดคงเหลือหลังชำระแรก ฿{{ number_format($order->dueAmount() - $order->amountDueNow()) }}</p>
                        @if($order->balance_due_date)
                            <p class="text-xs mt-1">กำหนดเรียกเก็บยอดคงเหลือ {{ $order->balance_due_date->format('d/m/Y') }}</p>
                        @endif
                    @elseif($order->balance_due_date)
                        <strong>{{ $order->isBalanceDue() ? '🔔 ถึงกำหนดเรียกเก็บยอดคงเหลือแล้ว' : '📅 กำหนดเรียกเก็บยอดคงเหลือ' }}</strong>
                        <p class="text-xs mt-1">
                            ยอดคงเหลือ ฿{{ number_format($order->dueAmount()) }}
                            กำหนดวันที่ {{ $order->balance_due_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs mt-1">
                            {{ $order->isBalanceDue() ? 'กรุณาชำระยอดคงเหลือเพื่อให้ร้านจัดส่งสินค้า' : 'สามารถชำระก่อนกำหนดได้ หรือรอชำระตามวันที่นัดไว้' }}
                        </p>
                    @else
                        <strong>💰 ยอดคงเหลือ</strong>
                        <p class="text-xs mt-1">฿{{ number_format($order->dueAmount()) }} (รอประกาศวันวางจำหน่าย)</p>
                    @endif
                </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-800">
                <strong>💡 ขั้นตอน:</strong>
                <ol class="list-decimal list-inside mt-2 space-y-1 text-xs">
                    <li>โอนเงินเข้าบัญชีร้าน (ดูในหน้าติดต่อ)</li>
                    <li>กดแจ้งชำระเงิน + อัปโหลดสลิป</li>
                    <li>รอพนักงานอนุมัติ แล้วจะเห็นสถานะเปลี่ยน</li>
                    @if($order->deposit_total > 0 && $order->dueAmount() > 0)
                        <li>
                            ชำระส่วนที่เหลือ
                            @if($order->balance_due_date)
                                ภายในวันที่ {{ $order->balance_due_date->format('d/m/Y') }}
                            @else
                                เมื่อสินค้าเข้า
                            @endif
                        </li>
                    @endif
                </ol>
            </div>
        </div>
    </div>

@endsection
