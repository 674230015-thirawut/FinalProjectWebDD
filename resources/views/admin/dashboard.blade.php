@extends('layouts.app')

@section('title', 'แดชบอร์ดสรุปผลระบบงาน Helpdesk')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">แดชบอร์ดภาพรวมและการวิเคราะห์ (Admin Dashboard)</h1>
            <p class="text-sm text-slate-500">สถิติและตัวชี้วัดประสิทธิภาพระบบแจ้งซ่อมไอที (Smart IT Helpdesk)</p>
        </div>
        <div class="text-xs text-slate-400 bg-white px-3 py-1.5 rounded-xl border border-slate-200 self-start sm:self-auto flex items-center gap-1.5">
            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600"></i>
            ข้อมูลอัปเดตล่าสุด: {{ now()->format('d/m/Y H:i น.') }}
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">งานทั้งหมด</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-3">{{ $totalTickets }}</div>
            <div class="text-xs text-slate-400 mt-1">ใบแจ้งซ่อมสะสม</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">รอดำเนินการ</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-amber-700 mt-3">{{ $openTickets + $assignedTickets }}</div>
            <div class="text-xs text-amber-500 mt-1">รอช่างรับเรื่อง / มอบหมาย</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">กำลังซ่อม</span>
                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-indigo-700 mt-3">{{ $inProgressTickets }}</div>
            <div class="text-xs text-indigo-500 mt-1">ช่างกำลังตรวจซ่อม</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">สำเร็จเสร็จสิ้น</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                </span>
            </div>
            <div class="text-3xl font-black text-emerald-700 mt-3">{{ $resolvedTickets + $closedTickets }}</div>
            <div class="text-xs text-emerald-500 mt-1">ซ่อมเสร็จ &amp; ปิดงาน</div>
        </div>
    </div>

    <!-- Secondary KPIs: Avg Resolution Time & Satisfaction Score -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-2xl p-6 text-white shadow-lg shadow-indigo-900/20">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-indigo-200 uppercase tracking-wider">ระยะเวลาเฉลี่ยในการซ่อม (Avg Resolution Time)</div>
                <i data-lucide="timer" class="w-5 h-5 text-indigo-300"></i>
            </div>
            <div class="text-3xl font-black mt-3">
                {{ number_format($avgResolutionHours, 1) }} <span class="text-base font-normal text-indigo-200">ชั่วโมง</span>
            </div>
            <p class="text-xs text-indigo-200/80 mt-1">คำนวณจากเวลาที่แจ้งซ่อมจนถึงเวลาที่ช่างกดซ่อมเสร็จ (Resolved)</p>
        </div>

        <div class="bg-gradient-to-br from-amber-900 to-orange-900 rounded-2xl p-6 text-white shadow-lg shadow-amber-900/20">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-amber-200 uppercase tracking-wider">คะแนนความพึงพอใจเฉลี่ย (Customer Satisfaction)</div>
                <i data-lucide="star" class="w-5 h-5 text-amber-300"></i>
            </div>
            <div class="text-3xl font-black mt-3 flex items-center gap-2">
                <span>{{ number_format($avgRating, 1) }}</span>
                <span class="text-lg text-amber-300">★ ★ ★ ★ ★</span>
            </div>
            <p class="text-xs text-amber-200/80 mt-1">จากผลการประเมินของผู้ใช้งานทั้งหมด {{ $totalRatings }} ครั้ง</p>
        </div>
    </div>

    <!-- 2 Column Analytics: Categories & Priority Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Category Distribution -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="folder" class="w-4 h-4 text-blue-600"></i>
                    <span>การกระจายตามหมวดหมู่งานซ่อม</span>
                </h3>
                <span class="text-xs text-slate-400">{{ $categoriesStats->count() }} หมวดหมู่</span>
            </div>

            <div class="space-y-3">
                @foreach($categoriesStats as $cat)
                @php
                    $percentage = $totalTickets > 0 ? round(($cat->tickets_count / $totalTickets) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                        <span>{{ $cat->name }}</span>
                        <span class="font-bold text-slate-900">{{ $cat->tickets_count }} งาน ({{ $percentage }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Breakdown -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600"></i>
                    <span>ระดับความสำคัญของปัญหา</span>
                </h3>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-rose-700 uppercase">เร่งด่วนมาก (Urgent)</div>
                    <div class="text-2xl font-black text-rose-800 mt-1">{{ $priorityStats['Urgent'] }}</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-orange-700 uppercase">ความสำคัญสูง (High)</div>
                    <div class="text-2xl font-black text-orange-800 mt-1">{{ $priorityStats['High'] }}</div>
                </div>
                <div class="bg-sky-50 border border-sky-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-sky-700 uppercase">ปานกลาง (Medium)</div>
                    <div class="text-2xl font-black text-sky-800 mt-1">{{ $priorityStats['Medium'] }}</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-slate-600 uppercase">ระดับต่ำ (Low)</div>
                    <div class="text-2xl font-black text-slate-800 mt-1">{{ $priorityStats['Low'] }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Technicians Performance & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Technicians Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-amber-600"></i>
                    <span>ผลการดำเนินงานของทีมช่าง (Technicians Performance)</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="py-2.5 px-3">ชื่อช่าง</th>
                            <th class="py-2.5 px-3 text-center">รับมอบหมาย</th>
                            <th class="py-2.5 px-3 text-center">ซ่อมสำเร็จ</th>
                            <th class="py-2.5 px-3 text-right">ความสำเร็จ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($technicians as $tech)
                        @php
                            $rate = $tech->total_assigned > 0 ? round(($tech->resolved_count / $tech->total_assigned) * 100) : 0;
                        @endphp
                        <tr>
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $tech->name }}</td>
                            <td class="py-3 px-3 text-center">{{ $tech->total_assigned }} งาน</td>
                            <td class="py-3 px-3 text-center text-emerald-600 font-bold">{{ $tech->resolved_count }} งาน</td>
                            <td class="py-3 px-3 text-right">
                                <span class="px-2 py-0.5 rounded-full font-bold {{ $rate >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $rate }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-400">ยังไม่มีข้อมูลช่างเทคนิค</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Status Change Activity Feed -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-purple-600"></i>
                    <span>กิจกรรมเปลี่ยนสถานะล่าสุด (Recent Status Logs)</span>
                </h3>
            </div>

            <div class="space-y-3">
                @forelse($recentLogs as $log)
                <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0">
                        {{ mb_substr($log->changer->name, 0, 1) }}
                    </div>
                    <div class="flex-1 text-xs space-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">
                                <a href="{{ route('tickets.show', $log->ticket_id) }}" class="hover:text-blue-600">
                                    ใบแจ้งซ่อม #{{ $log->ticket_id }}
                                </a>
                            </span>
                            <span class="text-slate-400 text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-slate-600">
                            {{ $log->changer->name }} เปลี่ยนสถานะ:
                            <span class="font-semibold text-slate-800">{{ $log->from_status->label() }}</span> &rarr;
                            <span class="font-bold text-blue-600">{{ $log->to_status->label() }}</span>
                        </div>
                        @if($log->note)
                        <div class="text-[11px] text-slate-500 italic">"{{ $log->note }}"</div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-slate-400">ยังไม่มีบันทึกกิจกรรม</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
