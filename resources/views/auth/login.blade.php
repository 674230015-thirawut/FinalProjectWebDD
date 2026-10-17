@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="text-center mb-6">
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full mb-2">IT HELPDESK</span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">เข้าสู่ระบบ</h1>
            <p class="text-xs text-slate-500 mt-1">Smart IT Helpdesk &amp; Notification System</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">อีเมลผู้ใช้งาน</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="your-email@example.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    จดจำการเข้าสู่ระบบ
                </label>
                <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">ลงทะเบียนผู้ใช้ใหม่</a>
            </div>

            <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                เข้าสู่ระบบ
            </button>
        </form>

        <!-- Quick 1-Click Demo Logins for Evaluation -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">
                สลับเข้าใช้งานแบบด่วนสำหรับตรวจงาน (1-Click Login)
            </div>
            <div class="grid grid-cols-1 gap-2">
                @if(isset($demoUsers) && $demoUsers->count() > 0)
                    @foreach($demoUsers->take(4) as $dUser)
                    <a href="{{ route('quick-login', $dUser->id) }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 transition group">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $dUser->role->badgeClasses() }}">
                                {{ $dUser->role->label() }}
                            </span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600">{{ $dUser->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $dUser->email }}</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-blue-600">
                            เข้าใช้ทันที &rarr;
                        </span>
                    </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
