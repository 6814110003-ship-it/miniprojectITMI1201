@extends('layouts.admin')

@section('title', 'ลูกค้า')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold">ลูกค้า</h2>
            <p class="text-sm text-gray-500">
                ทั้งหมด {{ number_format($totalMembers) }} คน
                <span class="text-green-600">• สมัครใหม่เดือนนี้ {{ number_format($newThisMonth) }} คน</span>
            </p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.members.index') }}" class="mb-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อ / อีเมล / เบอร์โทร..."
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">ค้นหา</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">ลูกค้า</th>
                    <th class="px-4 py-3 text-left">ติดต่อ</th>
                    <th class="px-4 py-3 text-center">ออเดอร์</th>
                    <th class="px-4 py-3 text-right">ยอดซื้อรวม</th>
                    <th class="px-4 py-3 text-left">สมัครเมื่อ</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400">#{{ $member->id }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <p>{{ $member->email }}</p>
                            <p class="text-xs text-gray-400">{{ $member->phone ?: 'ไม่มีเบอร์โทร' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ number_format($member->orders_count) }}</td>
                        <td class="px-4 py-3 text-right font-medium">฿{{ number_format($member->total_spent ?? 0) }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $member->created_at->format('d/m/Y') }}
                            @if($member->last_order_at)
                                <p class="text-xs text-gray-400">ซื้อล่าสุด {{ \Illuminate\Support\Carbon::parse($member->last_order_at)->diffForHumans() }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.members.show', $member) }}" class="text-sky-600 hover:underline">ดูข้อมูล</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">ไม่พบข้อมูลลูกค้า</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $members->links() }}</div>
@endsection
