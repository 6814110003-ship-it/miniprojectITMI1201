@extends('layouts.admin')

@section('title', 'ตรวจสอบชำระเงิน')
@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold">ตรวจสอบการชำระเงิน</h2>
            <p class="text-sm text-gray-500">พบ {{ $payments->total() }} รายการ</p>
        </div>
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex gap-2">
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\Payment::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">กรอง</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">สลิป</th>
                    <th class="px-4 py-3 text-left">ออเดอร์</th>
                    <th class="px-4 py-3 text-left">สมาชิก</th>
                    <th class="px-4 py-3 text-left">ธนาคาร</th>
                    <th class="px-4 py-3 text-right">จำนวนเงิน</th>
                    <th class="px-4 py-3 text-center">สถานะ</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            @if($payment->slip_path)
                                <img src="{{ route('admin.payments.slip', $payment) }}" alt="slip" class="w-12 h-12 object-cover rounded border cursor-pointer" onclick="window.open(this.src)">
                            @else
                                <span class="text-gray-300">ไม่มีสลิป</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 font-mono text-xs">
                            <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-sky-600 hover:underline">
                                {{ $payment->order?->order_no }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $payment->member?->name }}</td>
                        <td class="px-4 py-2">{{ $payment->bank }}<br><span class="text-xs text-gray-400">{{ $payment->transferred_at?->format('d/m/Y') }}</span></td>
                        <td class="px-4 py-2 text-right font-semibold">฿{{ number_format($payment->amount) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs font-bold px-2 py-1 rounded
                                @if($payment->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($payment->status === 'verified') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $payment->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center whitespace-nowrap">
                            @if($payment->status === 'pending')
                                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">@csrf
                                    <button class="text-xs bg-green-600 hover:bg-green-700 text-white rounded px-2 py-1">อนุมัติ</button>
                                </form>
                                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="inline">@csrf
                                    <button class="text-xs bg-red-500 hover:bg-red-600 text-white rounded px-2 py-1 ml-1">ปฏิเสธ</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">จัดการแล้ว</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">ยังไม่มีการแจ้งชำระเงิน</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>

@endsection
