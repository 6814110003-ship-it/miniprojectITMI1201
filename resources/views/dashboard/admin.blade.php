@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ยอดขายวันนี้</p>
            <p class="text-2xl font-bold text-green-600">฿{{ number_format($stats['todayRevenue']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">ออเดอร์ทั้งหมด</p>
            <p class="text-2xl font-bold">{{ $stats['totalOrders'] }}</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:border-sky-300 hover:shadow transition">
            <p class="text-gray-500 text-sm">สมาชิกทั้งหมด</p>
            <p class="text-2xl font-bold text-sky-600">{{ number_format($stats['totalMembers']) }} <span class="text-sm font-normal text-gray-500">คน</span></p>
            <p class="text-xs text-green-600 mt-1">สมัครใหม่วันนี้ +{{ number_format($stats['newMembersToday']) }} • ดูรายชื่อ →</p>
        </a>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">แจ้งชำระรอตรวจ</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pendingPayments'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5 {{ ($stats['balanceDueCount'] ?? 0) > 0 ? 'border-red-200' : '' }}">
            <p class="text-gray-500 text-sm">พรีออเดอร์ถึงกำหนดเก็บยอด</p>
            <p class="text-2xl font-bold {{ ($stats['balanceDueCount'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-700' }}">{{ number_format($stats['balanceDueCount'] ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">ใกล้กำหนด {{ number_format($stats['balanceUpcomingCount'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-gray-500 text-sm">สินค้าพร้อมขาย</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['activeProducts'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold">ออเดอร์ล่าสุด</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-gray-500">
                        <tr class="text-left">
                            <th class="pb-2">ออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th>ยอด</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr class="border-t">
                                <td class="py-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline font-medium">{{ $order->order_no }}</a>
                                </td>
                                <td class="py-2">{{ $order->customer_name }}</td>
                                <td class="py-2 font-medium">฿{{ number_format($order->total) }}</td>
                                <td class="py-2"><span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">{{ $order->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold">แจ้งชำระรอตรวจสอบ</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-blue-600 hover:underline">ดูทั้งหมด</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingPayments as $payment)
                    <div class="border rounded-lg p-3 text-sm flex items-center justify-between gap-3">
                        <div>
                            <a href="{{ route('admin.payments.index') }}" class="font-medium text-blue-600 hover:underline">{{ $payment->order->order_no }}</a>
                            <p class="text-xs text-gray-500">{{ $payment->member?->name }} • {{ $payment->bank }} • ฿{{ number_format($payment->amount) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->transferred_at->format('d M Y H:i') }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-amber-100 text-amber-700">รอตรวจสอบ</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6 text-sm">ไม่มีรายการรอตรวจ</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold">พรีออเดอร์ถึงกำหนดเก็บยอดคงเหลือ</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">ดูออเดอร์</a>
            </div>
            <div class="space-y-3">
                @forelse($balanceDueOrders as $dueOrder)
                    <div class="border rounded-lg p-3 text-sm flex items-center justify-between gap-3">
                        <div>
                            <a href="{{ route('admin.orders.show', $dueOrder) }}" class="font-medium text-blue-600 hover:underline">{{ $dueOrder->order_no }}</a>
                            <p class="text-xs text-gray-500">{{ $dueOrder->customer_name }} • คงเหลือ ฿{{ number_format($dueOrder->dueAmount()) }}</p>
                            <p class="text-xs text-red-500">กำหนด {{ $dueOrder->balance_due_date->format('d/m/Y') }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700">ถึงกำหนด</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6 text-sm">ไม่มีออเดอร์ถึงกำหนด</p>
                @endforelse
            </div>
        </div>
    </div>

@endsection