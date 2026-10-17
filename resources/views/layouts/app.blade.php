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

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', 'Sarabun', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800">

    <!-- Demo Fast Role Switcher Bar (Banner) -->
    @auth
    <div class="bg-slate-900 text-slate-300 px-4 py-1.5 text-xs flex flex-wrap items-center justify-between border-b border-slate-800">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="font-medium text-slate-200">เข้าใช้งานในฐานะ:</span>
            <span class="font-bold text-white">{{ Auth::user()->name }}</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ Auth::user()->role->badgeClasses() }}">
                {{ Auth::user()->role->label() }}
            </span>
        </div>
        <div class="flex items-center gap-2 mt-1 sm:mt-0">
            <span class="text-slate-400 hidden sm:inline">สลับบทบาททดสอบ:</span>
            <a href="{{ route('quick-login', 1) }}" class="px-2 py-0.5 rounded bg-purple-900/60 hover:bg-purple-800 text-purple-200 border border-purple-700/50 transition">👑 แอดมิน</a>
            <a href="{{ route('quick-login', 2) }}" class="px-2 py-0.5 rounded bg-amber-900/60 hover:bg-amber-800 text-amber-200 border border-amber-700/50 transition">🛠️ ช่างเทคนิค</a>
            <a href="{{ route('quick-login', 4) }}" class="px-2 py-0.5 rounded bg-blue-900/60 hover:bg-blue-800 text-blue-200 border border-blue-700/50 transition">👤 ผู้แจ้ง</a>
        </div>
    </div>
    @endauth

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition transform">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="font-bold text-lg text-slate-900 tracking-tight leading-none group-hover:text-blue-600 transition">Smart IT Helpdesk</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5">ระบบแจ้งซ่อมและติดตามงานไอที</div>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    @auth
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('tickets.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('tickets.index') && !request()->has('view') ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="ticket" class="w-4 h-4"></i> รายการแจ้งซ่อม
                            </span>
                        </a>

                        @if(Auth::user()->isTechnician() || Auth::user()->isAdmin())
                        <a href="{{ route('tickets.index', ['view' => 'mine']) }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->query('view') === 'mine' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-4 h-4"></i> งานที่ฉันรับผิดชอบ
                            </span>
                        </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i> แดชบอร์ดสรุปผล
                            </span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="tags" class="w-4 h-4"></i> หมวดหมู่
                            </span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="users" class="w-4 h-4"></i> ผู้ใช้งาน & สิทธิ์
                            </span>
                        </a>
                        @endif
                    </nav>
                    @endauth
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-500/25 hover:shadow-lg transition transform hover:-translate-y-0.5">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>แจ้งซ่อมใหม่</span>
                    </a>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm border border-slate-300">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-slate-500">{{ Auth::user()->department ?: Auth::user()->role->label() }}</div>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <div class="font-semibold text-xs text-slate-800">{{ Auth::user()->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ Auth::user()->email }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 flex items-center gap-2 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 px-3 py-2">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl shadow-sm transition">ลงทะเบียน</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center gap-3 shadow-sm mb-4 animate-in fade-in duration-200">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-center gap-3 shadow-sm mb-4 animate-in fade-in duration-200">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div class="text-sm font-medium">{{ session('error') }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-sm mb-4">
            <div class="flex items-center gap-2 font-bold text-sm mb-1">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i> พบข้อผิดพลาด:
            </div>
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
                พัฒนาตามหลัก State Machine &bull; RBAC &bull; Observer Notification
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
