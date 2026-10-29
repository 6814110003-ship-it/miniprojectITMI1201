@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('content')

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold">สินค้าทั้งหมด</h2>
            <p class="text-sm text-gray-500">พบ {{ $products->total() }} รายการ</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-4 py-2 text-sm transition">
            + เพิ่มสินค้า
        </a>
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4 flex flex-wrap items-center gap-2 max-w-3xl">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อหรือรหัสสินค้า..."
               class="flex-1 min-w-56 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        <select name="type" aria-label="ชนิดสินค้า"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            <option value="">ทุกชนิด</option>
            @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 py-2 text-sm">ค้นหา</button>
        @if(request()->filled('q') || request()->filled('type'))
            <a href="{{ route('admin.products.index') }}" class="text-sm text-sky-600 hover:underline">ล้างตัวกรอง</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">สินค้า</th>
                    <th class="px-4 py-3 text-left">หมวดหมู่</th>
                    <th class="px-4 py-3 text-right">ราคา</th>
                    <th class="px-4 py-3 text-center">สถานะ</th>
                    <th class="px-4 py-3 text-right">สต็อก</th>
                    <th class="px-4 py-3 text-center">รายงาน</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($product->coverImage())
                                        <img src="{{ asset('storage/'.$product->coverImage()) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-gray-300">🕹️</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $product->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">฿{{ number_format($product->price) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-bold px-2 py-1 rounded
                                @if($product->status === 'preorder') bg-amber-100 text-amber-700
                                @elseif($product->status === 'instock') bg-green-100 text-green-700
                                @else bg-gray-200 text-gray-600 @endif">
                                {{ $product->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">{{ $product->stock }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($product->pending_reports_count > 0)
                                <a href="{{ route('admin.reports.index', ['q' => $product->code ?: $product->name]) }}"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-red-600 hover:underline">
                                    ⚠ {{ $product->pending_reports_count }} รายงาน
                                </a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-sky-600 hover:underline">แก้ไข</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                  onsubmit="return confirm('ลบสินค้านี้?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline ml-2">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">ยังไม่มีสินค้า — กด "+ เพิ่มสินค้า" เพื่อเริ่มต้น</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>

@endsection
