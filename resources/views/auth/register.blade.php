@extends('layouts.app')

@section('title', 'ลงทะเบียนผู้ใช้ใหม่')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="text-center mb-6">
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full mb-2">IT HELPDESK</span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">ลงทะเบียนผู้ใช้งาน</h1>
            <p class="text-xs text-slate-500 mt-1">สร้างบัญชีสำหรับส่งคำขอแจ้งซ่อมไอที</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ชื่อ - นามสกุล *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="สมชาย ใจดี">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">อีเมล *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="somchai@company.com">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">แผนก / ฝ่าย</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="ฝ่ายบุคคล">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="081-234-5678">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน *</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="อย่างน้อย 6 ตัวอักษร">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ยืนยันรหัสผ่าน *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="พิมพ์รหัสผ่านอีกครั้ง">
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                ยืนยันการลงทะเบียน
            </button>

            <div class="text-center text-xs text-slate-500 pt-2">
                มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">เข้าสู่ระบบที่นี่</a>
            </div>
        </form>
    </div>
</div>
@endsection
