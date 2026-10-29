@extends('layouts.auth')

@section('title', 'ตั้งรหัสผ่านใหม่ — PoSFigure')
@section('subtitle', 'ตั้งรหัสผ่านใหม่')

@section('content')
    <form action="{{ route('password.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            @error('password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <button class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 transition">
            บันทึกรหัสผ่านใหม่
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" class="text-sky-700 font-semibold hover:underline">← กลับไปหน้าเข้าสู่ระบบ</a>
    </p>
@endsection
