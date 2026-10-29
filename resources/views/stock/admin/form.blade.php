@extends('layouts.admin')

@section('title', isset($product) ? 'แก้ไขสินค้า' : 'เพิ่มสินค้า')
@section('content')

    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">{{ isset($product) ? 'แก้ไขสินค้า: '.$product->name : 'เพิ่มสินค้าใหม่' }}</h2>
            <div class="flex items-center gap-3">
                @if(isset($product) && $product->reports()->where('status', \App\Models\ProductReport::STATUS_PENDING)->exists())
                    <a href="{{ route('admin.reports.index', ['q' => $product->code ?: $product->name]) }}"
                       class="text-sm text-red-600 hover:underline">🚩 ดูรายงานปัญหาสินค้านี้</a>
                @endif
                <a href="{{ route('admin.products.index') }}" class="text-sm text-sky-600 hover:underline">← กลับ</a>
            </div>
        </div>

        <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
              method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
            @csrf
            @if(isset($product)) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">ชื่อสินค้า *</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">รหัสสินค้า</label>
                    <input type="text" name="code" value="{{ old('code', $product->code ?? '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">หมวดหมู่</label>
                    <select name="category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        <option value="">— ไม่ระบุ —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">รายละเอียด</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">ราคา (บาท) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" required min="0"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">มัดจำ (บาท) *</label>
                    <input type="number" name="deposit" value="{{ old('deposit', $product->deposit ?? '') }}" required min="0"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">สต็อก *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">สถานะ *</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                        @foreach(\App\Models\Product::STATUS_LABELS as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $product->status ?? 'instock') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">วันวางจำหน่าย</label>
                    <input type="date" name="release_date" value="{{ old('release_date', isset($product) && $product->release_date ? $product->release_date->format('Y-m-d') : '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">ปิดรับพรี-ออเดอร์</label>
                    <input type="date" name="preorder_deadline" value="{{ old('preorder_deadline', isset($product) && $product->preorder_deadline ? $product->preorder_deadline->format('Y-m-d') : '') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ขนาด</label>
                <input type="text" name="size" value="{{ old('size', $product->size ?? '') }}" placeholder="เช่น 15cm"
                       class="w-full sm:w-64 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new ?? false))> NEW</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))> สินค้าแนะนำ</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))> แสดงหน้าร้าน</label>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">แท็ก</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    @php($selectedTags = old('tags', isset($product) ? $product->tags->pluck('name')->all() : []))
                    @foreach($tags as $tag)
                        <label class="inline-flex items-center gap-1 text-sm border rounded-lg px-2 py-1 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="tags[]" value="{{ $tag->name }}" @checked(in_array($tag->name, $selectedTags))>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
                <input type="text" name="new_tags" value="{{ old('new_tags') }}" placeholder="แท็กใหม่ (พิมพ์คั่นด้วย ,)"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">รูปภาพ (เลือกได้หลายรูป)</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                @if(isset($product) && $product->images->isNotEmpty())
                    <div class="flex gap-2 mt-2 flex-wrap">
                        @foreach($product->images as $image)
                            <div class="relative w-16 h-16 rounded overflow-hidden border">
                                <img src="{{ asset('storage/'.$image->path) }}" alt="" class="w-full h-full object-cover">
                                <button type="submit" form="{{ 'del-img-'.$image->id }}" onclick="return confirm('ลบรูปนี้?')"
                                        class="absolute top-0 right-0 bg-red-600 text-white text-xs w-4 h-4 leading-4 text-center">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-3 pt-2 border-t">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg px-6 py-2.5 text-sm transition">
                    {{ isset($product) ? 'บันทึกการแก้ไข' : 'สร้างสินค้า' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-6 py-2.5 text-sm font-medium transition">ยกเลิก</a>
            </div>
        </form>

        @if(isset($product) && $product->images->isNotEmpty())
            @foreach($product->images as $image)
                <form id="del-img-{{ $image->id }}" action="{{ route('admin.products.images.delete', $image) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        @endif
    </div>

@endsection
