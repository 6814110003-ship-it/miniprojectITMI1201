<a href="{{ route('products.show', $product) }}" class="group bg-white rounded-xl shadow-sm hover:shadow-lg border border-gray-100 overflow-hidden transition flex flex-col h-full">
    <div class="relative aspect-[7/5] bg-gray-100 overflow-hidden">
        @if($product->coverImage())
            <img src="{{ asset('storage/'.$product->coverImage()) }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300 text-4xl">🕹️</div>
        @endif

        <div class="absolute top-2 left-2 flex flex-col items-start gap-1">
            @if($product->is_new)
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded">NEW</span>
            @endif
            @if($product->status === 'preorder')
                <span class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded">พรี-ออเดอร์</span>
            @elseif($product->status === 'instock')
                <span class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded">ของเข้า</span>
            @else
                <span class="bg-gray-600 text-white text-xs font-bold px-2 py-0.5 rounded">หมด</span>
            @endif
        </div>
    </div>

    <div class="p-3 flex flex-col flex-1">
        <p class="text-xs text-gray-500">
            {{ $product->category?->name ?? '—' }}
        </p>
        <h3 class="font-semibold text-sm text-gray-800 line-clamp-2 mt-1">{{ $product->name }}</h3>

        <div class="mt-auto pt-2">
            <div class="text-sky-700 font-bold">฿{{ number_format($product->price) }}</div>
            @if($product->isPreorder())
                <div class="text-xs text-amber-600 mt-0.5">มัดจำ ฿{{ number_format($product->deposit) }}</div>
            @elseif($product->status === 'instock')
                <div class="text-xs text-gray-500 mt-0.5">คงเหลือ {{ $product->stock }} ชิ้น</div>
            @endif
        </div>
    </div>
</a>
