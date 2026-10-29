@extends('layouts.app')

@section('title', 'ติดต่อเรา')
@section('content')

    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">ติดต่อเรา</h1>

        <div class="bg-white rounded-xl shadow-sm border p-6 grid gap-6 md:grid-cols-2">
            <div>
                <h2 class="font-bold mb-3">ช่องทางติดต่อ</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">อีเมล</dt>
                        <dd class="font-medium">admin@posfigure.test</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Facebook</dt>
                        <dd class="font-medium">facebook.com/posfigure</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">LINE</dt>
                        <dd class="font-medium">@posfigure</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">เบอร์โทร</dt>
                        <dd class="font-medium">02-123-4567 (เวลาทำการ จ-ส 10:00 - 19:00)</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="font-bold mb-3">ที่อยู่ร้าน</h2>
                <p class="text-sm text-gray-700 leading-relaxed">
                    PoSFigure Co., Ltd.<br>
                    123 ถนนสุขุมวิท แขวงคลองเตย<br>
                    เขตคลองเตย กรุงเทพฯ 10110
                </p>

                <h2 class="font-bold mt-6 mb-3">บัญชีรับโอน</h2>
                <p class="text-sm text-gray-700">
                    ธนาคารกสิกรไทย<br>
                    เลขบัญชี 000-0-00000-0<br>
                    ชื่อบัญชี บริษัท PoSFigure จำกัด
                </p>
            </div>
        </div>
    </div>

@endsection