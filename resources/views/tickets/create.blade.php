@extends('layouts.app')

@section('title', 'แจ้งซ่อมอุปกรณ์ / ปัญหาไอทีใหม่')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Breadcrumb (No Icons) -->
    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-4">
        <a href="{{ route('tickets.index') }}" class="hover:text-blue-600 font-bold">&larr; รายการแจ้งซ่อม</a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-900 font-bold">สร้างคำขอแจ้งซ่อมใหม่</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50">
            <h1 class="text-xl font-black text-slate-900">แบบฟอร์มแจ้งซ่อมปัญหาไอที</h1>
            <p class="text-xs text-slate-500 mt-1">กรอกรายละเอียดปัญหาเพื่อส่งต่อไปยังทีมงานช่างเทคนิค</p>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Problem Title -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    หัวข้อปัญหา / อุปกรณ์ที่ชำรุด <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด, เครื่องพิมพ์กระดาษติด, อินเทอร์เน็ตใช้งานไม่ได้"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <p class="text-[11px] text-slate-400 mt-1">ระบุข้อความสั้นๆ ที่เข้าใจง่าย ชัดเจน</p>
            </div>

            <!-- Category & Priority -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        หมวดหมู่งานซ่อม <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        ระดับความสำคัญ / ความเร่งด่วน <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        @foreach($priorities as $priority)
                        <option value="{{ $priority->value }}" {{ old('priority', 'Medium') === $priority->value ? 'selected' : '' }}>
                            {{ $priority->label() }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    รายละเอียดอาการเสีย / ปัญหาที่พบ <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="5" required
                    placeholder="อธิบายอาการอย่างละเอียด เช่น ปัญหาเริ่มเกิดขึ้นตอนไหน, รหัส Error ที่แสดง, สถานที่ตั้งอุปกรณ์หรือโต๊ะทำงาน..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none leading-relaxed">{{ old('description') }}</textarea>
            </div>

            <!-- Image Upload with Instant Preview -->
            <div x-data="{ imageUrl: null }">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    แนบรูปถ่ายประกอบ (อุปกรณ์ / หน้าจอ Error)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-blue-400 transition bg-slate-50 relative">
                    <div class="space-y-2 text-center" x-show="!imageUrl">
                        <div class="flex text-sm text-slate-600 justify-center">
                            <label for="image-upload" class="relative cursor-pointer px-4 py-2 rounded-xl bg-white border border-slate-300 shadow-sm font-bold text-blue-600 hover:bg-slate-50">
                                <span>เลือกรูปภาพจากเครื่อง</span>
                                <input id="image-upload" name="image" type="file" accept="image/*" class="sr-only"
                                    @change="const file = $event.target.files[0]; if (file) { imageUrl = URL.createObjectURL(file) }">
                            </label>
                        </div>
                        <p class="text-xs text-slate-400">รองรับไฟล์ PNG, JPG, JPEG, WEBP ขนาดไม่เกิน 5MB</p>
                    </div>

                    <!-- Image Preview Container -->
                    <div x-show="imageUrl" x-cloak class="relative max-w-xs">
                        <img :src="imageUrl" class="rounded-xl max-h-56 object-cover shadow-sm border border-slate-200">
                        <button type="button" @click="imageUrl = null; document.getElementById('image-upload').value = ''"
                            class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full px-2 py-0.5 text-xs font-bold hover:bg-rose-700 shadow">
                            ลบรูป
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('tickets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    ยกเลิก
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    ส่งคำขอแจ้งซ่อม
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
