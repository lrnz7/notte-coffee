<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-notte-black { background-color: #080808; }
        .bg-notte-card { background-color: #121212; }
        .text-notte-gold { color: #c5a880; }
        .bg-notte-gold { background-color: #c5a880; }
    </style>
</head>
<body class="bg-notte-black min-h-screen flex flex-col justify-between antialiased selection:bg-amber-900 selection:text-amber-100">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-50 bg-notte-black/90 backdrop-blur-md border-b border-neutral-800/60">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-8 md:h-10 w-auto object-contain">
            </a>
            <div class="flex items-center space-x-4">
                <a href="{{ route('customer.menu') }}" class="bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest">
                    Order Menu
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT UTAMA AKUN -->
    <main class="max-w-6xl mx-auto px-6 pt-32 pb-24 flex-1 w-full">
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-700 text-emerald-200 text-xs rounded-xl flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">✕</button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Sidebar Profil & Pengaturan -->
            <div class="col-span-1 space-y-6">
                <div class="bg-notte-card border border-neutral-800 p-6 rounded-2xl shadow-xl">
                    <span class="text-[10px] font-mono text-notte-gold uppercase tracking-widest block mb-1">Customer Account</span>
                    <h2 class="font-serif-title text-2xl font-bold text-gray-100 mb-1">{{ $user->name }}</h2>
                    <p class="text-xs text-gray-400 font-mono">{{ $user->email }}</p>
                    
                    @if(!$user->has_claimed_welcome_discount)
                        <div class="mt-4 p-3 bg-notte-gold/10 border border-notte-gold/40 text-notte-gold text-[11px] rounded-xl text-center font-bold">
                            🎉 Diskon 50% Pengguna Baru Masih Aktif!
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-neutral-900 border border-neutral-800 text-gray-500 text-[11px] rounded-xl text-center font-mono">
                            Diskon pengguna baru sudah digunakan
                        </div>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full border border-rose-900/60 text-rose-400 hover:bg-rose-950 hover:text-rose-200 text-xs py-3 rounded-xl uppercase tracking-widest font-bold transition">
                            Logout
                        </button>
                    </form>
                </div>

                <!-- Form Edit Alamat & Profil -->
                <form action="{{ route('customer.account.update') }}" method="POST" class="bg-notte-card border border-neutral-800 p-6 rounded-2xl shadow-xl space-y-4">
                    @csrf
                    <h3 class="text-xs font-bold uppercase tracking-widest text-notte-gold border-b border-neutral-800 pb-3">Pengaturan Profil & Alamat</h3>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full bg-notte-black border border-neutral-800 p-3 rounded-xl text-xs text-gray-200 focus:border-notte-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1">Nomor WhatsApp</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" required class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-400 mb-1">Alamat Utama Pengiriman</label>
                        <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap rumah/kantor..." class="w-full bg-notte-black border border-neutral-800 p-3 rounded-xl text-xs text-gray-200 focus:border-notte-gold outline-none">{{ $user->address }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-notte-gold text-black font-extrabold text-xs tracking-widest uppercase py-3.5 rounded-xl hover:bg-amber-600 transition shadow-lg">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Tabel Riwayat Pesanan -->
            <div class="col-span-2 bg-notte-card border border-neutral-800 p-6 md:p-8 rounded-2xl shadow-xl flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-serif-title text-gray-100 mb-6 border-b border-neutral-800 pb-4">Riwayat Pesanan Saya</h3>
                    
                    <div class="space-y-4">
                        @forelse($orders as $order)
                            <div class="border border-neutral-800/80 p-4 rounded-xl flex flex-col md:flex-row justify-between md:items-center gap-4 hover:border-notte-gold/40 transition bg-notte-black">
                                <div>
                                    <span class="text-[10px] text-notte-gold font-mono uppercase tracking-widest block">{{ strtoupper($order->order_type) }}</span>
                                    <a href="{{ route('customer.order.track', $order->invoice_number) }}" class="text-sm font-bold text-gray-200 hover:text-notte-gold transition block">
                                        {{ $order->invoice_number }} →
                                    </a>
                                    <span class="text-[11px] text-gray-500 font-mono block mt-1">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-sm font-bold text-notte-gold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <span class="text-[9px] uppercase tracking-widest px-2.5 py-1 rounded-full bg-neutral-900 border border-neutral-800 text-gray-400 mt-1 inline-block font-mono">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <p class="text-xs text-gray-500 font-mono mb-4">Lu belum pernah order apapun nih.</p>
                                <a href="{{ route('customer.menu') }}" class="inline-block border border-notte-gold text-notte-gold hover:bg-notte-gold hover:text-black font-bold text-xs px-6 py-2.5 rounded-full transition uppercase tracking-widest">
                                    Mulai Pesan Kopi
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-neutral-800 bg-notte-black py-8 px-6 text-xs text-gray-500 font-mono">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <p>&copy; {{ date('Y') }} NOTTE Coffee. All rights reserved.</p>
            <a href="{{ route('home') }}" class="hover:text-notte-gold transition">← Landing Page</a>
        </div>
    </footer>
</body>
</html>