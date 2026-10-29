@extends('layouts.app')

@section('title', 'ตะกร้าสินค้า')
@section('content')

    <h1 class="text-2xl font-bold mb-6">ตะกร้าสินค้า</h1>

    @if(empty($items))
        <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
            <p class="text-4xl mb-3">🛒</p>
            <p class="text-gray-600 mb-4">ตะกร้าของคุณยังว่างอยู่</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg px-6 py-2.5 transition">
                ไปเลือกสินค้า
            </a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-3">
                @foreach($items as $item)
                    @php($product = $item['product'])
                    <div class="bg-white rounded-xl shadow-sm border p-4 flex gap-4">
                        <a href="{{ route('products.show', $product) }}" class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden shrink-0 flex items-center justify-center">
                            @if($product->coverImage())
                                <img src="{{ asset('storage/'.$product->coverImage()) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-2xl text-gray-300">🕹️</span>
                            @endif
                        </a>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $product) }}" class="font-semibold text-sm hover:text-sky-700 line-clamp-2">{{ $product->name }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($product->price) }} บาท{{ $product->isPreorder() ? ' / มัดจำ '.number_format($product->deposit).' บาท' : '' }}</p>

                            <div class="flex items-center gap-3 mt-3">
                                <form action="{{ route('cart.update', $product->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="qty" min="1" max="99" value="{{ $item['qty'] }}"
                                           class="w-16 rounded-lg border border-gray-300 px-2 py-1 text-sm text-center">
                                    <button class="text-xs bg-gray-100 hover:bg-gray-200 rounded px-3 py-1.5">อัปเดต</button>
                                </form>

                                <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                                    @csrf
                                    <button class="text-xs text-sky-600 hover:underline">ลบ</button>
                                </form>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="font-bold text-sky-700">฿{{ number_format($product->price * $item['qty']) }}</p>
                            @if($product->isPreorder())
                                <p class="text-xs text-amber-600">มัดจำ ฿{{ number_format($product->deposit * $item['qty']) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5 h-fit">
                <h2 class="font-bold mb-4">สรุปตะกร้า</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">รวมสินค้า</dt><dd>฿{{ number_format($subtotal) }}</dd></div>
                    @if($depositTotal > 0)
                        <div class="flex justify-between"><dt class="text-gray-500">มัดจำที่ต้องชำระตอนนี้</dt><dd class="text-amber-600 font-semibold">฿{{ number_format($depositTotal) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">ค่าส่ง</dt><dd>คำนวณหน้าเช็คเอาต์</dd></div>
                </dl>

                @auth
                    <a href="{{ route('checkout.index') }}" class="block text-center bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 mt-5 transition">
                        ไปชำระเงิน
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 mt-5 transition">
                        เข้าสู่ระบบเพื่อสั่งซื้อ
                    </a>
                @endauth

                <a href="{{ route('products.index') }}" class="block text-center text-gray-500 text-sm mt-3 hover:underline">
                    ← เลือกสินค้าเพิ่ม
                </a>
            </div>
        </div>
    @endif

@endsection
