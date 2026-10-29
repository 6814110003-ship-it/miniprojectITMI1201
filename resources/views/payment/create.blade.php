@extends('layouts.app')

@section('title', 'แจ้งชำระเงิน')
@section('content')

    <h1 class="text-2xl font-bold mb-6">แจ้งชำระเงิน</h1>

    <form action="{{ route('member.payments.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 max-w-2xl">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">เลือกออเดอร์</label>
            <select name="order_id" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                <option value="">-- เลือกออเดอร์ --</option>
                @foreach($orders as $order)
                    @php
                        $isDeposit = $order->paidAmount() === 0 && $order->isPreorder();
                        $payNow = $order->amountDueNow();
                    @endphp
                    <option value="{{ $order->id }}"
                            @selected(isset($selectedOrder) && $selectedOrder->id === $order->id)
                            data-due="{{ $payNow }}"
                            data-stage="{{ $isDeposit ? 'deposit' : 'balance' }}"
                            data-remaining="{{ $order->dueAmount() - $payNow }}"
                            data-due-date="{{ $isDeposit ? '' : $order->balance_due_date?->format('d/m/Y') }}">
                        {{ $order->order_no }} — รวม ฿{{ number_format($order->total) }} / ชำระแล้ว ฿{{ number_format($order->paidAmount()) }} / {{ $isDeposit ? 'ชำระตอนนี้' : 'คงเหลือ' }} ฿{{ number_format($payNow) }}{{ ! $isDeposit && $order->balance_due_date ? ' / กำหนด '.$order->balance_due_date->format('d/m/Y') : '' }}
                    </option>
                @endforeach
            </select>
            <p id="order-due-hint" class="text-xs text-amber-600 mt-1"></p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">จำนวนเงินที่โอน</label>
                <input type="number" name="amount" id="payment-amount" min="1" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ธนาคารที่โอน</label>
                <input type="text" name="bank" placeholder="กสิกรไทย, ไทยพาณิชย์, กรุงเทพ" required class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">วันที่-เวลาที่โอน</label>
            <input type="datetime-local" name="transferred_at" value="{{ now()->format('Y-m-d\TH:i') }}" required
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">อัปโหลดสลิปโอนเงิน (JPG / PNG / WEBP ไม่เกิน 4 MB)</label>
            <input type="file" name="slip" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm">
            @error('slip')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ (ไม่บังคับ)</label>
            <textarea name="note" rows="2" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
        </div>

        <button class="mt-6 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg px-6 py-2.5 transition">
            แจ้งชำระเงิน
        </button>
    </form>

    <script>
        (function () {
            const orderSelect = document.querySelector('select[name="order_id"]');
            const amountInput = document.getElementById('payment-amount');
            const hint = document.getElementById('order-due-hint');

            function update() {
                const option = orderSelect.options[orderSelect.selectedIndex];
                if (!option || !option.value) {
                    hint.textContent = '';
                    return;
                }
                const due = option.dataset.due;
                const dueDate = option.dataset.dueDate;
                const stage = option.dataset.stage;
                const remaining = option.dataset.remaining;
                if (due) {
                    if (stage === 'deposit') {
                        hint.textContent = 'ยอดที่ต้องชำระตอนนี้ ฿' + Number(due).toLocaleString('en-US')
                            + ' • ยอดคงเหลือภายหลัง ฿' + Number(remaining).toLocaleString('en-US');
                    } else {
                        hint.textContent = 'ยอดคงเหลือ ฿' + Number(due).toLocaleString('en-US')
                            + (dueDate ? ' • กำหนด ' + dueDate : '');
                    }
                    if (amountInput) {
                        amountInput.value = due;
                    }
                }
            }

            if (orderSelect) {
                orderSelect.addEventListener('change', update);
                update();
            }
        })();
    </script>
@endsection