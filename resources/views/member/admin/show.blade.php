@extends('layouts.admin')

@section('title', 'ข้อมูลลูกค้า')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold">{{ $member->name }}</h2>
            <p class="text-sm text-gray-500">สมาชิก #{{ $member->id }} • สมัครเมื่อ {{ $member->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="text-sm text-sky-600 hover:underline">← กลับรายการลูกค้า</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ออเดอร์ทั้งหมด</p>
            <p class="text-2xl font-bold">{{ number_format($stats['totalOrders']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ยอดซื้อรวม</p>
            <p class="text-2xl font-bold text-green-600">฿{{ number_format($stats['totalSpent']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">สำเร็จ / ยกเลิก</p>
            <p class="text-2xl font-bold"><span class="text-green-600">{{ number_format($stats['completedOrders']) }}</span> <span class="text-gray-300">/</span> <span class="text-red-500">{{ number_format($stats['cancelledOrders']) }}</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">รีวิว / รายงานสินค้า</p>
            <p class="text-2xl font-bold"><span class="text-sky-600">{{ number_format($stats['reviews']) }}</span> <span class="text-gray-300">/</span> <span class="text-amber-600">{{ number_format($stats['reports']) }}</span></p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm border p-5 h-fit">
            <h3 class="font-bold mb-4">ข้อมูลติดต่อ</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">ชื่อ</dt>
                    <dd class="font-medium">{{ $member->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">อีเมล</dt>
                    <dd class="font-medium break-all">{{ $member->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">เบอร์โทร</dt>
                    <dd class="font-medium">{{ $member->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">ที่อยู่</dt>
                    <dd class="font-medium whitespace-pre-line">{{ $member->address ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">ประวัติการสั่งซื้อ</h3>
                <a href="{{ route('admin.orders.index', ['q' => $member->email]) }}" class="text-sm text-sky-600 hover:underline">ดูในหน้าออเดอร์</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-gray-500 text-xs uppercase">
                        <tr class="text-left">
                            <th class="pb-2">ออเดอร์</th>
                            <th class="pb-2">วันที่</th>
                            <th class="pb-2 text-center">รายการ</th>
                            <th class="pb-2 text-right">ยอดรวม</th>
                            <th class="pb-2 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($orders as $order)
                            @php
                                $colors = [
                                    'pending_payment' => 'bg-amber-100 text-amber-700',
                                    'paid' => 'bg-sky-100 text-sky-700',
                                    'processing' => 'bg-indigo-100 text-indigo-700',
                                    'ready_to_ship' => 'bg-purple-100 text-purple-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $depositPaid = $order->paidAmount() > 0;
                                $remaining = $order->dueAmount();
                            @endphp
                            <tr>
                                <td class="py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sky-600 hover:underline font-medium">{{ $order->order_no }}</a>
                                </td>
                                <td class="py-3 text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 text-center">{{ number_format($order->items_count) }}</td>
                                <td class="py-3 text-right">
                                    <p class="font-medium">฿{{ number_format($order->total) }}</p>
                                    @if($order->isPreorder())
                                        <p class="text-xs {{ $depositPaid ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $depositPaid ? 'มัดจำแล้ว' : 'จ่ายยอดแรก' }} ฿{{ number_format($depositPaid ? $order->deposit_total : $order->amountDueNow()) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if($order->isPreorder())
                                        <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">พรีออเดอร์</span>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $depositPaid ? ($remaining > 0 ? 'มัดจำแล้ว' : 'ชำระครบแล้ว') : 'รอชำระยอดแรก' }}
                                        </p>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded {{ $colors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $order->statusLabel() }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-gray-400">ลูกค้ายังไม่มีประวัติการสั่งซื้อ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
