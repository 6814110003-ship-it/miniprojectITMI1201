@extends('layouts.auth')

@section('title', 'สมัครสมาชิก — PoSFigure')
@section('subtitle', 'สมัครบัญชีสมาชิกเพื่อสั่งซื้อฟิกเกอร์')

@section('content')
    <form action="{{ route('register.member.submit') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08x-xxx-xxxx"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่ (สำหรับจัดส่ง)</label>
            <textarea name="address" rows="3" placeholder="บ้านเลขที่ ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์"
                      class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ old('address') }}</textarea>
        </div>

        <button class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 transition">
            สมัครสมาชิก
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        มีบัญชีอยู่แล้ว?
        <a href="{{ route('login') }}" class="text-sky-700 font-semibold hover:underline">เข้าสู่ระบบ</a>
    </p>
@endsection
