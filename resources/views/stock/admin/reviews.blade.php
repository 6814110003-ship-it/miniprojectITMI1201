@extends('layouts.admin')

@section('title', 'รีวิวสินค้า')
@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-bold">รีวิวสินค้า</h2>
            <p class="text-sm text-gray-500">พบ {{ $reviews->total() }} รายการ</p>
        </div>
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาสินค้า/สมาชิก"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกสถานะ</option>
                @foreach(\App\Models\ProductReview::STATUS_LABELS as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="rating" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                <option value="">ทุกคะแนน</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>{{ $i }} ดาว</option>
                @endfor
            </select>
            <button class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 text-sm">กรอง</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($reviews as $review)
            <div class="bg-white rounded-xl shadow-sm border p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-amber-500 font-semibold">{{ $review->stars() }}</span>
                            <span class="text-xs font-bold px-2 py-1 rounded
                                @if($review->status === 'approved') bg-green-100 text-green-700
                                @elseif($review->status === 'rejected') bg-gray-200 text-gray-600
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $review->statusLabel() }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $review->created_at?->format('d/m/Y H:i') }}</span>
                        </div>

                        <p class="mt-2 font-semibold text-gray-800">
                            <a href="{{ route('admin.products.edit', $review->product_id) }}" class="hover:text-sky-600">
                                {{ $review->product?->name ?? '(สินค้าถูกลบ)' }}
                            </a>
                            @if($review->product?->code)
                                <span class="text-xs font-normal text-gray-400">({{ $review->product->code }})</span>
                            @endif
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            รีวิวโดย: {{ $review->member?->name ?? 'สมาชิก' }}
                            @if($review->member?->email)
                                ({{ $review->member->email }})
                            @endif
                            @if($review->order_id)
                                · ออเดอร์ #{{ $review->order_id }}
                            @endif
                        </p>

                        @if($review->comment)
                            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line bg-gray-50 border border-gray-200 rounded px-3 py-2">
                                {{ $review->comment }}
                            </p>
                        @endif

                        @if($review->admin_note)
                            <p class="mt-2 text-xs bg-gray-50 border border-gray-200 rounded px-2 py-1 text-gray-600">
                                <strong>หมายเหตุผู้ดูแล:</strong> {{ $review->admin_note }}
                            </p>
                        @endif
                    </div>

                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="w-full sm:w-72 shrink-0">
                        @csrf @method('PATCH')
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none mb-2">
                            @foreach(\App\Models\ProductReview::STATUS_LABELS as $key => $label)
                                <option value="{{ $key }}" @selected($review->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="admin_note" value="{{ $review->admin_note }}" placeholder="หมายเหตุผู้ดูแล (ไม่บังคับ)"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <button class="w-full bg-sky-600 hover:bg-sky-700 text-white rounded-lg px-4 py-2 text-sm font-medium">
                            บันทึก
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border p-10 text-center text-gray-400">
                ยังไม่มีรีวิวสินค้า
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $reviews->links() }}</div>

@endsection
