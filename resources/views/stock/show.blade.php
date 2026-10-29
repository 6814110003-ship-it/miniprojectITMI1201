@extends('layouts.app')

@section('title', $product->name)
@section('content')

    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-sky-700">หน้าแรก</a> ›
        <a href="{{ route('products.index') }}" class="hover:text-sky-700">สินค้า</a> ›
        @if($product->category)
            <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="hover:text-sky-700">{{ $product->category->name }}</a> ›
        @endif
        <span class="text-gray-700">{{ Str::limit($product->name, 40) }}</span>
    </nav>

    <div class="grid gap-8 md:grid-cols-2">
        <div>
            @if($product->images->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->name }}"
                         class="w-full aspect-square object-contain rounded-lg" id="mainImage">
                </div>
                <div class="grid grid-cols-5 gap-2 mt-3">
                    @foreach($product->images as $image)
                        <button type="button" onclick="document.getElementById('mainImage').src='{{ asset('storage/'.$image->path) }}'"
                                class="bg-white border rounded-lg overflow-hidden hover:ring-2 hover:ring-sky-600 focus:outline-none">
                            <img src="{{ asset('storage/'.$image->path) }}" alt="" class="w-full h-16 object-cover">
                        </button>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border flex items-center justify-center aspect-square text-gray-300 text-6xl">
                    🕹️
                </div>
            @endif
        </div>

        <div>
            <p class="text-sm text-gray-500 mb-1">รหัสสินค้า: {{ $product->code }}</p>
            <h1 class="text-2xl font-bold mb-3">{{ $product->name }}</h1>

            @if($product->is_new)
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded">NEW</span>
            @endif
            @if($product->status === 'preorder')
                <span class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded">พรี-ออเดอร์</span>
            @elseif($product->status === 'instock')
                <span class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded">มีของ / เข้าร้าน</span>
            @else
                <span class="bg-gray-600 text-white text-xs font-bold px-2 py-0.5 rounded">หมด / ปิดรับจอง</span>
            @endif

            <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-2 text-sm border-t border-b py-4">
                <div class="flex justify-between"><dt class="text-gray-500">ราคา</dt><dd class="font-bold text-sky-700 text-lg">฿{{ number_format($product->price) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">สถานะ</dt><dd>{{ $product->statusLabel() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">หมวดสินค้า</dt><dd>{{ $product->category?->name ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">ขนาด</dt><dd>{{ $product->size ?? '-' }}</dd></div>
                @if($product->status === 'instock')
                    <div class="flex justify-between"><dt class="text-gray-500">สต็อกคงเหลือ</dt><dd>{{ $product->stock }} ชิ้น</dd></div>
                @endif
                @if($product->status === 'preorder')
                    <div class="flex justify-between"><dt class="text-gray-500">มัดจำ</dt><dd class="font-semibold text-amber-600">฿{{ number_format($product->deposit) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">วันผลิตเสร็จ</dt><dd>{{ optional($product->release_date)->format('M Y') ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">รับจองถึง</dt><dd>{{ optional($product->preorder_deadline)->format('d M Y') ?? '-' }}</dd></div>
                @endif
            </dl>

            @if($product->status !== 'soldout')
                <form action="{{ route('cart.add') }}" method="POST" class="mt-5 flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="number" name="qty" min="1" max="{{ $product->status === 'preorder' ? 99 : $product->stock }}" value="1"
                           class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm">
                    <button class="flex-1 bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg py-2.5 px-4 transition">
                        🛒 {{ $product->status === 'preorder' ? 'สั่งจองสินค้า' : 'เพิ่มลงตะกร้า' }}
                    </button>
                </form>
            @else
                <p class="mt-5 bg-gray-100 border border-gray-300 rounded-lg py-2.5 text-center text-gray-500 font-semibold">
                    สินค้าชิ้นนี้หมดหรือปิดรับจองแล้ว
                </p>
            @endif

            <div class="mt-5 bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                💡 <strong>วิธีสั่งซื้อ:</strong> เพิ่มสินค้าเข้ากล่อง → ไปหน้าเช็คเอาต์ → ชำระ <strong>มัดจำ</strong>
                ({{ $product->status === 'preorder' ? '฿'.number_format($product->deposit) : 'เต็มจำนวน' }}) แล้วแจ้งโอนเงิน รอสินค้าเข้าครบชำระส่วนที่เหลือ
            </div>

            <div class="mt-4 flex items-center gap-3">
                @auth
                    <button type="button" onclick="document.getElementById('reportModal').classList.remove('hidden')"
                            class="text-sm text-gray-500 hover:text-red-600 transition underline underline-offset-2">
                        🚩 รายงานสินค้านี้ (ข้อมูล / รูป / ราคาไม่ถูกต้อง)
                    </button>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-red-600 transition underline underline-offset-2">
                        🚩 รายงานสินค้านี้ (ข้อมูล / รูป / ราคาไม่ถูกต้อง)
                    </a>
                    <span class="text-xs text-gray-400">— ต้องเข้าสู่ระบบก่อน</span>
                @endauth
            </div>
        </div>
    </div>

    @auth
        <div id="reportModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-5 py-3 border-b">
                    <h3 class="font-bold">รายงานสินค้า: {{ Str::limit($product->name, 40) }}</h3>
                    <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-700 text-xl leading-none">×</button>
                </div>

                <form action="{{ route('products.report', $product) }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ปัญหาที่พบ * <span class="text-xs text-gray-400 font-normal">(เลือกได้หลายข้อ)</span></label>
                        <div class="space-y-2">
                            @foreach(\App\Models\ProductReport::REASON_LABELS as $key => $label)
                                <label class="flex items-start gap-2 text-sm">
                                    <input type="checkbox" name="reasons[]" value="{{ $key }}"
                                           id="reason_{{ $key }}"
                                           @checked(in_array($key, old('reasons', []), true))
                                           class="mt-0.5 accent-red-600">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('reasons')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @error('reasons.*')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        <div id="otherReasonBox" class="{{ in_array(\App\Models\ProductReport::REASON_OTHER, old('reasons', []), true) ? 'mt-3 pl-6' : 'hidden mt-3 pl-6' }}">
                            <label class="block text-xs font-medium text-gray-600 mb-1">ระบุข้อผิดพลาดอื่น ๆ ที่พบ *</label>
                            <textarea name="other_detail" rows="2" maxlength="1000"
                                      placeholder="เช่น สินค้าถูกระบุว่าเป็นของแท้ แต่ภาพเป็นของก๊อปปี้"
                                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:outline-none">{{ old('other_detail') }}</textarea>
                            @error('other_detail')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดเพิ่มเติม</label>
                        <textarea name="detail" rows="3" maxlength="2000"
                                  placeholder="เช่น ราคาจริงในหน้าร้าน 1,500 บาท ไม่ใช่ 2,000 บาท"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:outline-none">{{ old('detail') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg py-2.5 text-sm transition">
                            ส่งรายงาน
                        </button>
                        <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')"
                                class="bg-gray-200 hover:bg-gray-300 rounded-lg px-5 py-2.5 text-sm font-medium transition">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>

        @if($errors->any())
            <script>
                document.getElementById('reportModal').classList.remove('hidden');
            </script>
        @endif

        <script>
            (function () {
                const other = document.getElementById('reason_other');
                const box = document.getElementById('otherReasonBox');
                if (!other || !box) return;

                const sync = () => box.classList.toggle('hidden', !other.checked);
                other.addEventListener('change', sync);
                sync();

                @if($errors->any())
                    box.classList.toggle('hidden', !other.checked);
                @endif
            })();
        </script>
    @endauth

    @if($product->description)
        <section class="mt-10 bg-white rounded-xl shadow-sm border p-6">
            <h2 class="font-bold text-lg mb-3">รายละเอียดสินค้า</h2>
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</div>
        </section>
    @endif

    <section class="mt-10 bg-white rounded-xl shadow-sm border p-6">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-bold text-lg">รีวิวสินค้า</h2>
            @if($reviewCount > 0)
                <p class="text-sm font-semibold text-amber-500">
                    {{ str_repeat('★', (int) round($reviewAvg)) }}{{ str_repeat('☆', 5 - (int) round($reviewAvg)) }}
                    <span class="text-gray-500 font-normal">({{ number_format($reviewAvg, 1) }}/5 · {{ $reviewCount }} รีวิว)</span>
                </p>
            @endif
        </div>

        @if($reviews->isEmpty())
            <p class="text-sm text-gray-500 mb-5">ยังไม่มีรีวิวสำหรับสินค้านี้</p>
        @else
            <div class="space-y-4 mb-6">
                @foreach($reviews as $review)
                    <div class="border-b pb-3 last:border-0">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-amber-500">{{ $review->stars() }}</span>
                                <span class="text-sm font-medium ml-1">{{ $review->member?->name ?? 'สมาชิก' }}</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ $review->created_at?->format('d/m/Y') }}</span>
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @auth
            @if($myReview)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-amber-500">{{ $myReview->stars() }}</span>
                        <span class="font-medium">รีวิวของคุณ</span>
                        <span class="text-xs px-2 py-0.5 rounded
                            @if($myReview->status === 'approved') bg-green-100 text-green-700
                            @elseif($myReview->status === 'rejected') bg-gray-200 text-gray-600
                            @else bg-amber-100 text-amber-700 @endif">
                            {{ $myReview->statusLabel() }}
                        </span>
                    </div>
                    @if($myReview->comment)
                        <p class="text-gray-700 mt-1 whitespace-pre-line">{{ $myReview->comment }}</p>
                    @endif
                </div>
            @elseif($canReview)
                <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="space-y-3 border-t pt-4">
                    @csrf
                    <h3 class="font-semibold text-sm text-gray-700">เขียนรีวิวสินค้านี้</h3>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ให้คะแนน *</label>
                        <select name="rating" required class="w-full sm:w-56 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            <option value="">— เลือกคะแนน —</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected((int) old('rating') === $i)>{{ $i }} ดาว {{ str_repeat('★', $i) }}</option>
                            @endfor
                        </select>
                        @error('rating')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">ความคิดเห็น</label>
                        <textarea name="comment" rows="3" maxlength="1000"
                                  placeholder="เล่าประสบการณ์หลังได้รับสินค้า..."
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="bg-sky-700 hover:bg-sky-800 text-white font-bold rounded-lg px-6 py-2 text-sm transition">
                        ส่งรีวิว
                    </button>
                    <p class="text-xs text-gray-400">รีวิวจะแสดงหลังทางร้านตรวจสอบ</p>
                </form>
            @else
                <p class="text-sm text-gray-500">คุณสามารถเขียนรีวิวได้เฉพาะสินค้าที่เคยสั่งซื้อเท่านั้น</p>
            @endif
        @else
            <p class="text-sm text-gray-500">
                <a href="{{ route('login') }}" class="text-sky-700 font-semibold hover:underline">เข้าสู่ระบบ</a>
                เพื่อเขียนรีวิวสินค้านี้
            </p>
        @endauth
    </section>


    @if($product->tags->isNotEmpty())
        <section class="mt-4">
            <h3 class="font-bold text-sm text-gray-600 mb-2">Tags:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($product->tags as $tag)
                    <a href="{{ route('products.search', ['q' => $tag->name]) }}"
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium px-3 py-1 rounded-full transition">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($related->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-xl font-bold mb-4">สินค้าที่เกี่ยวข้อง</h2>
            <div class="grid gap-4 [grid-template-columns:repeat(auto-fill,220px)]">
                @foreach($related as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

@endsection
