@extends('layouts.admin')

@section('title', 'แท็ก')
@section('content')

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-bold">แท็กสินค้า</h2>
            <p class="text-sm text-gray-500">พบ {{ $tags->total() }} แท็ก</p>
        </div>
        <a href="{{ route('admin.tags.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-4 py-2 text-sm transition">+ เพิ่มแท็ก</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">ชื่อ</th>
                    <th class="px-4 py-3 text-center">จำนวนสินค้า</th>
                    <th class="px-4 py-3 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tags as $tag)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $tag->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $tag->products_count }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="text-sky-600 hover:underline">แก้ไข</a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline" onsubmit="return confirm('ลบแท็กนี้?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline ml-2">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-10 text-center text-gray-400">ยังไม่มีแท็ก</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tags->links() }}</div>

@endsection
