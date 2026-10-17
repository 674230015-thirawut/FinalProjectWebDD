@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งานและสิทธิ์ (RBAC)')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ showModal: false }">

    <!-- Header & Add User Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">จัดการผู้ใช้งานและสิทธิ์ (Role-Based Access Control)</h1>
            <p class="text-sm text-slate-500 mt-0.5">กำหนดบทบาทผู้ใช้งานระหว่าง ผู้ใช้ทั่วไป (User), ช่างเทคนิค (Technician), และแอดมิน (Admin)</p>
        </div>
        <button type="button" @click="showModal = true"
            class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition whitespace-nowrap self-start sm:self-auto">
            + เพิ่มผู้ใช้งานใหม่
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">
                ผู้ใช้งานทั้งหมดในระบบ ({{ $users->count() }} บัญชี)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 min-w-[900px]">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12">#</th>
                        <th class="py-3 px-4">ชื่อ - นามสกุล</th>
                        <th class="py-3 px-4">อีเมล</th>
                        <th class="py-3 px-4">แผนก / โทรศัพท์</th>
                        <th class="py-3 px-4">บทบาทปัจจุบัน</th>
                        <th class="py-3 px-4">สถิติงาน</th>
                        <th class="py-3 px-4 text-right">การจัดการ &amp; สิทธิ์</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-400">{{ $user->id }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            <div>{{ $user->name }}</div>
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
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <!-- Change Role Form -->
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="inline-flex items-center gap-1">
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

                                <!-- Delete User Form -->
                                @if($user->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('ยืนยันลบผู้ใช้งาน {{ $user->name }} ออกจากระบบ?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-lg text-xs font-bold transition">
                                        ลบ
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Add New User Form -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showModal" @click="showModal = false"
                class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal Content Card -->
            <div x-show="showModal"
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title">เพิ่มผู้ใช้งานใหม่ในระบบ</h3>
                        <p class="text-xs text-slate-400 mt-0.5">กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้ใหม่และกำหนดสิทธิ์</p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg px-2">
                        &times;
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ชื่อ - นามสกุล *</label>
                        <input type="text" name="name" required placeholder="เช่น สมเกียรติ สว่างวงศ์"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">อีเมลผู้ใช้งาน *</label>
                        <input type="email" name="email" required placeholder="user@example.com"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">รหัสผ่านเริ่มต้น *</label>
                            <input type="password" name="password" required placeholder="อย่างน้อย 6 ตัวอักษร"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">บทบาทสิทธิ์ *</label>
                            <select name="role" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                @foreach($roles as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">แผนก / ฝ่าย</label>
                            <input type="text" name="department" placeholder="เช่น ฝ่ายบุคคล, ฝ่ายขาย"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone" placeholder="081-xxx-xxxx"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            ยกเลิก
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition">
                            บันทึกผู้ใช้ใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
