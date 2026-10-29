@extends('layouts.admin')

@section('title', isset($employee) ? 'แก้ไขพนักงาน' : 'เพิ่มพนักงาน')
@section('content')

    <div class="max-w-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">{{ isset($employee) ? 'แก้ไขพนักงาน: '.$employee->name : 'เพิ่มพนักงานใหม่' }}</h2>
            <a href="{{ route('admin.employees.index') }}" class="text-sm text-sky-600 hover:underline">← กลับ</a>
        </div>

        <form action="{{ isset($employee) ? route('admin.employees.update', $employee) : route('admin.employees.store') }}"
              method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            @csrf
            @if(isset($employee)) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">ชื่อ *</label>
                <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">อีเมล *</label>
                <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">รหัสผ่าน {{ isset($employee) ? '(เว้นว่างถ้าไม่เปลี่ยน)' : '*' }}</label>
                    <input type="password" name="password" {{ isset($employee) ? '' : 'required' }} minlength="6"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">ยืนยันรหัสผ่าน {{ isset($employee) ? '(ถ้าเปลี่ยน)' : '*' }}</label>
                    <input type="password" name="password_confirmation" {{ isset($employee) ? '' : 'required' }} minlength="6"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">เบอร์โทร</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">ตำแหน่ง</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $employee->is_admin ?? false))> เจ้าของ (สิทธิ์เต็ม)</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active ?? true))> ใช้งานได้</label>
            </div>

            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-6 py-2.5 text-sm transition">บันทึก</button>
                <a href="{{ route('admin.employees.index') }}" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-6 py-2.5 text-sm font-medium transition">ยกเลิก</a>
            </div>
        </form>
    </div>

@endsection
