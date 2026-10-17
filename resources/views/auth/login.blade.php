@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8 backdrop-blur-sm">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl mx-auto flex items-center justify-center text-white shadow-lg shadow-blue-500/30 mb-3">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">เข้าสู่ระบบ Helpdesk</h1>
            <p class="text-sm text-slate-500 mt-1">Smart IT Helpdesk &amp; Notification System</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">อีเมลผู้ใช้งาน</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                        placeholder="your-email@example.com">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                    <input type="password" name="password" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition outline-none"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    จดจำการเข้าสู่ระบบ
                </label>
                <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">ลงทะเบียนผู้ใช้ใหม่</a>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 transition">
                เข้าสู่ระบบ
            </button>
        </form>

        <!-- Quick 1-Click Demo Logins for Evaluation -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">
                ⚡ สลับเข้าใช้งานแบบด่วนสำหรับตรวจงาน (1-Click Login)
            </div>
            <div class="grid grid-cols-1 gap-2">
                @if(isset($demoUsers) && $demoUsers->count() > 0)
                    @foreach($demoUsers->take(4) as $dUser)
                    <a href="{{ route('quick-login', $dUser->id) }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 transition group">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold {{ $dUser->role->badgeClasses() }}">
                                {{ $dUser->role === \App\Enums\UserRole::Admin ? '👑' : ($dUser->role === \App\Enums\UserRole::Technician ? '🛠️' : '👤') }}
                            </span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600">{{ $dUser->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $dUser->email }}</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-blue-600 flex items-center gap-1">
                            เข้าใช้ทันที <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </a>
                    @endforeach
                @else
                    <div class="text-xs text-slate-400 text-center">รหัสผ่านเริ่มต้น: password</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
