@extends('layouts.auth')

@section('title', 'ลืมรหัสผ่าน — PoSFigure')
@section('subtitle', 'ลืมรหัสผ่าน')

@section('content')
    <p class="text-sm text-gray-600 mb-4">
        กรอกอีเมลที่ใช้สมัครสมาชิก ระบบจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้ทางอีเมล
    </p>

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 transition">
            ส่งลิงก์รีเซ็ตรหัสผ่าน
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" class="text-sky-700 font-semibold hover:underline">← กลับไปหน้าเข้าสู่ระบบ</a>
    </p>
@endsection
