@extends('layouts.auth')

@section('title', 'เข้าสู่ระบบพนักงาน — PoSFigure')
@section('subtitle', 'ระบบหลังบ้านสำหรับพนักงาน')

@section('content')
    <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            จดจำฉันไว้
        </label>

        <button class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-lg py-2.5 transition">
            เข้าสู่ระบบพนักงาน
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="hover:underline">← กลับหน้าล็อกอินสมาชิก</a>
    </p>
@endsection