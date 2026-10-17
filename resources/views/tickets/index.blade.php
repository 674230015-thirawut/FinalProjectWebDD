@extends('layouts.app')

@section('title', 'รายการแจ้งซ่อม')

@section('content')
<div class="space-y-6">

    <!-- Header & Summary Cards -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">รายการแจ้งซ่อมไอที</h1>
            <p class="text-sm text-slate-500">ติดตามสถานะงานซ่อมทั้งหมดในระบบแบบ Real-time</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition self-start sm:self-auto">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>แจ้งซ่อมอุปกรณ์ / โปรแกรม</span>
        </a>
    </div>

    <!-- Quick Stat Badges -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('tickets.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-300 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">ทั้งหมด</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-blue-50 group-hover:text-blue-600 transition">
                    <i data-lucide="inbox" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $counts['all'] }}</div>
            <div class="text-xs text-slate-400 mt-0.5">ใบแจ้งซ่อมทั้งหมด</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'Open']) }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-400 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">รอรับเรื่อง</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-blue-700 mt-2">{{ $counts['open'] }}</div>
            <div class="text-xs text-blue-500 mt-0.5">รอมอบหมายช่าง</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'InProgress']) }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-indigo-400 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">กำลังดำเนินการ</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="loader" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-indigo-700 mt-2">{{ $counts['in_progress'] }}</div>
            <div class="text-xs text-indigo-500 mt-0.5">มอบหมาย / กำลังซ่อม</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'Closed']) }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm hover:border-emerald-400 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">เสร็จสิ้นแล้ว</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-2">{{ $counts['resolved'] }}</div>
            <div class="text-xs text-emerald-500 mt-0.5">ซ่อมเสร็จ &amp; ปิดงาน</div>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @if(request()->query('view'))
            <input type="hidden" name="view" value="{{ request()->query('view') }}">
            @endif

            <!-- Search input -->
            <div class="relative lg:col-span-2">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="ค้นหาตามรหัส, หัวข้อ, อาการเสีย..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <!-- Status filter -->
            <div>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="">ทุกสถานะ</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Category filter -->
            <div>
                <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="">ทุกหมวดหมู่</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-4 h-4"></i> ค้นหา
                </button>
                @if(request()->hasAny(['search', 'status', 'category_id', 'priority']))
                <a href="{{ route('tickets.index', request()->query('view') ? ['view' => request()->query('view')] : []) }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 text-sm hover:bg-slate-100 transition" title="ล้างตัวกรอง">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ticket Table / Card List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($tickets->isEmpty())
        <div class="text-center py-16 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                <i data-lucide="inbox" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">ไม่พบรายการแจ้งซ่อม</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">ยังไม่มีคำขอแจ้งซ่อมที่ตรงกับเงื่อนไขการค้นหา คุณสามารถกดสร้างคำขอใหม่ได้ตลอดเวลา</p>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> แจ้งซ่อมใหม่เดี๋ยวนี้
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">รหัส / วันที่</th>
                        <th class="py-3.5 px-4">หัวข้อปัญหา</th>
                        <th class="py-3.5 px-4">หมวดหมู่</th>
                        <th class="py-3.5 px-4">ความสำคัญ</th>
                        <th class="py-3.5 px-4">สถานะ</th>
                        <th class="py-3.5 px-4">ผู้แจ้ง</th>
                        <th class="py-3.5 px-4">ช่างที่รับผิดชอบ</th>
                        <th class="py-3.5 px-4 text-right">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-slate-50/80 transition group">
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-extrabold text-slate-900 group-hover:text-blue-600 transition">#{{ $ticket->id }}</div>
                            <div class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-slate-900 hover:text-blue-600 transition line-clamp-1 block">
                                {{ $ticket->title }}
                            </a>
                            <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">
                                {{ Str::limit($ticket->description, 60) }}
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                <i data-lucide="tag" class="w-3 h-3 text-slate-400"></i>
                                {{ $ticket->category->name }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ticket->priority->badgeClasses() }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $ticket->status->badgeClasses() }}">
                                <span class="w-2 h-2 rounded-full {{ $ticket->status->dotColor() }}"></span>
                                {{ $ticket->status->label() }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-medium text-slate-800">{{ $ticket->user->name }}</div>
                            <div class="text-xs text-slate-400">{{ $ticket->user->department ?: 'ทั่วไป' }}</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($ticket->technician)
                            <div class="flex items-center gap-1.5 font-medium text-slate-800">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-amber-500"></i>
                                {{ $ticket->technician->name }}
                            </div>
                            @else
                            <span class="text-xs text-slate-400 italic">ยังไม่ได้มอบหมาย</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 transition">
                                ดูรายละเอียด <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
