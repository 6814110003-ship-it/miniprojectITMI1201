@extends('layouts.admin')

@section('title', 'หมวดหมู่')
@section('content')

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">หมวดหมู่สินค้า</h2>
        <a href="{{ route('admin.categories.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-4 py-2 text-sm transition">+ เพิ่มหมวดหมู่</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">ชื่อ</th>
                    <th class="px-4 py-3 text-center">ลำดับ</th>
                    <th class="px-4 py-3 text-center">สถานะ</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($category->is_active)
                                <span class="text-xs font-bold px-2 py-1 rounded bg-green-100 text-green-700">แสดง</span>
                            @else
                                <span class="text-xs font-bold px-2 py-1 rounded bg-gray-200 text-gray-500">ซ่อน</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-sky-600 hover:underline">แก้ไข</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('ลบหมวดหมู่นี้?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline ml-2">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-gray-400">ยังไม่มีหมวดหมู่</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>

@endsection
