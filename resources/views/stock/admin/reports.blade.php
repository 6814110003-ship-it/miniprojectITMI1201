@extends('layouts.admin')

@section('title', 'รายงานปัญหาสินค้า')
@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold">รายงานปัญหาสินค้า</h2>
            <p class="text-sm text-gray-500">พบ {{ $reports->total() }} รายการ</p>
        </div>
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อ/รหัสสินค้า"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\ProductReport::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="reason" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกปัญหา</option>
                @foreach(\App\Models\ProductReport::REASON_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('reason') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">กรอง</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($reports as $report)
            <div class="bg-white rounded-xl shadow-sm border p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($report->reasonList() as $reason)
                                <span class="text-xs font-bold px-2 py-1 rounded bg-red-100 text-red-700">
                                    {{ $reason }}
                                </span>
                            @endforeach
                            <span class="text-xs font-bold px-2 py-1 rounded
                                @if($report->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($report->status === 'resolved') bg-green-100 text-green-700
                                @else bg-gray-200 text-gray-600 @endif">
                                {{ $report->statusLabel() }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $report->created_at?->format('d/m/Y H:i') }}</span>
                        </div>

                        <p class="mt-2 font-semibold text-gray-800">
                            <a href="{{ route('admin.products.edit', $report->product_id) }}" class="hover:text-sky-600">
                                {{ $report->product?->name ?? '(สินค้าถูกลบ)' }}
                            </a>
                            @if($report->product?->code)
                                <span class="text-xs font-normal text-gray-400">({{ $report->product->code }})</span>
                            @endif
                        </p>

                        @if($report->other_detail)
                            <p class="mt-2 text-sm bg-amber-50 border border-amber-200 rounded px-3 py-2 text-amber-900">
                                <strong>รายละเอียดอื่น ๆ:</strong> {{ $report->other_detail }}
                            </p>
                        @endif

                        @if($report->detail)
                            <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $report->detail }}</p>
                        @endif

                        <p class="mt-1 text-xs text-gray-400">
                            แจ้งโดย: {{ $report->member?->name ?? 'ผู้เข้าชม' }}
                            @if($report->member?->email)
                                ({{ $report->member->email }})
                            @endif
                        </p>

                        @if($report->admin_note)
                            <p class="mt-2 text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1 text-gray-600">
                                <strong>ผู้ดูแลแก้ไข:</strong> {{ $report->admin_note }}
                                @if($report->employee)
                                    ({{ $report->employee->name }})
                                @endif
                            </p>
                        @endif
                    </div>

                    <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="w-full sm:w-72 shrink-0">
                        @csrf @method('PATCH')
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none mb-2">
                            @foreach(\App\Models\ProductReport::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" @selected($report->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="admin_note" value="{{ $report->admin_note }}" placeholder="หมายเหตุผู้ดูแล (ไม่บังคับ)"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <button class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded-lg px-4 py-2 text-sm font-medium">
                            บันทึกการจัดการ
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border p-10 text-center text-gray-400">
                ยังไม่มีรายงานปัญหาสินค้า
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>

@endsection
