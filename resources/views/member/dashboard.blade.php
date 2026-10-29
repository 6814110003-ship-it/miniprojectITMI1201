@extends('layouts.app')

@section('title', 'แดชบอร์ดสมาชิก')
@section('content')

    <h1 class="text-2xl font-bold mb-6">สวัสดี, {{ $member->name }} 👋</h1>

    @if($balanceDueOrders->isNotEmpty())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700">
            <p class="font-bold">🔔 มียอดคงเหลือถึงกำหนดชำระ {{ $balanceDueOrders->count() }} ออเดอร์</p>
            <div class="mt-2 space-y-2">
                @foreach($balanceDueOrders->take(3) as $dueOrder)
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-white/60 rounded-lg px-3 py-2">
                        <span>
                            <a href="{{ route('member.orders.show', $dueOrder) }}" class="font-medium underline">{{ $dueOrder->order_no }}</a>
                            • คงเหลือ ฿{{ number_format($dueOrder->total - (int) $dueOrder->verified_paid) }}
                            • กำหนด {{ $dueOrder->balance_due_date->format('d/m/Y') }}
                        </span>
                        <a href="{{ route('member.payments.create') }}?order_id={{ $dueOrder->id }}" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg px-3 py-1.5">ชำระยอดคงเหลือ</a>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($upcomingBalanceOrders->isNotEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
            <p class="font-bold">📅 พรีออเดอร์ใกล้ถึงกำหนดเรียกเก็บยอดคงเหลือ</p>
            <div class="mt-2 space-y-1 text-xs">
                @foreach($upcomingBalanceOrders as $upcomingOrder)
                    <p>
                        <a href="{{ route('member.orders.show', $upcomingOrder) }}" class="font-medium underline">{{ $upcomingOrder->order_no }}</a>
                        • ฿{{ number_format($upcomingOrder->total - (int) $upcomingOrder->verified_paid) }}
                        • {{ $upcomingOrder->balance_due_date->format('d/m/Y') }}
                    </p>
                @endforeach
            </div>
        </div>
    @endif


    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ออเดอร์ทั้งหมด</p>
            <p class="text-2xl font-bold">{{ $stats['totalOrders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">รอชำระเงิน</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pendingOrders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">สำเร็จแล้ว</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['completedOrders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ยอดซื้อรวม</p>
            <p class="text-2xl font-bold text-sky-700">฿{{ number_format($stats['totalSpent']) }}</p>
        </div>
    </div>

    <div class="flex gap-4 mb-6">
        <a href="{{ route('products.index') }}" class="bg-sky-700 hover:bg-sky-800 text-white text-sm font-bold rounded-lg px-4 py-2 transition">สั่งซื้อสินค้า</a>
        <a href="{{ route('member.orders') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold rounded-lg px-4 py-2 transition">ดูออเดอร์ของฉัน</a>
        <a href="{{ route('member.payments.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-lg px-4 py-2 transition">แจ้งชำระเงิน</a>
        <a href="{{ route('member.profile.edit') }}" class="text-sm text-gray-600 hover:underline self-center">จัดการโปรไฟล์</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">ออเดอร์ล่าสุด</h2>
            <a href="{{ route('member.orders') }}" class="text-sm text-sky-700 hover:underline">ดูทั้งหมด →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">เลขออเดอร์</th>
                        <th>วันที่</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        @php
                            $depositPaid = $order->paidAmount() > 0;
                            $remaining = $order->dueAmount();
                        @endphp
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $order->order_no }}</td>
                            <td class="py-2">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="py-2">
                                @if($order->isPreorder())
                                    <p class="text-xs font-semibold {{ $depositPaid ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ $depositPaid ? 'มัดจำแล้ว' : 'จ่ายยอดแรก' }} ฿{{ number_format($depositPaid ? $order->deposit_total : $order->amountDueNow()) }}
                                    </p>
                                    <p class="text-xs text-gray-500">ยอดรวม ฿{{ number_format($order->total) }}</p>
                                @else
                                    ฿{{ number_format($order->total) }}
                                @endif
                            </td>
                            <td class="py-2">
                                @if($order->isPreorder())
                                    <span class="text-xs font-semibold px-2 py-1 rounded bg-amber-100 text-amber-700">พรีออเดอร์</span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $depositPaid ? ($remaining > 0 ? 'มัดจำแล้ว' : 'ชำระครบแล้ว') : 'รอชำระยอดแรก' }}
                                    </p>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 rounded
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $order->statusLabel() }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 text-right">
                                <a href="{{ route('member.orders.show', $order) }}" class="text-sky-700 hover:underline text-xs">รายละเอียด</a>
                                @if($order->isPreorder() && $remaining > 0 && in_array($order->status, ['pending_payment', 'paid', 'processing', 'ready_to_ship']))
                                    <a href="{{ route('member.payments.create') }}?order_id={{ $order->id }}"
                                       class="block text-xs font-semibold text-amber-600 hover:underline mt-1">
                                        {{ $depositPaid ? 'จ่ายส่วนที่เหลือ' : 'จ่ายยอดแรก' }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">ยังไม่มีออเดอร์</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
