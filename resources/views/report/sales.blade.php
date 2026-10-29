@extends('layouts.admin')

@section('title', 'รายงานยอดขาย')
@section('content')

    <div class="flex flex-wrap items-end justify-between gap-3 mb-5">
        <div>
            <h2 class="text-lg font-bold">รายงานยอดขาย</h2>
            <p class="text-sm text-gray-500">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</p>
        </div>
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex items-end gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">จาก</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">ถึง</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>
            <button class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg px-4 py-2 text-sm font-bold">แสดง</button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">ออเดอร์</p>
            <p class="text-xl font-bold">{{ $summary['orders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">ยอดขายรวม</p>
            <p class="text-xl font-bold text-sky-700">฿{{ number_format($summary['revenue']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">มัดจำ</p>
            <p class="text-xl font-bold text-amber-600">฿{{ number_format($summary['deposits']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">ค่าจัดส่ง</p>
            <p class="text-xl font-bold">฿{{ number_format($summary['shipping']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-xs text-gray-500">จำนวนชิ้น</p>
            <p class="text-xl font-bold">{{ $summary['items'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="font-bold mb-3">ยอดขายรายวัน</h3>
            <table class="w-full text-sm">
                <thead class="text-gray-400 text-xs uppercase border-b">
                    <tr><th class="py-2 text-left">วันที่</th><th class="py-2 text-center">ออเดอร์</th><th class="py-2 text-right">ยอดขาย</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($byDay as $row)
                        <tr>
                            <td class="py-2">{{ \Carbon\Carbon::parse($row->day)->format('d/m/Y') }}</td>
                            <td class="py-2 text-center">{{ $row->orders }}</td>
                            <td class="py-2 text-right font-semibold">฿{{ number_format($row->revenue) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-gray-400">ไม่มีข้อมูลในช่วงนี้</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-bold mb-3">สินค้าขายดี Top 10</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                        @forelse($topProducts as $product)
                            <tr>
                                <td class="py-2">{{ $product->name }}</td>
                                <td class="py-2 text-right font-semibold">{{ $product->sold_qty }} ชิ้น</td>
                            </tr>
                        @empty
                            <tr><td class="py-6 text-center text-gray-400">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-bold mb-3">ยอดขายตามหมวดหมู่</h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y">
                        @forelse($byCategory as $category)
                            <tr>
                                <td class="py-2">{{ $category->name }}</td>
                                <td class="py-2 text-right font-semibold">{{ $category->sold_qty }} ชิ้น</td>
                            </tr>
                        @empty
                            <tr><td class="py-6 text-center text-gray-400">ไม่มีข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
