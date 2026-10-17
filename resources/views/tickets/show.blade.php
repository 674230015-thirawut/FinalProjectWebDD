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
            <div class="bg-gradient-to-br from-white to-blue-50/40 rounded-2xl border-2 border-blue-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-blue-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">การดำเนินการตามกระบวนการ (State Machine)</h2>
                        <p class="text-xs text-slate-500">สิทธิ์และคำสั่งที่สามารถทำได้ตามสถานะปัจจุบัน</p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-blue-100 text-blue-800 text-xs font-bold">
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

                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                            มอบหมายงานให้ช่าง (Open &rarr; Assigned)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        ใบแจ้งซ่อมนี้กำลังรอให้ผู้ดูแลระบบ (Admin) ตรวจสอบและมอบหมายช่าง
                    </div>
                    @endif
                @endif

                <!-- 2. Assigned -> InProgress (For Assigned Technician or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::Assigned)
                    @if(Auth::user()->isAdmin() || (Auth::user()->isTechnician() && $ticket->technician_id === Auth::id()))
                    <form action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="to_status" value="InProgress">

                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3 text-xs text-indigo-900 leading-relaxed">
                            <strong>คุณได้รับมอบหมายงานแล้ว:</strong> กดปุ่มเพื่อเปลี่ยนสถานะเป็น <strong>กำลังดำเนินการ (In Progress)</strong>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ข้อความตอบรับ / บันทึกการเริ่มงาน</label>
                            <input type="text" name="comment" value="รับทราบงานแล้ว กำลังเข้าไปตรวจสอบอุปกรณ์ครับ" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none">
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                            เริ่มดำเนินการซ่อม (Assigned &rarr; In Progress)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        งานนี้มอบหมายให้ช่าง: <strong>{{ $ticket->technician->name ?? 'N/A' }}</strong> รอช่างกดรับงาน
                    </div>
                    @endif
                @endif

                <!-- 3. InProgress -> Resolved (For Assigned Technician or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::InProgress)
                    @if(Auth::user()->isAdmin() || (Auth::user()->isTechnician() && $ticket->technician_id === Auth::id()))
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

                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-sm transition">
                            บันทึกผลการซ่อมเสร็จสิ้น (In Progress &rarr; Resolved)
                        </button>
                    </form>
                    @else
                    <div class="bg-slate-100 rounded-xl p-4 text-xs text-slate-600">
                        ช่างกำลังดำเนินการซ่อมแซมอุปกรณ์ โปรดรอช่างอัปเดตผลการซ่อม
                    </div>
                    @endif
                @endif

                <!-- 4. Resolved -> Closed OR Resolved -> InProgress (For Ticket Owner or Admin) -->
                @if($ticket->status === \App\Enums\TicketStatus::Resolved)
                    @if(Auth::user()->isAdmin() || $ticket->user_id === Auth::id())
                    <div class="space-y-6" x-data="{ actionType: 'close' }">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 leading-relaxed">
                            <strong>ช่างซ่อมเสร็จสิ้นแล้ว:</strong> โปรดตรวจสอบผลงาน และเลือกยืนยันปิดงาน หรือส่งกลับให้แก้ไข
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="actionType = 'close'"
                                :class="actionType === 'close' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="px-4 py-2 rounded-xl text-xs transition">
                                ยืนยันผลการซ่อม &amp; ให้คะแนน (Closed)
                            </button>
                            <button type="button" @click="actionType = 'rework'"
                                :class="actionType === 'rework' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 text-slate-700'"
                                class="px-4 py-2 rounded-xl text-xs transition">
                                พบปัญหายังไม่หาย / ส่งกลับแก้ไข (In Progress)
                            </button>
                        </div>

                        <!-- Form A: Confirm & Rate -->
                        <form x-show="actionType === 'close'" action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="to_status" value="Closed">

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
                                <textarea name="feedback" rows="2" placeholder="ช่างบริการรวดเร็ว ขอบคุณครับ" class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm outline-none"></textarea>
                            </div>

                            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                ยืนยันปิดงานเรียบร้อย (Resolved &rarr; Closed)
                            </button>
                        </form>

                        <!-- Form B: Reject with Reason -->
                        <form x-show="actionType === 'rework'" x-cloak action="{{ route('tickets.update-status', $ticket) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="to_status" value="InProgress">

                            <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-xs text-rose-800">
                                <strong>ระบุเหตุผลที่ส่งกลับ:</strong> แจ้งอาการที่ยังไม่หายเพื่อให้ช่างเทคนิคตรวจสอบเพิ่มเติม
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">เหตุผลที่ส่งกลับแก้ไข *</label>
                                <textarea name="note" rows="2" required placeholder="เช่น พอลองเปิดเครื่องแล้วยังมีเสียงดังผิดปกติอยู่เหมือนเดิม..." class="w-full px-4 py-2 rounded-xl border border-slate-300 text-sm outline-none"></textarea>
                            </div>

                            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition">
                                ส่งกลับให้ช่างแก้ไข (Resolved &rarr; In Progress)
                            </button>
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

                <!-- Comment Items -->
                <div class="space-y-4">
                    @forelse($ticket->comments as $comment)
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-300">
                            {{ mb_substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $comment->user->role->badgeClasses() }}">
                                        {{ $comment->user->role->label() }}
                                    </span>
                                </div>
                                <span class="text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $comment->body }}</div>
                            @if($comment->image_path)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $comment->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $comment->image_path) }}" class="max-h-48 rounded-xl border border-slate-200 hover:opacity-90 transition">
                                </a>
                            </div>
                            @endif
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
