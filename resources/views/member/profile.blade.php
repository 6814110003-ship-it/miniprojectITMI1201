@extends('layouts.app')

@section('title', 'โปรไฟล์ของฉัน')
@section('content')

    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">จัดการโปรไฟล์</h1>

        <form action="{{ route('member.profile.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                <input type="text" name="name" value="{{ $member->name }}" required
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                <input type="email" name="email" value="{{ $member->email }}" required
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                <input type="text" name="phone" value="{{ $member->phone }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่</label>
                <textarea name="address" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ $member->address }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่ (เว้นว่างถ้าไม่ต้องการเปลี่ยน)</label>
                <input type="password" name="password"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <button class="bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg px-6 py-2.5 transition">
                บันทึก
            </button>
        </form>
    </div>

@endsection
