<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', sidebarOpen: false }" x-init="$watch('darkMode', v => localStorage.setItem('theme', v ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BRILink Rambo</title>
    <meta name="description" content="Sistem Informasi Akuntansi Agen BRILink Rambo — Pencatatan transaksi real-time">

    <!-- Google Fonts: Inter & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .material-symbols-outlined.fill { font-variation-settings: 'FILL' 1; }
    </style>
</head>
<body class="bg-background text-on-surface font-body antialiased flex h-screen overflow-hidden transition-colors duration-300">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

    <!-- SideNavBar -->
    <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed left-0 top-0 h-full w-[240px] bg-surface dark:bg-surface-container border-r border-outline-variant dark:border-outline flex flex-col py-margin_lg z-50 transition-transform duration-300 md:translate-x-0">
        <div class="px-margin_lg mb-8 flex justify-between items-center">
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">BRILink Rambo</h1>
                <p class="font-label text-label text-on-surface-variant">Accounting System</p>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-2">

            {{-- ── UTAMA ── --}}
            <div class="px-margin_lg pt-3 pb-1">
                <p class="font-label-caps text-[10px] text-on-surface-variant/50 uppercase tracking-widest">Utama</p>
            </div>
            <ul class="space-y-0.5 mb-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('dashboard') ? 'fill' : '' }}">dashboard</span>
                        Dashboard
                    </a>
                </li>
            </ul>

            {{-- ── TRANSAKSI ── --}}
            <div class="px-margin_lg pt-3 pb-1">
                <p class="font-label-caps text-[10px] text-on-surface-variant/50 uppercase tracking-widest">Transaksi</p>
            </div>
            <ul class="space-y-0.5 mb-2">
                <li>
                    <a href="{{ route('transaksi.create') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('transaksi.create') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('transaksi.create') ? 'fill' : '' }}">add_circle</span>
                        Input Transaksi
                    </a>
                </li>
                <li>
                    <a href="{{ route('transaksi.index') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('transaksi.index') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('transaksi.index') ? 'fill' : '' }}">receipt_long</span>
                        Riwayat Transaksi
                    </a>
                </li>
            </ul>

            {{-- ── LAPORAN & ADMIN ── --}}
            <div class="px-margin_lg pt-3 pb-1">
                <p class="font-label-caps text-[10px] text-on-surface-variant/50 uppercase tracking-widest">Laporan & Admin</p>
            </div>
            <ul class="space-y-0.5 mb-2">
                <li>
                    <a href="{{ route('laporan.index') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('laporan.*') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('laporan.*') ? 'fill' : '' }}">assessment</span>
                        Laporan
                    </a>
                </li>
                @if(auth()->user()->role === 'owner')
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('users.*') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('users.*') ? 'fill' : '' }}">manage_accounts</span>
                        Kelola User
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('settings.*') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('settings.*') ? 'fill' : '' }}">settings</span>
                        Pengaturan Agen
                    </a>
                </li>
                @endif
            </ul>

            {{-- ── SISTEM ── --}}
            <div class="px-margin_lg pt-3 pb-1">
                <p class="font-label-caps text-[10px] text-on-surface-variant/50 uppercase tracking-widest">Sistem</p>
            </div>
            <ul class="space-y-0.5 mb-2">
                <li>
                    <a href="{{ route('audit-log.index') }}" class="flex items-center px-margin_lg py-2.5 font-body text-sm transition-colors rounded-lg mx-2 {{ request()->routeIs('audit-log.*') ? 'text-primary bg-primary/10 font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined mr-3 text-[20px] {{ request()->routeIs('audit-log.*') ? 'fill' : '' }}">history</span>
                        Audit Trail
                    </a>
                </li>
            </ul>

        </nav>

        <!-- User Info & Logout -->
        <div class="px-margin_lg py-4 border-t border-outline-variant">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-primary rounded flex items-center justify-center text-on-primary font-bold text-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-on-surface text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-on-surface-variant text-xs capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full text-left text-on-surface-variant font-body hover:text-error transition-colors">
                    <span class="material-symbols-outlined mr-3 text-[18px]">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <!-- TopNavBar -->
    <header class="fixed top-0 right-0 h-[56px] w-full md:w-[calc(100%-240px)] bg-surface dark:bg-surface-container border-b border-outline-variant dark:border-outline flex justify-between items-center px-4 md:px-margin_lg z-30 transition-colors">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="md:hidden text-on-surface focus:outline-none">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h2 class="font-subheading text-subheading text-primary dark:text-primary-fixed-dim font-bold truncate">@yield('page-title', 'Dashboard')</h2>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <div class="text-right hidden sm:block mr-2 md:mr-4 border-r border-outline-variant pr-2 md:pr-4">
                <p class="font-label-caps text-label-caps text-on-surface tabular-nums" id="jam-sekarang">--:--:--</p>
                <p class="font-label text-label text-on-surface-variant">{{ now()->translatedFormat('d M Y') }}</p>
            </div>
            
            <button @click="darkMode = !darkMode" class="text-on-surface-variant hover:text-primary transition-colors focus:outline-none" :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'">dark_mode</span>
            </button>
            <button class="text-on-surface-variant hover:text-primary transition-colors focus:outline-none hidden sm:block" title="Profil">
                <span class="material-symbols-outlined">account_circle</span>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full md:ml-[240px] mt-[56px] md:w-[calc(100%-240px)] h-[calc(100vh-56px)] overflow-y-auto p-4 md:p-margin_lg relative">
        <div class="max-w-container_max_width mx-auto">
            
            <!-- Flash Messages -->
            <div class="mb-6">
                @if(session('success'))
                    <div class="flex items-center gap-3 p-4 bg-success-dim border border-success-text/30 text-success-text rounded text-sm mb-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 p-4 bg-danger-dim border border-danger-text/30 text-danger-text rounded text-sm mb-4" x-data x-init="setTimeout(() => $el.remove(), 7000)">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            @yield('content')
            
        </div>
    </main>

<script>
    function updateJam() {
        const el = document.getElementById('jam-sekarang');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    updateJam();
    setInterval(updateJam, 1000);
</script>

@stack('scripts')

</body>
</html>
