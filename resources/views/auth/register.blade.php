@extends('layouts.app')

@section('title', 'ลงทะเบียนผู้ใช้ใหม่')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white shadow-lg shadow-blue-500/30 mb-3">
                <i data-lucide="user-plus" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">ลงทะเบียนผู้ใช้งาน</h1>
            <p class="text-sm text-slate-500 mt-1">สร้างบัญชีสำหรับส่งคำขอแจ้งซ่อมไอที</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ชื่อ - นามสกุล *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                    placeholder="สมชาย ใจดี">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">อีเมล *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                    placeholder="somchai@company.com">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">แผนก / ฝ่าย</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                        placeholder="ฝ่ายบุคคล">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                        placeholder="081-234-5678">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน *</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                    placeholder="อย่างน้อย 6 ตัวอักษร">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ยืนยันรหัสผ่าน *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                    placeholder="พิมพ์รหัสผ่านอีกครั้ง">
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 transition">
                ยืนยันการลงทะเบียน
            </button>

            <div class="text-center text-xs text-slate-500 pt-2">
                มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">เข้าสู่ระบบที่นี่</a>
            </div>
        </form>
    </div>
</div>
@endsection
