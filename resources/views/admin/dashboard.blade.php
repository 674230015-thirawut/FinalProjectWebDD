@extends('layouts.app')

@section('title', 'แดชบอร์ดสรุปผลระบบงาน Helpdesk')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">แดชบอร์ดภาพรวมและการวิเคราะห์ (Admin Dashboard)</h1>
            <p class="text-sm text-slate-500 mt-0.5">สถิติและตัวชี้วัดประสิทธิภาพระบบแจ้งซ่อมไอที</p>
        </div>
        <div class="text-xs text-slate-500 bg-white px-3.5 py-2 rounded-xl border border-slate-200 self-start sm:self-auto font-medium shadow-sm">
            อัปเดตล่าสุด: {{ now()->format('d/m/Y H:i น.') }}
        </div>
    </div>

    <!-- Top KPI Cards (6 Statuses matching colors) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-slate-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">งานทั้งหมด</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">Total</span>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $totalTickets }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">ใบแจ้งทั้งหมด</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-blue-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">รอรับเรื่อง</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Open</span>
            </div>
            <div class="text-2xl font-black text-blue-700 mt-2">{{ $openTickets }}</div>
            <div class="text-[11px] text-blue-500 mt-0.5">รอมอบหมายช่าง</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-amber-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">มอบหมายแล้ว</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Assigned</span>
            </div>
            <div class="text-2xl font-black text-amber-700 mt-2">{{ $assignedTickets }}</div>
            <div class="text-[11px] text-amber-600 mt-0.5">รอช่างเริ่มงาน</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-indigo-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">กำลังดำเนินการ</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">In Progress</span>
            </div>
            <div class="text-2xl font-black text-indigo-700 mt-2">{{ $inProgressTickets }}</div>
            <div class="text-[11px] text-indigo-500 mt-0.5">กำลังตรวจซ่อม</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-purple-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">ซ่อมเสร็จแล้ว</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Resolved</span>
            </div>
            <div class="text-2xl font-black text-purple-700 mt-2">{{ $resolvedTickets }}</div>
            <div class="text-[11px] text-purple-500 mt-0.5">รอผู้แจ้งยืนยัน</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border-t-4 border-t-emerald-500 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">ปิดงานเรียบร้อย</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Closed</span>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-2">{{ $closedTickets }}</div>
            <div class="text-[11px] text-emerald-600 mt-0.5">ประเมินผลสำเร็จ</div>
        </div>
    </div>

    <!-- Secondary KPIs: Avg Resolution Time & Satisfaction Score -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-2xl p-6 text-white shadow-md">
            <div class="text-xs font-bold text-indigo-200 uppercase tracking-wider">ระยะเวลาเฉลี่ยในการซ่อม (Avg Resolution Time)</div>
            <div class="text-3xl font-black mt-3">
                {{ number_format($avgResolutionHours, 1) }} <span class="text-base font-normal text-indigo-200">ชั่วโมง</span>
            </div>
            <p class="text-xs text-indigo-200/80 mt-1">คำนวณจากเวลาที่แจ้งซ่อมจนถึงเวลาที่ช่างกดซ่อมเสร็จ (Resolved)</p>
        </div>

        <div class="bg-gradient-to-br from-amber-900 to-orange-900 rounded-2xl p-6 text-white shadow-md">
            <div class="text-xs font-bold text-amber-200 uppercase tracking-wider">คะแนนความพึงพอใจเฉลี่ย (Customer Satisfaction)</div>
            <div class="text-3xl font-black mt-3 flex items-center gap-2">
                <span>{{ number_format($avgRating, 1) }}</span>
                <span class="text-lg text-amber-300">★ ★ ★ ★ ★</span>
            </div>
            <p class="text-xs text-amber-200/80 mt-1">จากผลการประเมินของผู้ใช้งานทั้งหมด {{ $totalRatings }} ครั้ง</p>
        </div>
    </div>

    <!-- Visual Charts Row: Prominent Interactive Charts via Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Status Distribution Bar Chart -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">กราฟแสดงจำนวนงานตามสถานะ (Status Overview)</h3>
                    <p class="text-xs text-slate-400">เปรียบเทียบปริมาณงานในแต่ละขั้นตอน</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">Bar Chart</span>
            </div>

            <div class="h-64 relative">
                <canvas id="statusBarChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Category Distribution Doughnut Chart -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">สัดส่วนงานซ่อมตามหมวดหมู่ (Category Share)</h3>
                    <p class="text-xs text-slate-400">จำแนกตามประเภทของอุปกรณ์และปัญหา</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">Doughnut</span>
            </div>

            <div class="h-64 relative flex items-center justify-center">
                <canvas id="categoryDoughnutChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Priority Breakdown and Category Detailed Table -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Priority Cards -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">ระดับความสำคัญของปัญหา (Priority Levels)</h3>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-rose-700 uppercase">เร่งด่วนมาก (Urgent)</div>
                    <div class="text-3xl font-black text-rose-800 mt-1">{{ $priorityStats['Urgent'] }}</div>
                    <div class="text-[11px] text-rose-500 mt-0.5">ต้องแก้ไขทันที</div>
                </div>
                <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-orange-700 uppercase">ความสำคัญสูง (High)</div>
                    <div class="text-3xl font-black text-orange-800 mt-1">{{ $priorityStats['High'] }}</div>
                    <div class="text-[11px] text-orange-500 mt-0.5">ส่งผลต่อการทำงาน</div>
                </div>
                <div class="bg-sky-50 border border-sky-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-sky-700 uppercase">ปานกลาง (Medium)</div>
                    <div class="text-3xl font-black text-sky-800 mt-1">{{ $priorityStats['Medium'] }}</div>
                    <div class="text-[11px] text-sky-500 mt-0.5">ทำงานได้บางส่วน</div>
                </div>
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-center">
                    <div class="text-xs font-bold text-slate-600 uppercase">ระดับต่ำ (Low)</div>
                    <div class="text-3xl font-black text-slate-800 mt-1">{{ $priorityStats['Low'] }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">แจ้งทั่วไป / ปรึกษา</div>
                </div>
            </div>
        </div>

        <!-- Technicians Workload Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">ผลการดำเนินงานของทีมช่าง (Technicians Performance)</h3>
                <span class="text-xs text-slate-400">{{ $technicians->count() }} คน</span>
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

    </div>

    <!-- Recent Status Change Activity Feed -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">กิจกรรมเปลี่ยนสถานะล่าสุด (Recent Status Logs)</h3>
            <span class="text-xs text-slate-400">8 รายการล่าสุด</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse($recentLogs as $log)
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-slate-900">
                        <a href="{{ route('tickets.show', $log->ticket_id) }}" class="text-blue-600 hover:underline">
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
                <div class="text-[11px] text-slate-500 italic bg-white p-2 rounded-lg border border-slate-200/60 mt-1">
                    "{{ $log->note }}"
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-2 text-center py-6 text-xs text-slate-400">ยังไม่มีบันทึกกิจกรรม</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Status Bar Chart
        const statusCtx = document.getElementById('statusBarChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['รอรับเรื่อง', 'มอบหมายแล้ว', 'กำลังดำเนินการ', 'ซ่อมเสร็จแล้ว', 'ปิดงานแล้ว'],
                datasets: [{
                    label: 'จำนวนงาน',
                    data: [
                        {{ $openTickets }},
                        {{ $assignedTickets }},
                        {{ $inProgressTickets }},
                        {{ $resolvedTickets }},
                        {{ $closedTickets }}
                    ],
                    backgroundColor: [
                        '#3b82f6', // Blue (Open)
                        '#f59e0b', // Amber (Assigned)
                        '#6366f1', // Indigo (InProgress)
                        '#a855f7', // Purple (Resolved)
                        '#10b981'  // Emerald (Closed)
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // 2. Category Doughnut Chart
        const catLabels = {!! json_encode($categoriesStats->pluck('name')) !!};
        const catData = {!! json_encode($categoriesStats->pluck('tickets_count')) !!};

        const catCtx = document.getElementById('categoryDoughnutChart').getContext('2d');
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: [
                        '#3b82f6',
                        '#8b5cf6',
                        '#06b6d4',
                        '#f59e0b',
                        '#10b981',
                        '#ec4899',
                        '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection
