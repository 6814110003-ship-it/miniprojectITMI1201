@extends('layouts.admin')

@section('title', isset($category) ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่')
@section('content')

    <div class="max-w-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">{{ isset($category) ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่ใหม่' }}</h2>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-sky-600 hover:underline">← กลับ</a>
        </div>

        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
              method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">ชื่อหมวดหมู่ *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ลำดับการแสดง</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0"
                       class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))> แสดงหน้าร้าน
            </label>

            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-6 py-2.5 text-sm transition">บันทึก</button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-6 py-2.5 text-sm font-medium transition">ยกเลิก</a>
            </div>
        </form>
    </div>

@endsection
