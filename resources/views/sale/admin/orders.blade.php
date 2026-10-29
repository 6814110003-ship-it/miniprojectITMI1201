@extends('layouts.admin')

@section('title', 'ออเดอร์')
@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold">ออเดอร์ทั้งหมด</h2>
            <p class="text-sm text-gray-500">พบ {{ $orders->total() }} รายการ</p>
        </div>
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="เลขที่/ลูกค้า/อีเมล/เบอร์"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="balance" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทั้งหมด</option>
                <option value="due" @selected(request('balance') === 'due')>ถึงกำหนดเก็บยอดคงเหลือ</option>
                <option value="upcoming" @selected(request('balance') === 'upcoming')>ใกล้กำหนดเก็บยอด</option>
                <option value="preorder" @selected(request('balance') === 'preorder')>พรีออเดอร์</option>
            </select>
            <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">กรอง</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">เลขที่ออเดอร์</th>
                    <th class="px-4 py-3 text-left">ลูกค้า</th>
                    <th class="px-4 py-3 text-center">สถานะ</th>
                    <th class="px-4 py-3 text-right">ยอดรวม</th>
                    <th class="px-4 py-3 text-center">วันที่สั่ง</th>
                    <th class="px-4 py-3 text-center">ดู</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                        @forelse($orders as $order)
                            @php
                                $depositPaid = $order->paidAmount() > 0;
                                $remaining = $order->dueAmount();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $order->order_no }}</td>
                                <td class="px-4 py-3">
                                    <p>{{ $order->customer_name }}</p>
                                    @if($order->isPreorder())
                                        <p class="text-xs text-amber-600">พรีออเดอร์ · {{ $depositPaid ? ($remaining > 0 ? 'มัดจำแล้ว' : 'ชำระครบแล้ว') : 'รอชำระยอดแรก' }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($order->isPreorder())
                                        <span class="text-xs font-bold px-2 py-1 rounded bg-amber-100 text-amber-700">พรีออเดอร์</span>
                                    @else
                                        <span class="text-xs font-bold px-2 py-1 rounded
                                            @if($order->status === 'pending_payment') bg-amber-100 text-amber-700
                                            @elseif($order->status === 'paid') bg-blue-100 text-blue-700
                                            @elseif($order->status === 'processing') bg-indigo-100 text-indigo-700
                                            @elseif($order->status === 'ready_to_ship') bg-purple-100 text-purple-700
                                            @elseif($order->status === 'completed') bg-green-100 text-green-700
                                            @else bg-gray-200 text-gray-600 @endif">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <p class="font-semibold">฿{{ number_format($order->total) }}</p>
                                    @if($order->isPreorder())
                                        <p class="text-xs {{ $depositPaid ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $depositPaid ? 'มัดจำแล้ว' : 'จ่ายยอดแรก' }} ฿{{ number_format($depositPaid ? $order->deposit_total : $order->amountDueNow()) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sky-600 hover:underline">รายละเอียด</a>
                                </td>
                            </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">ยังไม่มีออเดอร์</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>

@endsection
