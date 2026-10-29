@extends('layouts.app')

@section('title', 'หน้าแรก - ร้านโมเดลฟิกเกอร์คุณภาพส่งทั่วไทย')
@section('content')

    <section class="bg-gradient-to-r from-sky-700 to-sky-600 text-white rounded-2xl p-8 md:p-12 mb-10 shadow-lg">
        <div class="max-w-2xl">
            <h1 class="text-3xl md:text-4xl font-black mb-3">
                รวมโมเดล ฟิกเกอร์ และการ์ดคุณภาพจากญี่ปุ่น พรีออเดอร์-สินค้าพร้อมส่ง
            </h1>
            <p class="text-sky-100 mb-4">
                สินค้าแท้ 100% นำเข้าจากญี่ปุ่น จัดส่งตรงถึงบ้านคุณ
                บริการจัดส่ง EMS / Kerry / Flash Express ทั่วประเทศ
            </p>
            <p class="text-sm text-sky-200/90 mb-4">
                *เว็บนี้สร้างเพื่อใช้สำหรับการส่งงานให้กับอาจารย์ ไม่ได้มีเจตนาหลอกลวงหรือก็อปของคนอื่นมาขาย*
            </p>
            <div class="flex gap-3">
                <a href="{{ route('products.index') }}" class="bg-yellow-400 hover:bg-yellow-500 text-sky-800 font-bold px-6 py-2.5 rounded-lg transition">
                    ดูสินค้าทั้งหมด
                </a>
                <a href="{{ route('pages.howtoorder') }}" class="bg-white/20 hover:bg-white/30 font-semibold px-6 py-2.5 rounded-lg transition">
                    ดูวิธีการสั่งซื้อ
                </a>
            </div>
        </div>
    </section>

    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">สินค้ามาใหม่</h2>
            <a href="{{ route('products.index') }}" class="text-sm text-sky-700 hover:underline">ดูสินค้าทั้งหมด →</a>
        </div>

        <div class="grid gap-4 [grid-template-columns:repeat(auto-fill,220px)]">
            @forelse($newProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <p class="col-span-full text-gray-500 text-sm">ยังไม่มีสินค้าในขณะนี้</p>
            @endforelse
        </div>
    </section>

    @if($featuredProducts->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">สินค้าแนะนำ</h2>
            </div>

            <div class="grid gap-4 [grid-template-columns:repeat(auto-fill,220px)]">
                @foreach($featuredProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-gray-900 text-white rounded-2xl p-8 grid gap-6 md:grid-cols-3">
        <div>
            <h3 class="font-bold text-yellow-400 mb-2">รับประกันคุณภาพ-สินค้าแท้</h3>
            <p class="text-sm text-gray-300">สินค้าทุกชิ้นเป็นของแท้ 100% นำเข้าจากผู้ผลิตโดยตรง ตรวจสอบก่อนจัดส่งทุกครั้ง จัดส่งด้วย EMS / Kerry / Flash Express ทั่วประเทศ</p>
        </div>
        <div>
            <h3 class="font-bold text-yellow-400 mb-2">ตรวจสอบแล้ว 100%</h3>
            <p class="text-sm text-gray-300">ทุกสินค้าผ่านการตรวจสอบก่อนจัดส่ง ตรวจสอบบรรจุภัณฑ์และสภาพสินค้าก่อนส่งถึงมือลูกค้า</p>
        </div>
        <div>
            <h3 class="font-bold text-yellow-400 mb-2">จัดส่งทั่วประเทศ</h3>
            <p class="text-sm text-gray-300">EMS / Kerry / Flash Express จัดส่งฟรี 100 - 120 บาท ทั่วประเทศไทย</p>
        </div>
    </section>

@endsection
