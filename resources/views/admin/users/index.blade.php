@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งานและสิทธิ์ (RBAC)')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">จัดการผู้ใช้งานและสิทธิ์ (Role-Based Access Control)</h1>
            <p class="text-sm text-slate-500">กำหนดบทบาทผู้ใช้งานระหว่าง ผู้ใช้ทั่วไป (User), ช่างเทคนิค (Technician), และแอดมิน (Admin)</p>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4 text-blue-600"></i>
                <span>ผู้ใช้งานทั้งหมดในระบบ ({{ $users->count() }} บัญชี)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">ชื่อ - นามสกุล</th>
                        <th class="py-3 px-4">อีเมล</th>
                        <th class="py-3 px-4">แผนก / โทรศัพท์</th>
                        <th class="py-3 px-4">บทบาทปัจจุบัน</th>
                        <th class="py-3 px-4">สถิติงาน</th>
                        <th class="py-3 px-4 text-right">เปลี่ยนบทบาทสิทธิ์</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-400">{{ $user->id }}</td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900">{{ $user->name }}</div>
                            @if($user->id === Auth::id())
                            <span class="text-[10px] text-blue-600 font-bold">(บัญชีของคุณ)</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $user->email }}</td>
                        <td class="py-3.5 px-4 text-slate-500">
                            <div>{{ $user->department ?: 'ทั่วไป' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $user->phone ?: '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->role->badgeClasses() }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500">
                            <div>แจ้งซ่อม: <strong class="text-slate-800">{{ $user->tickets_count }}</strong> งาน</div>
                            <div>รับมอบหมาย: <strong class="text-amber-700">{{ $user->assigned_tickets_count }}</strong> งาน</div>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="inline-flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="px-2 py-1 rounded-lg border border-slate-300 text-xs bg-white focus:ring-1 focus:ring-blue-500 outline-none">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->value }}" {{ $user->role === $role ? 'selected' : '' }}>
                                        {{ $role->label() }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition">
                                    บันทึก
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
