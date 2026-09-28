<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NOTTE Coffee - ERP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine JS dipindah ke sini biar jadi standar seluruh layout ERP lu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- BACKDROP MOBILE: Gelap transparan pas menu samping dibuka -->
        <div x-show="sidebarOpen" style="display: none;" x-transition.opacity class="fixed inset-0 z-40 bg-black/60 md:hidden backdrop-blur-sm" @click="sidebarOpen = false"></div>

        <!-- SIDEBAR NAVIGASI -->
        <!-- Desktop: Nempel terus di kiri (relative md:translate-x-0). Mobile: Sembunyi ke kiri (-translate-x-full) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white flex flex-col justify-between p-4 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 shadow-2xl md:shadow-none">
            <div>
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold tracking-wider text-amber-500">NOTTE ERP</h1>
                    <!-- Tombol Close (Silang) khusus Mobile -->
                    <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white p-1">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">Dashboard</a>
                    <a href="{{ route('pos.index') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('pos.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">POS Kasir Toko</a>
                    <a href="{{ route('orders.index') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">Pesanan Online</a>
                    <a href="{{ route('cash_flows.index') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('cash_flows.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">Laporan Keuangan</a>
                    <a href="{{ route('materials.index') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('materials.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">Stok Bahan Baku</a>
                    <a href="{{ route('menus.index') }}" class="block py-2.5 px-4 rounded transition {{ request()->routeIs('menus.*') ? 'bg-slate-800 text-amber-400 font-bold' : 'hover:bg-slate-800' }}">Katalog Menu & HPP</a>
                </nav>
            </div>
            <div class="border-t border-slate-800 pt-4">
                <p class="text-xs text-gray-400">Log masuk sebagai:</p>
                <p class="text-sm font-semibold text-amber-400">{{ auth()->user()->name ?? 'Admin / Kasir' }}</p>
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-medium">← Logout</button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col overflow-hidden relative w-full">
            
            <!-- HEADER MOBILE: Cuma muncul kalau dibuka di HP -->
            <header class="md:hidden bg-slate-900 text-white flex items-center justify-between px-4 py-3 shadow-md z-30 relative">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="text-amber-500 focus:outline-none active:scale-95 transition p-1">
                        <!-- Ikon Hamburger Menu -->
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <span class="font-black tracking-wider text-amber-500 text-lg uppercase">NOTTE ERP</span>
                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <!-- p-2 buat di HP biar layarnya mentok lebar, p-8 buat desktop biar rapi -->
            <main class="flex-1 overflow-y-auto p-2 md:p-8 bg-gray-100">
                @if(session('success'))
                    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-6 text-sm font-semibold shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>