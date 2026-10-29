@extends('layouts.admin')

@section('title', isset($tag) ? 'แก้ไขแท็ก' : 'เพิ่มแท็ก')
@section('content')

    <div class="max-w-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">{{ isset($tag) ? 'แก้ไขแท็ก' : 'เพิ่มแท็กใหม่' }}</h2>
            <a href="{{ route('admin.tags.index') }}" class="text-sm text-sky-600 hover:underline">← กลับ</a>
        </div>

        <form action="{{ isset($tag) ? route('admin.tags.update', $tag) : route('admin.tags.store') }}"
              method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            @csrf
            @if(isset($tag)) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">ชื่อแท็ก *</label>
                <input type="text" name="name" value="{{ old('name', $tag->name ?? '') }}" required maxlength="50"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-6 py-2.5 text-sm transition">บันทึก</button>
                <a href="{{ route('admin.tags.index') }}" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-6 py-2.5 text-sm font-medium transition">ยกเลิก</a>
            </div>
        </form>
    </div>

@endsection
