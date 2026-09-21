<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTTE Coffee - ERP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-wider text-amber-500 mb-8">NOTTE ERP</h1>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Dashboard</a>
                    <a href="{{ route('pos.index') }}" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">POS Kasir Toko</a>
                    <a href="{{ route('orders.index') }}" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Pesanan Online</a>
                    <a href="{{ route('materials.index') }}" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Stok Bahan Baku</a>
                    <a href="{{ route('menus.index') }}" class="block py-2.5 px-4 rounded transition hover:bg-slate-800">Katalog Menu & HPP</a>
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

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>