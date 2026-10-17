@extends('layouts.app')

@section('title', 'รายการแจ้งซ่อม')

@section('content')
<div class="space-y-6">

    <!-- Page Header (Clean, No Icons) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">รายการแจ้งซ่อมไอที</h1>
            <p class="text-sm text-slate-500 mt-0.5">ติดตามสถานะงานซ่อมทั้งหมดในระบบ</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition whitespace-nowrap self-start sm:self-auto">
            + แจ้งซ่อมอุปกรณ์ / โปรแกรม
        </a>
    </div>

    <!-- Quick Stat Badges (Clean Borders, No Icons) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('tickets.index') }}" class="bg-white p-5 rounded-2xl border-t-4 border-t-slate-400 border border-slate-200 shadow-sm hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">ทั้งหมด</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600">รวม</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-2">{{ $counts['all'] }}</div>
            <div class="text-xs text-slate-400 mt-1">ใบแจ้งซ่อมทั้งหมด</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'Open']) }}" class="bg-white p-5 rounded-2xl border-t-4 border-t-blue-500 border border-slate-200 shadow-sm hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">รอรับเรื่อง</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700">Open</span>
            </div>
            <div class="text-3xl font-black text-blue-700 mt-2">{{ $counts['open'] }}</div>
            <div class="text-xs text-blue-500 mt-1">รอมอบหมายช่าง</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'InProgress']) }}" class="bg-white p-5 rounded-2xl border-t-4 border-t-indigo-500 border border-slate-200 shadow-sm hover:border-indigo-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">กำลังดำเนินการ</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700">In Progress</span>
            </div>
            <div class="text-3xl font-black text-indigo-700 mt-2">{{ $counts['in_progress'] }}</div>
            <div class="text-xs text-indigo-500 mt-1">มอบหมาย / กำลังซ่อม</div>
        </a>

        <a href="{{ route('tickets.index', ['status' => 'Closed']) }}" class="bg-white p-5 rounded-2xl border-t-4 border-t-emerald-500 border border-slate-200 shadow-sm hover:border-emerald-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">เสร็จสิ้นแล้ว</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700">Closed</span>
            </div>
            <div class="text-3xl font-black text-emerald-700 mt-2">{{ $counts['resolved'] }}</div>
            <div class="text-xs text-emerald-500 mt-1">ซ่อมเสร็จและปิดงาน</div>
        </a>
    </div>

    <!-- Filter & Search Toolbar (No Icons, Clear Text) -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @if(request()->query('view'))
            <input type="hidden" name="view" value="{{ request()->query('view') }}">
            @endif

            <!-- Search input -->
            <div class="lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="พิมพ์คำค้นหา: รหัส, หัวข้อ, อาการเสีย..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <!-- Status filter -->
            <div>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- ทุกสถานะ --</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                        {{ $st->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Category filter -->
            <div>
                <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">-- ทุกหมวดหมู่ --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition whitespace-nowrap">
                    ค้นหา
                </button>
                @if(request()->hasAny(['search', 'status', 'category_id', 'priority']))
                <a href="{{ route('tickets.index', request()->query('view') ? ['view' => request()->query('view')] : []) }}"
                    class="py-2.5 px-3 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-100 transition whitespace-nowrap">
                    ล้างค่า
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ticket Table with Generous Column Widths (No Awkward Word-Wrapping) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($tickets->isEmpty())
        <div class="text-center py-16 px-4">
            <div class="inline-block px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-xs mb-3">
                ไม่พบข้อมูล
            </div>
            <h3 class="text-lg font-bold text-slate-800">ไม่พบรายการแจ้งซ่อม</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">ยังไม่มีคำขอแจ้งซ่อมที่ตรงกับเงื่อนไข สามารถกดสร้างคำขอใหม่ได้ทันที</p>
            <a href="{{ route('tickets.create') }}" class="inline-block mt-4 px-5 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition">
                + แจ้งซ่อมใหม่เดี๋ยวนี้
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 min-w-[1050px]">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-32 whitespace-nowrap">รหัส / วันที่</th>
                        <th class="py-3.5 px-4 min-w-[340px] max-w-[460px]">หัวข้อปัญหา</th>
                        <th class="py-3.5 px-4 w-44 whitespace-nowrap">หมวดหมู่</th>
                        <th class="py-3.5 px-4 w-32 whitespace-nowrap">ความสำคัญ</th>
                        <th class="py-3.5 px-4 w-44 whitespace-nowrap">สถานะ</th>
                        <th class="py-3.5 px-4 w-40 whitespace-nowrap">ผู้แจ้ง</th>
                        <th class="py-3.5 px-4 w-40 whitespace-nowrap">ช่างที่รับผิดชอบ</th>
                        <th class="py-3.5 px-4 w-28 text-right whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-blue-50/30 transition">
                        <!-- Code / Date -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            <div class="font-black text-slate-900 text-sm">#{{ $ticket->id }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $ticket->created_at->format('d/m/Y H:i น.') }}
                            </div>
                        </td>

                        <!-- Problem Title (Generous Width, No Awkward Stacking) -->
                        <td class="py-4 px-4 min-w-[340px] max-w-[460px] align-top">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-bold text-slate-900 hover:text-blue-600 transition text-sm leading-snug block">
                                {{ $ticket->title }}
                            </a>
                            <div class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $ticket->description }}
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $ticket->category->name }}
                            </span>
                        </td>

                        <!-- Priority -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ticket->priority->badgeClasses() }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $ticket->status->badgeClasses() }}">
                                <span class="w-2 h-2 rounded-full {{ $ticket->status->dotColor() }}"></span>
                                {{ $ticket->status->label() }}
                            </span>
                        </td>

                        <!-- Requester -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            <div class="font-bold text-slate-800 text-xs">{{ $ticket->user->name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $ticket->user->department ?: 'ทั่วไป' }}</div>
                        </td>

                        <!-- Technician -->
                        <td class="py-4 px-4 whitespace-nowrap align-top">
                            @if($ticket->technician)
                            <div class="font-bold text-slate-800 text-xs">{{ $ticket->technician->name }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $ticket->technician->email }}</div>
                            @else
                            <span class="text-xs text-slate-400 italic">ยังไม่มอบหมาย</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-4 text-right whitespace-nowrap align-top">
                            <a href="{{ route('tickets.show', $ticket) }}"
                                class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                ดูรายละเอียด
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
