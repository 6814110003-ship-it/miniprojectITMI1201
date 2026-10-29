@extends('layouts.app')

@section('title', 'สินค้าทั้งหมด')
@section('content')

    <div class="flex gap-6">
        <aside class="hidden lg:block w-56 shrink-0">
            <div class="sticky top-20 space-y-5">
                <div>
                    <h3 class="font-bold text-sm mb-2">📂 หมวดสินค้า</h3>
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('products.index') }}"
                               class="{{ !request('category') ? 'text-sky-700 font-semibold' : 'text-gray-600 hover:text-sky-700' }}">
                                ทุกหมวด
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('products.index', ['category' => $category->id, ...request()->except('category')]) }}"
                                   class="{{ request('category') == $category->id ? 'text-sky-700 font-semibold' : 'text-gray-600 hover:text-sky-700' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-sm mb-2">⚡ สถานะ</h3>
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('products.index') }}"
                               class="{{ !request('status') ? 'text-sky-700 font-semibold' : 'text-gray-600 hover:text-sky-700' }}">
                                ทั้งหมด
                            </a>
                        </li>
                        @foreach(\App\Models\Product::STATUS_LABELS as $key => $label)
                            <li>
                                <a href="{{ route('products.index', ['status' => $key, ...request()->except('status')]) }}"
                                   class="{{ request('status') == $key ? 'text-sky-700 font-semibold' : 'text-gray-600 hover:text-sky-700' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold">สินค้าทั้งหมด</h1>
                <p class="text-sm text-gray-500">พบ {{ $products->total() }} รายการ</p>
            </div>

            <div class="grid gap-4 [grid-template-columns:repeat(auto-fill,250px)]">
                @forelse($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <p class="col-span-full text-center text-gray-500 py-16">ไม่พบสินค้าที่ค้นหา</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>

@endsection
