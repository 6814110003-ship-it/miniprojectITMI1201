@extends('layouts.app')

@section('title', 'วิธีการสั่งซื้อ / จัดส่ง')
@section('content')

    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">วิธีการสั่งซื้อ / จัดส่ง</h1>

        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h2 class="font-bold text-lg mb-3">การสั่งซื้อสินค้า</h2>
            <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                <li><strong>สินค้าพรี-ออเดอร์</strong> — เปิดรับจองเพื่อสั่งการผลิตจากโรงงาน ควรสั่งจองล่วงหน้า เพราะโรงงานผลิตตามยอดจองเท่านั้น</li>
                <li><strong>สินค้าเข้าร้าน</strong> — สินค้าที่โรงงานผลิตเสร็จส่งมาถึงร้าน พร้อมจัดส่งให้ลูกค้า</li>
            </ul>
            <p class="mt-3 text-sm bg-amber-50 border border-amber-200 rounded-lg p-3 text-amber-800">
                ** หลังสั่งซื้อเสร็จ รออีเมลยืนยันคำสั่งซื้อจากทางร้านก่อน แล้วจึงโอนชำระเงินมัดจำ **
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h2 class="font-bold text-lg mb-3">เงื่อนไขการสั่งจอง</h2>
            <p class="text-sm text-gray-700">
                ต้องวางเงินมัดจำตามยอดที่ระบุไว้ในสินค้าแต่ละชิ้น และชำระส่วนที่เหลือเมื่อสินค้ามาถึงพร้อมส่ง
            </p>
            <p class="text-sm text-gray-700 mt-2">อย่าลืมเก็บหลักฐานการโอนเงินไว้ด้วย</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h2 class="font-bold text-lg mb-3">การจัดส่งสินค้า</h2>
            <p class="text-sm text-gray-700">
                ทางร้านจัดส่งทาง EMS / Kerry / Flash Express / J&T ทั่วประเทศ คิดค่าส่ง 50 - 120 บาท ตามน้ำหนักต่อตัว (สินค้าประเภทการ์ดลดค่าส่ง 50 บาท)
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="font-bold text-lg mb-3">ขั้นตอนการแจ้งชำระเงิน</h2>
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                <li>โอนเงินเข้าบัญชีธนาคารของร้าน (มัดจำ หรือ ค่าที่เหลือ)</li>
                <li>เข้าเมนู <a href="{{ route('member.orders') }}" class="text-sky-700 hover:underline">ออเดอร์ของฉัน</a> แล้วกดแจ้งชำระเงิน</li>
                <li>กรอกข้อมูลการโอน + อัปโหลดสลิป</li>
                <li>พนักงานตรวจสอบสลิปแล้วอนุมัติ ระบบจะอัปเดตสถานะออเดอร์</li>
            </ol>
        </div>
    </div>

@endsection
