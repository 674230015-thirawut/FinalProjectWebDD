@extends('layouts.app')

@section('title', 'ใบแจ้งซ่อม #' . $ticket->id . ' - ' . $ticket->title)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Breadcrumb & Back (Clean Text, No Icons) -->
    <div class="flex items-center justify-between text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <a href="{{ route('tickets.index') }}" class="hover:text-blue-600 font-bold text-slate-700">
                &larr; กลับหน้ารายการแจ้งซ่อม
            </a>
            <span class="text-slate-300">/</span>
            <span class="font-bold text-slate-900">ใบแจ้งซ่อม #{{ $ticket->id }}</span>
        </div>
        <div class="text-slate-400">
            สร้างเมื่อ: {{ $ticket->created_at->format('d/m/Y H:i น.') }}
        </div>
    </div>

    <!-- Main Layout: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Ticket Info, Actions, Timeline, Comments -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Ticket Core Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-xs font-black px-2.5 py-1 rounded bg-slate-900 text-white">#{{ $ticket->id }}</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $ticket->status->badgeClasses() }}">
                            <span class="w-2 h-2 rounded-full {{ $ticket->status->dotColor() }}"></span>
                            {{ $ticket->status->label() }}
                        </span>
                        @if($ticket->status === \App\Enums\TicketStatus::PendingFix)
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                            จัดอยู่ในกลุ่ม: รอดำเนินการ (แก้ไข)
                        </span>
                        @endif
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ticket->priority->badgeClasses() }}">
                            ความสำคัญ: {{ $ticket->priority->label() }}
                        </span>
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            หมวดหมู่: {{ $ticket->category->name }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                        {{ $ticket->title }}
                    </h1>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">รายละเอียดอาการเสีย / ปัญหา</div>
                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                            {{ $ticket->description }}
                        </div>
                    </div>

                    <!-- Initial Image Attachment -->
                    @if($ticket->image_path)
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">รูปภาพประกอบตอนแจ้งซ่อม</div>
                        <div class="relative group rounded-xl overflow-hidden border border-slate-200 max-w-md bg-slate-100">
                            <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="รูปภาพแนบ" class="w-full max-h-80 object-contain rounded-xl">
                            <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank"
                                class="absolute bottom-2 right-2 px-3 py-1 bg-slate-900/80 hover:bg-slate-900 text-white text-xs rounded-lg transition font-medium">
                                ดูรูปขนาดเต็ม &rarr;
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Context-Aware State Machine Action Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-green-50/60 rounded-2xl border-2 border-emerald-300 shadow-md p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-emerald-200">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center shadow-sm flex-shrink-0">
                            <span class="text-white text-base font-black">⚙</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">การดำเนินการตามกระบวนการ (State Machine)</h2>
                            <p class="text-xs text-emerald-700">สิทธิ์และคำสั่งที่สามารถทำได้ตามสถานะปัจจุบัน</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold shadow-sm">
                        สถานะ: {{ $ticket->status->label() }}
                    </span>
                </div>

                <!-- 1. Open -> Assigned (For Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::Open)
                    @if(Auth::user()->isAdmin())
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="to_status" value="Assigned">

                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 leading-relaxed">
                            <strong>คำแนะนำ:</strong> สถานะปัจจุบันคือ <strong>รอรับเรื่อง (Open)</strong> ให้ผู้ดูแลระบบเลือกช่างเทคนิคที่รับผิดชอบเพื่อมอบหมายงาน
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">เลือกช่างเทคนิคที่จะมอบหมายงาน *</label>
                            <select name="technician_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-blue-500 bg-white outline-none">
                                <option value="">-- เลือกช่างเทคนิค --</option>
                                @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">บันทึกข้อความ / หมายเหตุ (ถ้ามี)</label>
                            <input type="text" name="note" placeholder="เช่น มอบหมายด่วน ให้เข้าตรวจสอบช่วงบ่าย" class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm outline-none">
                        </div>

                        <button type="submit" class="w-full px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="text-base">✓</span>
                            มอบหมายงานให้ช่าง (Open → Assigned)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        ใบแจ้งซ่อมนี้กำลังรอให้ผู้ดูแลระบบ (Admin) ตรวจสอบและมอบหมายช่าง
                    </div>
                    @endif
                @endif

                <!-- 2. Assigned -> InProgress (For Technician or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::Assigned)
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnician())
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="to_status" value="InProgress">

                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3 text-xs text-indigo-900 leading-relaxed">
                            <strong>งานซ่อมนี้อยู่ในขั้นตอนมอบหมายงาน:</strong> ช่างเทคนิคสามารถกดปุ่มเพื่อเปลี่ยนสถานะเป็น <strong>กำลังดำเนินการ (In Progress)</strong>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ข้อความตอบรับ / บันทึกการเริ่มงาน</label>
                            <input type="text" name="comment" value="รับทราบงานแล้ว กำลังเข้าไปตรวจสอบอุปกรณ์ครับ" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none">
                        </div>

                        <button type="submit" class="w-full px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="text-base">▶</span>
                            เริ่มดำเนินการซ่อม (Assigned → In Progress)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        งานนี้มอบหมายให้ช่าง: <strong>{{ $ticket->technician->name ?? 'N/A' }}</strong> รอช่างกดรับงาน
                    </div>
                    @endif
                @endif

                <!-- 3. InProgress -> Resolved (For Technician or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::InProgress)
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnician())
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="to_status" value="Resolved">

                        <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 text-xs text-purple-900 leading-relaxed">
                            <strong>เมื่อซ่อมเสร็จแล้ว:</strong> บันทึกผลการซ่อมและแนบรูปถ่ายเพื่อเปลี่ยนสถานะเป็น <strong>ซ่อมเสร็จแล้ว (Resolved)</strong>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">รายละเอียดผลการซ่อมแซม *</label>
                            <textarea name="comment" rows="2" required placeholder="ระบุสิ่งที่ได้ดำเนินการซ่อมแซม เช่น เปลี่ยนสายแพรหน้าจอใหม่, ลงโปรแกรมใหม่ ใช้งานได้ปกติแล้ว" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">แนบรูปถ่ายผลการซ่อม (ถ้ามี)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>

                        <button type="submit" class="w-full px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="text-base font-black">✓</span>
                            กดซ่อมเสร็จสิ้น / บันทึกผลการซ่อมเสร็จ (In Progress → Resolved)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        ช่างกำลังดำเนินการซ่อมแซมอุปกรณ์ โปรดรอช่างอัปเดตผลการซ่อม
                    </div>
                    @endif
                @endif

                <!-- 3.1 PendingFix -> Resolved (For Technician or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::PendingFix)
                    @if(Auth::user()->isAdmin() || Auth::user()->isTechnician())
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="to_status" value="Resolved">

                        <div class="bg-rose-50 border-2 border-rose-300 rounded-xl p-4 text-xs text-rose-950 leading-relaxed shadow-sm">
                            <div class="flex items-center gap-2 mb-1.5 font-black text-rose-800 text-sm">
                                <span>⚠️</span>
                                <span>พบปัญหาซ้ำ — สถานะ: รอดำเนินการแก้ไข (จัดอยู่ในกลุ่มรอดำเนินการ)</span>
                            </div>
                            <p>ผู้แจ้งได้รายงานว่าพบปัญหาซ้ำและส่งงานกลับมาแก้ไข โปรดตรวจสอบอาการและดำเนินการแก้ไขอุปกรณ์อีกครั้ง</p>
                            <p class="mt-1 font-bold text-rose-700">เมื่อช่างดำเนินการแก้ไขปัญหาซ้ำเรียบร้อยแล้ว สามารถกดปุ่มยืนยันซ่อมเสร็จได้ทันที</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">บันทึกผลการแก้ไขปัญหาซ้ำ *</label>
                            <textarea name="comment" rows="2" required placeholder="ระบุสิ่งที่ได้ดำเนินการแก้ไขเพิ่มเติม เช่น ตรวจเช็กระบบใหม่ เปลี่ยนสายแพร/อะไหล่สำรอง ทดสอบซ้ำใช้งานได้ตามปกติแล้ว" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">แนบรูปถ่ายผลการแก้ไข (ถ้ามี)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                        </div>

                        <button type="submit" class="w-full px-6 py-3.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <span class="text-base font-black">✓</span>
                            กดซ่อมเสร็จแล้ว (รอดำเนินการแก้ไข → ซ่อมเสร็จแล้ว)
                        </button>
                    </form>
                    @else
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs text-rose-800 leading-relaxed">
                        <strong>พบปัญหาซ้ำ:</strong> กำลังรอดำเนินการแก้ไขโดยช่าง (<strong>{{ $ticket->technician->name ?? 'N/A' }}</strong>) โปรดรอช่างตรวจสอบและกดยืนยันงานเสร็จแล้ว
                    </div>
                    @endif
                @endif

                <!-- 4. Resolved -> Closed OR Resolved -> PendingFix (For Ticket Owner or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::Resolved)
                    @if(Auth::user()->isAdmin() || $ticket->user_id === Auth::id())
                    <div class="space-y-4" x-data="{ actionType: null }">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
                            <span class="text-emerald-600 text-lg mt-0.5">✓</span>
                            <div class="text-sm text-emerald-900 leading-relaxed">
                                <strong>ช่างดำเนินการซ่อมเสร็จสิ้นแล้ว:</strong> โปรดตรวจสอบการทำงานของอุปกรณ์ แล้ว<strong>กดเลือกหนึ่งในสองตัวเลือกด้านล่าง</strong>เพื่อดำเนินการต่อ
                            </div>
                        </div>

                        <!-- 2 Large Choice Buttons (User clicks to select first) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Button: Confirm & Rate -->
                            <button type="button" @click="actionType = (actionType === 'close' ? null : 'close')"
                                :class="actionType === 'close'
                                    ? 'bg-emerald-600 text-white ring-4 ring-emerald-300 shadow-lg scale-[1.02]'
                                    : 'bg-white text-slate-800 border-2 border-emerald-300 hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-md'"
                                class="p-5 rounded-2xl transition-all duration-200 text-left w-full cursor-pointer">
                                <div class="flex items-center gap-3 mb-2">
                                    <span :class="actionType === 'close' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-700'"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center text-xl font-black transition-all duration-200">✓</span>
                                    <div>
                                        <div class="font-black text-base leading-tight">ยืนยันผลการซ่อม</div>
                                        <div :class="actionType === 'close' ? 'text-emerald-100' : 'text-emerald-700'" class="text-xs font-semibold">&amp; ให้คะแนนความพึงพอใจ</div>
                                    </div>
                                </div>
                                <div :class="actionType === 'close' ? 'text-emerald-100' : 'text-slate-400'" class="text-xs pl-1">อุปกรณ์ใช้งานได้ปกติ &bull; ปิดงาน (Closed)</div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span :class="actionType === 'close' ? 'bg-emerald-500 text-white' : 'bg-emerald-600 text-white'"
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all duration-200">
                                        <span x-text="actionType === 'close' ? '✓ เลือกอยู่แล้ว' : '▶ กดเลือกตัวนี้'"></span>
                                    </span>
                                </div>
                            </button>

                            <!-- Button: Repeat Problem still exists -> Send back to PendingFix -->
                            <button type="button" @click="actionType = (actionType === 'rework' ? null : 'rework')"
                                :class="actionType === 'rework'
                                    ? 'bg-rose-600 text-white ring-4 ring-rose-300 shadow-lg scale-[1.02]'
                                    : 'bg-white text-slate-800 border-2 border-rose-300 hover:border-rose-500 hover:bg-rose-50 hover:shadow-md'"
                                class="p-5 rounded-2xl transition-all duration-200 text-left w-full cursor-pointer">
                                <div class="flex items-center gap-3 mb-2">
                                    <span :class="actionType === 'rework' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700'"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center text-xl font-black transition-all duration-200">✕</span>
                                    <div>
                                        <div class="font-black text-base leading-tight">พบปัญหาซ้ำ / ส่งกลับไปแก้ไข</div>
                                        <div :class="actionType === 'rework' ? 'text-rose-100' : 'text-rose-700'" class="text-xs font-semibold">ส่งกลับให้ช่างแก้ไข</div>
                                    </div>
                                </div>
                                <div :class="actionType === 'rework' ? 'text-rose-100' : 'text-slate-400'" class="text-xs pl-1">พบปัญหาซ้ำ อาการยังไม่หาย &bull; ส่งช่างแก้ไข (รอดำเนินการแก้ไข)</div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span :class="actionType === 'rework' ? 'bg-rose-500 text-white' : 'bg-rose-600 text-white'"
                                        class="px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-all duration-200">
                                        <span x-text="actionType === 'rework' ? '✓ เลือกอยู่แล้ว' : '▶ กดเลือกตัวนี้'"></span>
                                    </span>
                                </div>
                            </button>
                        </div>

                        <!-- Initial Guide when neither is selected -->
                        <div x-show="!actionType" class="text-center py-5 px-4 bg-white rounded-2xl border-2 border-dashed border-emerald-300 text-sm text-emerald-700 font-semibold">
                            ⬆ กรุณากดเลือกหนึ่งในสองตัวเลือกด้านบน เพื่อเปิดแบบฟอร์มยืนยัน
                        </div>

                        <!-- Form A: Confirm & Rate (Expands ONLY when user clicks 'close') -->
                        <form x-show="actionType === 'close'" x-cloak action="{{ route('tickets.update-status', $ticket) }}" method="POST"
                            class="space-y-4 bg-white p-5 rounded-2xl border-2 border-emerald-200 shadow-sm animate-in fade-in duration-200">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="to_status" value="Closed">

                            <div class="flex items-center justify-between pb-2 border-b border-emerald-100">
                                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">แบบฟอร์มประเมินและยืนยันปิดงาน</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">ยืนยันผลการซ่อม</span>
                            </div>

                            <div x-data="{ score: 5 }">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">ประเมินความพึงพอใจการบริการ (1-5 ดาว) *</label>
                                <div class="flex items-center gap-2">
                                    <template x-for="star in 5">
                                        <button type="button" @click="score = star" class="p-1 text-2xl transition hover:scale-125 focus:outline-none">
                                            <span x-text="star <= score ? '★' : '☆'" class="text-amber-400"></span>
                                        </button>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600 ml-2" x-text="score + ' / 5 คะแนน'"></span>
                                    <input type="hidden" name="score" :value="score">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ความคิดเห็น / ข้อเสนอแนะเพิ่มเติม</label>
                                <textarea name="feedback" rows="2" placeholder="ช่างบริการรวดเร็ว ขอบคุณครับ" class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-emerald-100">
                                <button type="button" @click="actionType = null" class="text-xs text-slate-400 hover:text-slate-600 font-semibold flex items-center gap-1">
                                    ← ยกเลิกการเลือก
                                </button>
                                <button type="submit" class="px-6 py-3 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                                    <span>✓</span> ยืนยันปิดงานเรียบร้อย
                                </button>
                            </div>
                        </form>

                        <!-- Form B: Reject with Reason (Expands ONLY when user clicks 'rework') -> Sets to PendingFix -->
                        <form x-show="actionType === 'rework'" x-cloak action="{{ route('tickets.update-status', $ticket) }}" method="POST"
                            class="space-y-4 bg-white p-5 rounded-2xl border-2 border-rose-200 shadow-sm animate-in fade-in duration-200">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="to_status" value="PendingFix">

                            <div class="flex items-center justify-between pb-2 border-b border-rose-100">
                                <span class="text-xs font-bold text-rose-800 uppercase tracking-wider">แบบฟอร์มพบปัญหาซ้ำ ส่งกลับให้ช่างแก้ไข</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">รอดำเนินการแก้ไข</span>
                            </div>

                            <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-xs text-rose-800 leading-relaxed">
                                <strong>พบปัญหาซ้ำ / ยังใช้งานไม่ได้:</strong> กรุณาระบุรายละเอียดปัญหาที่ยังพบอยู่ เพื่อส่งกลับให้ช่างเทคนิคดำเนินการตรวจสอบแก้ไขต่อ โดยสถานะจะเปลี่ยนเป็น <strong>รอดำเนินการแก้ไข</strong> (จัดอยู่ในกลุ่มรอดำเนินการ)
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">เหตุผลหรืออาการปัญหาซ้ำที่พบ *</label>
                                <textarea name="note" rows="2" required placeholder="เช่น พอลองเปิดเครื่องแล้วยังมีเสียงดังผิดปกติอยู่เหมือนเดิม..." class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-rose-100">
                                <button type="button" @click="actionType = null" class="text-xs text-slate-400 hover:text-slate-600 font-semibold flex items-center gap-1">
                                    ← ยกเลิกการเลือก
                                </button>
                                <button type="submit" class="px-6 py-3 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                                    <span>✕</span> ยืนยันพบปัญหาซ้ำ ส่งกลับไปแก้ไข (รอดำเนินการแก้ไข)
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        งานซ่อมเสร็จแล้ว รอให้ผู้แจ้ง (<strong>{{ $ticket->user->name }}</strong>) ยืนยันผลการซ่อมและประเมินความพึงพอใจ
                    </div>
                    @endif
                @endif

                <!-- 5. Closed Stage -->
                @if($ticket->status === \App\Enums\TicketStatus::Closed)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-bold text-emerald-900">งานนี้ปิดเรียบร้อยแล้ว (Closed)</div>
                        <div class="text-xs text-emerald-700">ปิดงานเมื่อ: {{ $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i น.') : '-' }}</div>
                    </div>
                    @if($ticket->rating)
                    <div class="text-right">
                        <div class="text-amber-500 font-bold text-lg">
                            {{ str_repeat('★', $ticket->rating->score) }}
                        </div>
                        <div class="text-xs text-slate-500">{{ $ticket->rating->score }}/5 คะแนน</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Customer Rating Card (If rated) -->
            @if($ticket->rating)
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs font-bold text-amber-800 uppercase tracking-wider">ผลการประเมินความพึงพอใจ</div>
                    <div class="text-amber-700 font-bold text-sm">
                        {{ $ticket->rating->score }} จาก 5 คะแนน
                    </div>
                </div>
                <div class="text-2xl text-amber-500 mb-2">
                    {{ str_repeat('★', $ticket->rating->score) }}
                </div>
                @if($ticket->rating->feedback)
                <div class="text-sm text-slate-700 italic bg-white p-3 rounded-xl border border-amber-200/60">
                    "{{ $ticket->rating->feedback }}"
                </div>
                @endif
            </div>
            @endif

            <!-- Comments / Activity Stream -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">
                        การสนทนาและบันทึกข้อความ ({{ $ticket->comments->count() }})
                    </h2>
                </div>

                <!-- Comment Items (Chat style: User on Right, Others on Left) -->
                <div class="space-y-4">
                    @forelse($ticket->comments as $comment)
                    @php $isMine = ($comment->user_id === Auth::id()); @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-start gap-2.5 max-w-[85%] {{ $isMine ? 'flex-row-reverse' : 'flex-row' }}">
                            <!-- Avatar -->
                            <div class="w-8 h-8 rounded-full {{ $isMine ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }} font-bold text-xs flex items-center justify-center shrink-0 border border-slate-300">
                                {{ mb_substr($comment->user->name, 0, 1) }}
                            </div>

                            <!-- Message Bubble -->
                            <div class="{{ $isMine ? 'bg-blue-50 border border-blue-200 rounded-2xl rounded-tr-none' : 'bg-slate-50 border border-slate-200 rounded-2xl rounded-tl-none' }} p-4 space-y-1.5 shadow-sm">
                                <div class="flex items-center justify-between gap-3 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold {{ $isMine ? 'text-blue-900' : 'text-slate-900' }}">
                                            {{ $isMine ? 'คุณ (' . $comment->user->name . ')' : $comment->user->name }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $comment->user->role->badgeClasses() }}">
                                            {{ $comment->user->role->label() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if($comment->user_id === Auth::id() || Auth::user()->isAdmin())
                                        <form action="{{ route('tickets.destroy-comment', [$ticket, $comment]) }}" method="POST" onsubmit="return confirm('ยืนยันลบความคิดเห็นนี้?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-bold hover:underline ml-1" title="ลบความคิดเห็นนี้">
                                                ลบ
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">{{ $comment->body }}</div>
                                @if($comment->image_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $comment->image_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $comment->image_path) }}" class="max-h-52 rounded-xl border border-slate-200 hover:opacity-95 transition">
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-xs text-slate-400">ยังไม่มีข้อความสนทนาในใบแจ้งซ่อมนี้</div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('tickets.add-comment', $ticket) }}" method="POST" enctype="multipart/form-data" class="pt-4 border-t border-slate-100 space-y-3">
                    @csrf
                    <div>
                        <textarea name="body" rows="2" required placeholder="พิมพ์ข้อความสอบถามหรือบันทึกเพิ่มเติม..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex items-center justify-between">
                        <input type="file" name="image" accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            ส่งข้อความ
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Col: Ticket Meta & Audit Log Timeline -->
        <div class="space-y-6">

            <!-- People Info Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">ข้อมูลผู้เกี่ยวข้อง</h3>

                <!-- Requester -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="text-slate-400 text-xs font-medium">ผู้แจ้งซ่อม:</div>
                    <div class="font-bold text-slate-900 text-sm">{{ $ticket->user->name }}</div>
                    <div class="text-xs text-slate-500">{{ $ticket->user->department ?: 'ไม่ระบุแผนก' }}</div>
                    <div class="text-xs text-slate-500">โทร: {{ $ticket->user->phone ?: '-' }}</div>
                </div>

                <!-- Technician -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="text-slate-400 text-xs font-medium">ช่างเทคนิคผู้รับผิดชอบ:</div>
                    @if($ticket->technician)
                    <div class="font-bold text-slate-900 text-sm">{{ $ticket->technician->name }}</div>
                    <div class="text-xs text-slate-500">{{ $ticket->technician->email }}</div>
                    @else
                    <div class="font-semibold text-slate-400 text-xs italic">ยังไม่ได้มอบหมาย</div>
                    @endif
                </div>
            </div>

            <!-- Audit Trail / State Transition Timeline -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">ประวัติการเปลี่ยนสถานะ (Audit Logs)</h3>
                </div>

                <div class="relative border-l-2 border-slate-200 ml-2 space-y-5">
                    @foreach($ticket->statusLogs as $log)
                    <div class="relative pl-5">
                        <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full bg-blue-600 ring-4 ring-white"></div>

                        <div class="text-xs space-y-1">
                            <div class="font-bold text-slate-800">
                                <span>{{ $log->from_status->label() }}</span> &rarr;
                                <span class="text-blue-600">{{ $log->to_status->label() }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                โดย: <span class="font-semibold text-slate-700">{{ $log->changer->name }}</span> ({{ $log->changer->role->label() }})
                            </div>
                            @if($log->note)
                            <div class="text-[11px] text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                {{ $log->note }}
                            </div>
                            @endif
                            <div class="text-[10px] text-slate-400">
                                {{ $log->created_at->format('d/m/Y H:i น.') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
