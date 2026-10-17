<!DOCTYPE html>
<html lang="th" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ระบบแจ้งซ่อม IT') - Smart IT Helpdesk</title>

    <!-- Google Fonts: Sarabun & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Sarabun', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800">

    <!-- Top Bar: Demo Role Switcher (Clean Text & Badges, No Icons) -->
    @auth
    <div class="bg-slate-900 text-slate-300 px-4 py-2 text-xs border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2 whitespace-nowrap">
                <span class="text-slate-400 font-medium">เข้าใช้งานในชื่อ:</span>
                <span class="font-bold text-white">{{ Auth::user()->name }}</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ Auth::user()->role->badgeClasses() }}">
                    {{ Auth::user()->role->label() }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 whitespace-nowrap">
                <span class="text-slate-400 mr-1 text-[11px]">สลับบทบาททดสอบ:</span>
                <a href="{{ route('quick-login', 1) }}" class="px-2.5 py-1 rounded bg-purple-900/70 hover:bg-purple-800 text-purple-200 border border-purple-700/60 font-semibold transition">แอดมิน</a>
                <a href="{{ route('quick-login', 2) }}" class="px-2.5 py-1 rounded bg-amber-900/70 hover:bg-amber-800 text-amber-200 border border-amber-700/60 font-semibold transition">ช่างเทคนิค</a>
                <a href="{{ route('quick-login', 4) }}" class="px-2.5 py-1 rounded bg-blue-900/70 hover:bg-blue-800 text-blue-200 border border-blue-700/60 font-semibold transition">ผู้แจ้งซ่อม</a>
            </div>
        </div>
    </div>
    @endauth

    <!-- Main Navigation Header (Spacious, No Line-Break on Menu Text) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">

                <!-- Brand / Logo (Clean Typography) -->
                <div class="flex items-center gap-6 shrink-0">
                    <a href="{{ route('tickets.index') }}" class="flex items-center gap-2.5 group">
                        <span class="px-2.5 py-1 rounded-lg bg-blue-600 text-white font-extrabold text-sm tracking-wide shadow-sm">IT</span>
                        <div class="leading-tight">
                            <span class="font-extrabold text-base text-slate-900 group-hover:text-blue-600 transition whitespace-nowrap">Helpdesk</span>
                            <span class="block text-[11px] text-slate-400 font-medium whitespace-nowrap">ระบบแจ้งซ่อมและติดตามงาน</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation Links (No Wrap, Clean Spacing) -->
                    @auth
                    <nav class="hidden md:flex items-center gap-1 overflow-x-auto py-1">
                        <a href="{{ route('tickets.index') }}"
                            class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('tickets.index') && !request()->has('view') ? 'text-blue-600 bg-blue-50 border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            รายการแจ้งซ่อม
                        </a>

                        @if(Auth::user()->isTechnician() || Auth::user()->isAdmin())
                        <a href="{{ route('tickets.index', ['view' => 'mine']) }}"
                            class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition {{ request()->query('view') === 'mine' ? 'text-blue-600 bg-blue-50 border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            งานที่ฉันรับผิดชอบ
                        </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 bg-blue-50 border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            แดชบอร์ดสรุปผล
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.categories.*') ? 'text-blue-600 bg-blue-50 border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            หมวดหมู่
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.users.*') ? 'text-blue-600 bg-blue-50 border border-blue-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            ผู้ใช้งานและสิทธิ์
                        </a>
                        @endif
                    </nav>
                    @endauth
                </div>

                <!-- Right Action Buttons & User Profile (No Icons, No Wrap) -->
                <div class="flex items-center gap-3 shrink-0">
                    @auth
                    <a href="{{ route('tickets.create') }}"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition whitespace-nowrap">
                        + แจ้งซ่อมใหม่
                    </a>

                    <!-- User Dropdown Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="text-xs font-bold text-slate-800 whitespace-nowrap hidden sm:inline">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-[10px] text-slate-400">▼</span>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="font-bold text-xs text-slate-800">{{ Auth::user()->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ Auth::user()->email }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                    ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2 whitespace-nowrap">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl shadow-sm transition whitespace-nowrap">ลงทะเบียน</a>
                    @endauth
                </div>

            </div>
        </div>
    </header>

    <!-- Flash Notifications (Clean Text Tags, No Icons) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3.5 text-emerald-800 text-sm font-medium flex items-center gap-2 shadow-sm mb-4">
            <span class="px-2 py-0.5 rounded bg-emerald-600 text-white text-[11px] font-bold">สำเร็จ</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-rose-800 text-sm font-medium flex items-center gap-2 shadow-sm mb-4">
            <span class="px-2 py-0.5 rounded bg-rose-600 text-white text-[11px] font-bold">แจ้งเตือน</span>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-sm mb-4">
            <div class="font-bold text-sm mb-1 text-rose-900">พบข้อผิดพลาด:</div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                <strong>Smart IT Helpdesk &amp; Notification System</strong> &bull; Mini Project
            </div>
            <div class="text-slate-400">
                พัฒนาตามหลัก State Machine &bull; RBAC &bull; Notification
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
