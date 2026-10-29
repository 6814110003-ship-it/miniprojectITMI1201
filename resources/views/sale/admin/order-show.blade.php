@extends('layouts.admin')

@section('title', 'ออเดอร์ '.$order->order_no)
@section('content')

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">ออเดอร์ <span class="font-mono">{{ $order->order_no }}</span></h2>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-sky-600 hover:underline">← กลับ</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold">สินค้าในออเดอร์</h3>
                    <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100">{{ $order->statusLabel() }}</span>
                </div>
                <table class="w-full text-sm">
                    <thead class="text-gray-400 text-xs uppercase border-b">
                        <tr>
                            <th class="py-2 text-left">สินค้า</th>
                            <th class="py-2 text-right">ราคา</th>
                            <th class="py-2 text-center">จำนวน</th>
                            <th class="py-2 text-right">รวม</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-2">
                                    <p class="font-medium">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->product_code }}</p>
                                    @if($item->deposit > 0)
                                        <p class="text-xs text-amber-600">มัดจำ/ชิ้น ฿{{ number_format($item->deposit) }}</p>
                                    @endif
                                </td>
                                <td class="py-2 text-right">฿{{ number_format($item->price) }}</td>
                                <td class="py-2 text-center">{{ $item->qty }}</td>
                                <td class="py-2 text-right font-semibold">฿{{ number_format($item->price * $item->qty) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t">
                        <tr><td colspan="3" class="py-2 text-right text-gray-500">ยอดสินค้า</td><td class="py-2 text-right">฿{{ number_format($order->subtotal) }}</td></tr>
                        <tr><td colspan="3" class="py-2 text-right text-gray-500">ค่าจัดส่ง</td><td class="py-2 text-right">฿{{ number_format($order->shipping_fee) }}</td></tr>
                        <tr><td colspan="3" class="py-2 text-right font-bold">ยอดรวมทั้งหมด</td><td class="py-2 text-right font-bold text-sky-700">฿{{ number_format($order->total) }}</td></tr>
                        @if($order->deposit_total > 0)
                            <tr><td colspan="3" class="py-1 text-right text-gray-500">มัดจำรวม</td><td class="py-1 text-right text-amber-600">฿{{ number_format($order->deposit_total) }}</td></tr>
                        @endif
                        @if($order->balance_due_date)
                            <tr><td colspan="3" class="py-1 text-right text-gray-500">กำหนดเก็บยอดคงเหลือ</td><td class="py-1 text-right">{{ $order->balance_due_date->format('d/m/Y') }}</td></tr>
                        @endif
                        <tr><td colspan="3" class="py-1 text-right text-gray-500">ชำระแล้ว</td><td class="py-1 text-right text-green-600">฿{{ number_format($order->paidAmount()) }}</td></tr>
                        <tr><td colspan="3" class="py-1 text-right text-gray-500">คงเหลือ</td><td class="py-1 text-right text-red-500">฿{{ number_format($order->dueAmount()) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-bold mb-3">ประวัติชำระเงิน</h3>
                @forelse($order->payments as $payment)
                    <div class="flex items-center gap-3 border-b last:border-0 py-2 text-sm">
                        @if($payment->slip_path)
                            <a href="{{ route('admin.payments.slip', $payment) }}" target="_blank" class="shrink-0">
                                <img src="{{ route('admin.payments.slip', $payment) }}" alt="slip" class="w-12 h-12 object-cover rounded border">
                            </a>
                        @endif
                        <div class="flex-1">
                            <p class="font-medium">฿{{ number_format($payment->amount) }} — {{ $payment->bank }}</p>
                            <p class="text-xs text-gray-400">{{ $payment->transferred_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded
                            @if($payment->status === 'pending') bg-amber-100 text-amber-700
                            @elseif($payment->status === 'verified') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $payment->statusLabel() }}
                        </span>
                        @if($payment->status === 'pending')
                            <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">@csrf
                                <button class="text-xs bg-green-600 hover:bg-green-700 text-white rounded px-2 py-1">อนุมัติ</button>
                            </form>
                            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="inline">@csrf
                                <button class="text-xs bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1">ปฏิเสธ</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400">ยังไม่มีการแจ้งชำระเงิน</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-bold mb-3">อัปเดตสถานะ</h3>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-4 py-2 text-sm transition">บันทึกสถานะ</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5 text-sm space-y-2">
                <h3 class="font-bold mb-2">ข้อมูลลูกค้า</h3>
                <p><span class="text-gray-500">ลูกค้า:</span> {{ $order->member?->name ?? $order->customer_name }}</p>
                <p><span class="text-gray-500">อีเมล:</span> {{ $order->member?->email ?? '-' }}</p>
                <p><span class="text-gray-500">เบอร์โทร:</span> {{ $order->customer_phone }}</p>
                <p><span class="text-gray-500">ที่อยู่จัดส่ง:</span> {{ $order->shipping_address }}</p>
                <p><span class="text-gray-500">วิธีจัดส่ง:</span> {{ strtoupper($order->shipping_method) }}</p>
                @if($order->balance_due_date)
                    <p><span class="text-gray-500">วันนัดเก็บยอดคงเหลือ:</span> {{ $order->balance_due_date->format('d/m/Y') }}</p>
                @endif
                @if($order->note)<p><span class="text-gray-500">หมายเหตุ:</span> {{ $order->note }}</p>@endif
            </div>
        </div>
    </div>

@endsection
