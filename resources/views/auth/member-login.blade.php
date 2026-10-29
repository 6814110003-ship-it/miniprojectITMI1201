@extends('layouts.auth')

@section('title', 'เข้าสู่ระบบ — PoSFigure')
@section('subtitle', 'เข้าสู่ระบบสมาชิก')

@section('content')
    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div class="text-right">
            <a href="{{ route('password.request') }}" class="text-xs text-sky-700 hover:underline">ลืมรหัสผ่าน?</a>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            จดจำฉันไว้
        </label>

        <button class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 transition">
            เข้าสู่ระบบ
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        ยังไม่มีบัญชี?
        <a href="{{ route('register.member') }}" class="text-sky-700 font-semibold hover:underline">สมัครสมาชิก</a>
        <span class="mx-2">•</span>
        <a href="{{ route('admin.login') }}" class="text-gray-500 hover:underline">ล็อกอินพนักงาน</a>
    </p>
@endsection
