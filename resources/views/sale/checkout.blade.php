@extends('layouts.app')

@section('title', 'เช็คเอาต์')
@section('content')

    <h1 class="text-2xl font-bold mb-6">เช็คเอาต์</h1>

    <form action="{{ route('checkout.store') }}" method="POST" class="grid gap-6 lg:grid-cols-3">
        @csrf

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h2 class="font-bold mb-4">ข้อมูลผู้รับ</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $member->name) }}" required
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $member->phone) }}" required
                               class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่จัดส่ง</label>
                    <textarea name="shipping_address" rows="3" required
                              class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">{{ old('shipping_address', $member->address) }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h2 class="font-bold mb-4">ช่องทางจัดส่ง</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach(['ems' => 'EMS', 'kerry' => 'Kerry', 'flash' => 'Flash Express', 'jnt' => 'J&T'] as $key => $label)
                        <label class="border-2 rounded-lg p-3 flex items-center gap-2 cursor-pointer
                                      {{ old('shipping_method', 'kerry') === $key ? 'border-sky-600' : 'border-gray-200' }}">
                            <input type="radio" name="shipping_method" value="{{ $key }}"
                                   data-fee="{{ $shippingFees[$key] }}"
                                   {{ old('shipping_method', 'kerry') === $key ? 'checked' : '' }} class="accent-sky-700 shipping-radio">
                            <span class="text-sm">{{ $label }} ({{ $shippingFees[$key] }} บาท)</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h2 class="font-bold mb-4">รายการสินค้า</h2>
                <div class="space-y-2 text-sm">
                    @foreach($items as $item)
                        <div class="flex justify-between gap-4">
                            <span class="flex-1 min-w-0 truncate">
                                {{ $item['product']->name }} × {{ $item['qty'] }}
                                <span class="text-gray-400">({{ $item['product']->status === 'preorder' ? 'พรี-ออเดอร์' : 'ของเข้า' }})</span>
                            </span>
                            <span class="font-medium">฿{{ number_format($item['product']->price * $item['qty']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ (ไม่บังคับ)</label>
                <textarea name="note" rows="2" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5 h-fit">
            <h2 class="font-bold mb-4">สรุปยอด</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">รวมสินค้า</dt><dd>฿{{ number_format($subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">ค่าส่ง</dt><dd id="shipping-fee-display">฿{{ number_format($shippingFee) }}</dd></div>
                <div class="flex justify-between border-t pt-2 font-bold"><dt>ยอดรวม</dt><dd id="total-display">฿{{ number_format($total) }}</dd></div>
                @if($depositTotal > 0)
                    <div class="flex justify-between text-amber-600">
                        <dt>ชำระตอนนี้{{ $initialPayment > $depositTotal ? ' (สินค้าพร้อมส่ง + มัดจำ)' : ' (มัดจำ)' }}</dt>
                        <dd>฿{{ number_format($initialPayment) }}</dd>
                    </div>
                    <div class="flex justify-between"><dt>ส่วนที่เหลือจ่ายตอนสินค้าเข้า</dt><dd>฿{{ number_format($total - $initialPayment) }}</dd></div>
                    @if($balanceDueDate)
                        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            กำหนดเรียกเก็บยอดคงเหลือ: {{ $balanceDueDate->format('d/m/Y') }}
                        </p>
                    @endif
                @endif
            </dl>

            <button class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 mt-5 transition">
                ยืนยันสั่งซื้อ
            </button>
        </div>
    </form>

    <script>
        (function () {
            const subtotal = {{ (int) $subtotal }};
            const feeDisplay = document.getElementById('shipping-fee-display');
            const totalDisplay = document.getElementById('total-display');
            const radios = document.querySelectorAll('.shipping-radio');

            function update() {
                const checked = document.querySelector('.shipping-radio:checked');
                if (!checked) return;
                const fee = parseInt(checked.dataset.fee, 10) || 0;
                feeDisplay.textContent = '฿' + fee.toLocaleString('en-US');
                totalDisplay.textContent = '฿' + (subtotal + fee).toLocaleString('en-US');
            }

            radios.forEach(function (r) {
                r.addEventListener('change', update);
            });
        })();
    </script>

@endsection
