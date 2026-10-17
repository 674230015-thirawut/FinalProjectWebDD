@extends('layouts.app')

@section('title', 'จัดการหมวดหมู่งานซ่อม')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">จัดการหมวดหมู่งานซ่อม</h1>
            <p class="text-sm text-slate-500">กำหนดประเภทอุปกรณ์และปัญหาเพื่อให้ผู้แจ้งเลือกตอนแจ้งซ่อม</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Add Category Form -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 self-start">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i>
                <span>เพิ่มหมวดหมู่ใหม่</span>
            </h3>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ชื่อหมวดหมู่ *</label>
                    <input type="text" name="name" required placeholder="เช่น Hardware / ฮาร์ดแวร์"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">คำอธิบายเพิ่มเติม</label>
                    <textarea name="description" rows="3" placeholder="ระบุขอบเขตของหมวดหมู่นี้..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 transition">
                    บันทึกหมวดหมู่ใหม่
                </button>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="tags" class="w-4 h-4 text-slate-500"></i>
                    <span>รายการหมวดหมู่ทั้งหมด ({{ $categories->count() }})</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">ชื่อหมวดหมู่</th>
                            <th class="py-3 px-4">คำอธิบาย</th>
                            <th class="py-3 px-4 text-center">จำนวนใบแจ้ง</th>
                            <th class="py-3 px-4 text-right">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $category)
                        <tr class="hover:bg-slate-50 transition" x-data="{ editing: false, name: '{{ addslashes($category->name) }}', desc: '{{ addslashes($category->description) }}' }">
                            <td class="py-3.5 px-4 font-bold text-slate-400">{{ $category->id }}</td>
                            <td class="py-3.5 px-4">
                                <div x-show="!editing" class="font-bold text-slate-900">{{ $category->name }}</div>
                                <div x-show="editing" x-cloak>
                                    <input type="text" x-model="name" class="px-2 py-1 border rounded text-xs w-full">
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">
                                <div x-show="!editing">{{ $category->description ?: '-' }}</div>
                                <div x-show="editing" x-cloak class="mt-1">
                                    <input type="text" x-model="desc" class="px-2 py-1 border rounded text-xs w-full">
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700">
                                    {{ $category->tickets_count }} งาน
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div x-show="!editing" class="flex items-center justify-end gap-2">
                                    <button type="button" @click="editing = true" class="text-blue-600 hover:text-blue-800 font-semibold">แก้ไข</button>
                                    @if($category->tickets_count === 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('ยืนยันลบหมวดหมู่นี้?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">ลบ</button>
                                    </form>
                                    @endif
                                </div>
                                <div x-show="editing" x-cloak class="flex items-center justify-end gap-1">
                                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" :value="name">
                                        <input type="hidden" name="description" :value="desc">
                                        <button type="submit" class="px-2 py-1 bg-emerald-600 text-white rounded text-[11px] font-bold">บันทึก</button>
                                    </form>
                                    <button type="button" @click="editing = false" class="px-2 py-1 bg-slate-200 text-slate-700 rounded text-[11px]">ยกเลิก</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
