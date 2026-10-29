@extends('layouts.app')

@section('title', 'ออเดอร์ของฉัน')
@section('content')

    <h1 class="text-2xl font-bold mb-6">ออเดอร์ของฉัน</h1>

    <div class="flex gap-2 mb-6 flex-wrap">
        <a href="{{ route('member.orders') }}"
           class="text-sm px-3 py-1.5 rounded-full {{ !request('status') ? 'bg-sky-700 text-white' : 'bg-gray-200 text-gray-700' }}">ทั้งหมด</a>
        @foreach(\App\Models\Order::STATUS_LABELS as $key => $label)
            <a href="{{ route('member.orders', ['status' => $key]) }}"
               class="text-sm px-3 py-1.5 rounded-full {{ request('status') === $key ? 'bg-sky-700 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr class="text-left">
                        <th class="px-4 py-3">เลขออเดอร์</th>
                        <th>วันที่สั่ง</th>
                        <th>จำนวนรายการ</th>
                        <th>ยอดรวม</th>
                        <th>ยอดชำระแล้ว</th>
                        <th>สถานะ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $depositPaid = $order->paidAmount() > 0;
                            $remaining = $order->dueAmount();
                        @endphp
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium">{{ $order->order_no }}</td>
                            <td class="py-3">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="py-3">{{ $order->items->sum('qty') }} ชิ้น</td>
                            <td class="py-3">
                                <p class="font-semibold">฿{{ number_format($order->total) }}</p>
                                @if($order->isPreorder())
                                    <p class="text-xs {{ $depositPaid ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ $depositPaid ? 'มัดจำแล้ว' : 'จ่ายยอดแรก' }} ฿{{ number_format($depositPaid ? $order->deposit_total : $order->amountDueNow()) }}
                                    </p>
                                @endif
                            </td>
                            <td class="py-3 text-amber-600 font-semibold">฿{{ number_format($order->paidAmount()) }}</td>
                            <td class="py-3">
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
                            <td class="py-3 text-right pr-4">
                                <a href="{{ route('member.orders.show', $order) }}" class="text-sky-700 hover:underline text-xs">ดูรายละเอียด</a>
                                @if($order->isPreorder() && $remaining > 0 && in_array($order->status, ['pending_payment', 'paid', 'processing', 'ready_to_ship']))
                                    <a href="{{ route('member.payments.create') }}?order_id={{ $order->id }}"
                                       class="block text-xs font-semibold text-amber-600 hover:underline mt-1">
                                        {{ $depositPaid ? 'จ่ายส่วนที่เหลือ' : 'จ่ายยอดแรก' }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-gray-500">ไม่มีออเดอร์</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $orders->links() }}
        </div>
    </div>

@endsection
