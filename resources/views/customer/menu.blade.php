<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — NOTTE Coffee</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-notte.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- LEAFLET MAPS CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-notte-black { background-color: #080808; }
        .bg-notte-card { background-color: #121212; }
        .border-notte-gold { border-color: #c5a880; }
        .text-notte-gold { color: #c5a880; }
        .bg-notte-gold { background-color: #c5a880; }
        #leaflet-map { height: 220px; width: 100%; border-radius: 0.75rem; z-index: 10; }
    </style>
</head>
<body class="bg-notte-black min-h-screen flex flex-col justify-between antialiased selection:bg-amber-900 selection:text-amber-100 relative pb-28">

    <!-- BANNER DISKON -->
    @auth
        @if(Auth::user()->role === 'customer' && !Auth::user()->has_claimed_welcome_discount)
        <div id="welcome-discount-banner" class="bg-notte-gold text-black text-center py-2 px-4 text-[10px] md:text-xs font-bold uppercase tracking-widest relative z-[60] flex justify-between items-center">
            <span class="mx-auto">✦ Selamat Datang {{ Auth::user()->name }}! Diskon 50% otomatis terpasang saat checkout ✦</span>
            <button onclick="document.getElementById('welcome-discount-banner').remove()" class="text-black font-bold text-sm hover:opacity-75">✕</button>
        </div>
        @endif
    @endauth

    <!-- FLOATING LIVE TRACKER -->
    <div id="live-tracker-bar" class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md bg-[#121212]/95 backdrop-blur-md border border-[#c5a880]/60 p-3.5 sm:p-4 rounded-xl shadow-2xl flex items-center justify-between gap-3 transition-all duration-300">
        <div class="flex items-center gap-3 min-w-0">
            <span class="relative flex h-3 w-3 shrink-0">
              <span id="tracker-ping-dot" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#c5a880] opacity-75"></span>
              <span id="tracker-solid-dot" class="relative inline-flex rounded-full h-3 w-3 bg-[#c5a880]"></span>
            </span>
            <div class="truncate">
                <span id="live-invoice-label" class="text-[9px] font-mono uppercase tracking-widest text-gray-400 block truncate">Pesanan Aktif: {{ session('active_invoice', '') }}</span>
                <p id="live-status-text" class="text-xs font-bold text-gray-200 truncate">Memuat status pesanan...</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a id="live-tracker-link" href="{{ session('active_invoice') ? route('customer.order.track', session('active_invoice')) : '#' }}" class="bg-[#c5a880] text-black font-extrabold text-[10px] px-3.5 py-2 rounded-lg uppercase tracking-wider transition hover:bg-amber-600 shadow">
                Struk →
            </a>
            <button type="button" onclick="closeTracker()" class="text-gray-400 hover:text-white font-bold p-1 text-sm leading-none" title="Tutup">✕</button>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast-notification" class="fixed top-24 right-6 z-[60] transform translate-x-[150%] opacity-0 transition-all duration-500 ease-out bg-notte-card border border-notte-gold text-gray-200 px-6 py-4 rounded-md shadow-sm flex items-center gap-4">
        <span class="text-notte-gold text-xl">✦</span>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-notte-gold">Berhasil</p>
            <p id="toast-message" class="text-xs text-gray-300 mt-0.5">Menu ditambahkan ke keranjang.</p>
        </div>
    </div>

    <!-- ERROR ALERTS DARI CONTROLLER -->
    @if(session('error'))
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[70] w-full max-w-md bg-rose-950/90 backdrop-blur-sm border border-rose-800/60 text-rose-200 px-6 py-4 rounded-md shadow-sm flex items-center gap-3">
            <span class="text-rose-400 text-lg">⚠</span>
            <div class="flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-400">Gagal Memproses Pesanan</p>
                <p class="text-xs mt-0.5">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">✕</button>
        </div>
    @endif

    <!-- NAVBAR -->
    <nav class="sticky top-0 w-full z-50 bg-notte-black/90 backdrop-blur-md border-b border-neutral-800/60">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-8 md:h-10 w-auto object-contain">
            </a>

            <div class="hidden md:flex items-center gap-8 text-[11px] font-bold tracking-widest text-gray-400 uppercase">
                <a href="{{ route('home') }}#philosophy" class="hover:text-notte-gold transition">Our Story</a>
                <a href="{{ route('home') }}#crafted" class="hover:text-notte-gold transition">The Coffee</a>
                <a href="{{ route('home') }}#literan" class="hover:text-notte-gold transition">NOTTE 1L</a>
                <a href="{{ route('home') }}#visit" class="hover:text-notte-gold transition">Visit Us</a>
            </div>

            <div class="flex items-center space-x-4">
                <button onclick="toggleCart()" class="relative bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest flex items-center gap-2 shadow-lg">
                    <span>Keranjang</span>
                    <span id="cart-count" class="bg-black text-notte-gold text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-mono">0</span>
                </button>

                @auth
                    @if(Auth::user()->role === 'customer')
                        <a href="{{ route('customer.account') }}" class="border border-notte-gold text-notte-gold hover:bg-notte-gold hover:text-black font-bold text-[11px] px-5 py-2 rounded-full transition uppercase tracking-widest flex items-center gap-2">
                            <span>{{ Str::limit(Auth::user()->name, 10) }}</span>
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="bg-rose-900/80 text-rose-200 border border-rose-700 font-bold text-[11px] px-4 py-2 rounded-full transition uppercase tracking-widest">
                            Panel ERP
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="bg-neutral-800 hover:bg-neutral-700 text-gray-200 font-bold text-[11px] px-5 py-2 rounded-full transition uppercase tracking-widest">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT: GRID MENU -->
    <main class="max-w-7xl mx-auto px-6 pt-16 pb-24 flex-1">
        <div class="text-center max-w-xl mx-auto mb-10 space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold block">NOTTE COFFEE</span>
            <h1 class="font-serif-title font-bold text-4xl md:text-5xl text-gray-100">Our Menu.</h1>
            <p class="text-xs text-gray-400 font-light leading-relaxed">
                <em>"Good Coffee. Fair Price."</em>
            </p>
        </div>

        <div class="flex justify-center items-center gap-3 mb-12 flex-wrap">
            <button onclick="filterCategory('all')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md bg-notte-gold text-black uppercase tracking-wider">Semua Menu</button>
            <button onclick="filterCategory('Coffee')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md border border-neutral-800 text-gray-400 hover:text-notte-gold uppercase tracking-wider">Coffee</button>
            <button onclick="filterCategory('1 Liter')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md border border-neutral-800 text-gray-400 hover:text-notte-gold uppercase tracking-wider">1 Liter</button>
            <button onclick="filterCategory('Non-Coffee')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md border border-neutral-800 text-gray-400 hover:text-notte-gold uppercase tracking-wider">Food</button>
            <button onclick="filterCategory('Food')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md border border-neutral-800 text-gray-400 hover:text-notte-gold uppercase tracking-wider">Food</button>
            <button onclick="filterCategory('Dessert')" class="category-btn text-xs font-bold px-6 py-2.5 rounded-md border border-neutral-800 text-gray-400 hover:text-notte-gold uppercase tracking-wider">Dessert</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($menus as $menu)
            <div class="menu-card bg-notte-card border border-neutral-800/80 group flex flex-col justify-between overflow-hidden rounded-lg hover:border-notte-gold/40 transition-all" data-category="{{ $menu->category }}">
                <div class="aspect-[4/5] overflow-hidden relative bg-neutral-900">
                    <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-notte-card via-transparent to-transparent opacity-80"></div>
                </div>
                <div class="p-6 flex flex-col flex-grow justify-between">
                    <div>
                        <span class="text-[10px] font-mono tracking-widest uppercase text-notte-gold block mb-1">{{ $menu->category }}</span>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2">{{ $menu->name }}</h3>
                        <p class="text-xs text-gray-400 mb-6 line-clamp-2">{{ $menu->description }}</p>
                    </div>
                    <div class="pt-4 border-t border-neutral-800/80 flex justify-between items-center mt-auto">
                        <span class="font-serif-title text-xl text-gray-200">Rp{{ number_format($menu->selling_price, 0, ',', '.') }}</span>
                        @if(in_array($menu->category, ['1 Liter', 'Food', 'Dessert']))
                            <button onclick="addDirectToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{$menu->selling_price }})" class="text-xs font-bold bg-notte-gold/10 text-notte-gold border border-notte-gold px-5 py-2.5 rounded-md uppercase">Tambahkan</button>
                        @else
                            <button onclick="openModifierModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{$menu->selling_price }})" class="text-xs font-bold border border-neutral-700 text-gray-300 hover:text-notte-gold px-5 py-2.5 rounded-md uppercase">Pilih Varian</button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-20 text-gray-500 font-mono text-xs">Belum ada menu yang tersedia.</div>
            @endforelse
        </div>
    </main>

    <!-- MODAL MODIFIER -->
    <div id="modifier-modal" class="fixed inset-0 bg-notte-black/90 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-notte-card border border-neutral-800 w-full max-w-md p-8 rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-neutral-800 pb-4">
                <div>
                    <span class="text-[10px] font-mono uppercase text-notte-gold block">Custom Order</span>
                    <h2 id="modal-menu-name" class="font-serif-title text-2xl text-gray-100">Nama Menu</h2>
                </div>
                <button onclick="closeModifierModal()" class="text-gray-500 hover:text-white text-lg">✕</button>
            </div>
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-400 mb-2">Ice Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('ice', 'Normal Ice')" class="opt-ice text-xs border border-notte-gold bg-notte-gold text-black py-2.5 rounded-lg font-bold">Normal Ice</button>
                        <button type="button" onclick="selectOption('ice', 'Less Ice')" class="opt-ice text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg">Less Ice</button>
                        <button type="button" onclick="selectOption('ice', 'No Ice')" class="opt-ice text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg">No Ice</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-400 mb-2">Sugar Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('sugar', 'Normal Sugar')" class="opt-sugar text-xs border border-notte-gold bg-notte-gold text-black py-2.5 rounded-lg font-bold">Normal Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Less Sugar')" class="opt-sugar text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg">Less Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Extra Sugar')" class="opt-sugar text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg">Extra Sugar</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase text-gray-400 mb-1">Catatan Khusus</label>
                    <input type="text" id="modal-custom-note" placeholder="Contoh: Pisah es batu" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 outline-none">
                </div>
            </div>
            <div class="border-t border-neutral-800 pt-6 flex justify-between items-center">
                <span id="modal-menu-price" class="font-serif-title text-xl text-notte-gold">Rp0</span>
                <button type="button" onclick="confirmAddToCart()" class="bg-notte-gold text-black font-extrabold text-xs uppercase px-6 py-3.5 rounded-md">Masukkan Keranjang</button>
            </div>
        </div>
    </div>

    <!-- DRAWER CART & CHECKOUT -->
    <div id="cart-drawer" class="fixed inset-0 bg-notte-black/80 backdrop-blur-sm z-50 hidden flex justify-end">
        <div class="w-full max-w-md bg-notte-card border-l border-neutral-800 h-full p-6 flex flex-col justify-between overflow-y-auto">
            <div>
                <div class="flex justify-between items-center pb-4 border-b border-neutral-800 mb-6">
                    <h2 class="font-serif-title text-2xl text-gray-100">Pesanan Anda</h2>
                    <button onclick="toggleCart()" class="text-gray-500 hover:text-white text-xl font-bold">✕</button>
                </div>
                
                <!-- ONSUBMIT DITAMBAH DI SINI -->
                <form id="checkout-form" action="{{ route('customer.checkout') }}" method="POST" onsubmit="return disableSubmitButton()">
                    @csrf
                    <div id="cart-items-container" class="space-y-4 mb-6">
                        <p class="text-xs text-gray-500 text-center py-8 font-mono">Keranjang belanjaan masih kosong.</p>
                    </div>
                    
                    <div class="space-y-4 border-t border-neutral-800 pt-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-notte-gold">Informasi Pemesan</h3>
                        <div>
                            <input type="text" name="customer_name" value="{{ Auth::check() ? Auth::user()->name : '' }}" required placeholder="Nama Lengkap" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 outline-none mb-2">
                            <input type="text" name="customer_phone" value="{{ Auth::check() ? Auth::user()->phone : '' }}" required placeholder="Nomor WhatsApp" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 outline-none mb-2">
                            <select name="order_type" id="order_type" onchange="toggleAddress()" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 outline-none">
                                <option value="delivery">Delivery (Anter ke Alamat)</option>
                                <option value="pickup">Pickup (Ambil di Outlet)</option>
                            </select>
                        </div>

                        <!-- FIELD ALAMAT & PETA INTERAKTIF -->
                        <div id="address-field" class="space-y-3 relative bg-neutral-900/50 p-3 rounded-md border border-neutral-800">
                            
                            <!-- Header Peta & Tombol GPS -->
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lokasi Pengiriman (Max 3 KM)</label>
                                <button type="button" onclick="getCurrentLocation()" class="text-[9px] bg-neutral-800 hover:bg-neutral-700 border border-neutral-700 text-notte-gold px-2 py-1 rounded flex items-center gap-1 transition uppercase tracking-widest">
                                    📍 Deteksi GPS
                                </button>
                            </div>
                            
                            <!-- Peta Leaflet -->
                            <div id="leaflet-map" class="border border-neutral-700"></div>
                            
                            <!-- Textarea Alamat (Bisa diketik, bisa auto-fill) -->
                            <div class="relative">
                                <textarea name="shipping_address" id="shipping_address" rows="2" placeholder="Geser pin di peta, klik Deteksi GPS, atau ketik alamat lu..." oninput="handleAddressInput()" class="w-full bg-notte-black border border-neutral-700 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">{{ Auth::check() ? Auth::user()->address : '' }}</textarea>
                                
                                <!-- DROPDOWN SUGGESTION ALAMAT -->
                                <div id="address-suggestions" class="hidden absolute left-0 right-0 top-[100%] mt-1 bg-[#1a1a1a] border border-neutral-700 rounded-md shadow-sm z-50 max-h-48 overflow-y-auto divide-y divide-neutral-800"></div>
                            </div>

                            <!-- Indikator Jarak -->
                            <div id="distance-info" class="p-3 rounded-lg text-xs font-semibold flex items-center justify-between bg-neutral-950 border border-neutral-800">
                                <span id="distance-text" class="text-gray-400">Pilih titik di peta...</span>
                                <span id="delivery-fee-badge" class="font-bold text-emerald-400"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="border-t border-neutral-800 pt-6 mt-6 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Total Pembayaran</span>
                    <span id="cart-total" class="font-serif-title text-2xl text-notte-gold">Rp0</span>
                </div>

                <div id="checkout-action-container">
                    <button type="submit" id="submit-checkout-btn" form="checkout-form" class="w-full bg-notte-gold text-black font-extrabold text-xs tracking-widest uppercase py-4 rounded-md">Konfirmasi Checkout</button>

                    <!-- TOMBOL SHOPEEFOOD HYBRID -->
                    <a id="shopeefood-btn" 
                       onclick="openShopeeFood(event)"
                       href="https://shopee.co.id/universal-link/now-food/shop/23386724?deep_and_deferred=1&shareChannel=whatsapp" 
                       target="_blank" 
                       class="hidden w-full bg-orange-600 hover:bg-orange-500 text-white font-extrabold text-xs tracking-widest uppercase py-4 rounded-md text-center flex items-center justify-center gap-2 cursor-pointer">
                        <span>Pesan via ShopeeFood</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP KHUSUS PC SHOPEEFOOD -->
    <div id="shopeefood-pc-modal" class="fixed inset-0 bg-notte-black/90 backdrop-blur-sm z-[80] hidden flex items-center justify-center p-4">
        <div class="bg-notte-card border border-notte-gold/60 w-full max-w-sm p-6 rounded-lg shadow-sm space-y-4 text-center">
            <div>
                <span class="text-[10px] font-mono uppercase tracking-widest text-notte-gold block mb-1">Pemberitahuan ShopeeFood</span>
                <h3 class="font-serif-title text-xl font-bold text-gray-100">Khusus Aplikasi HP</h3>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed">
                Layanan ShopeeFood cuma bisa dipesan lewat aplikasi Shopee di HP kamu!
            </p>
            <div class="pt-2 border-t border-neutral-800 space-y-2">
                <button type="button" onclick="document.getElementById('shopeefood-pc-modal').classList.add('hidden')" class="w-full bg-notte-gold text-black font-extrabold text-xs uppercase tracking-widest py-3 rounded-md hover:bg-amber-600 transition">
                    Oke
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-neutral-800 bg-notte-black py-8 px-6 text-xs text-gray-500 font-mono">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; {{ date('Y') }} NOTTE Coffee. Good Coffee. Fair Price.</p>
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="hover:text-notte-gold transition">← Kembali ke Landing Page</a>
            </div>
        </div>
    </footer>

    <script>
        const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
        const loginUrl = "{{ route('login', ['message' => 'login_required', 'redirect' => 'menu']) }}";

        let cart = {};
        let activeItem = null;
        let selectedIce = 'Normal Ice';
        let selectedSugar = 'Normal Sugar';

        // KOORDINAT FIX NOTTE JATIMURNI
        const NOTTE_LAT = -6.31971;
        const NOTTE_LNG = 106.92484;
        let map, userMarker, outletMarker;
        let debounceTimer;

        // DISABLE SUBMIT BUTTON BIAR GAK SPAM
        function disableSubmitButton() {
            const btn = document.getElementById('submit-checkout-btn');
            
            // Kalau keranjang kosong, cegah submit
            if (Object.keys(cart).length === 0) {
                alert("Keranjang kamu masih kosong!");
                return false;
            }

            // Matiin tombol biar gak bisa diklik 2x
            btn.disabled = true;
            btn.innerHTML = 'MEMPROSES... ⏳';
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-amber-600');
            
            return true; 
        }

        // FUNGSI HANDLER HYBRID SHOPEEFOOD
        function openShopeeFood(event) {
            const shopeeFoodAppUrl = "https://shopee.co.id/universal-link/now-food/shop/23386724?deep_and_deferred=1&shareChannel=whatsapp";
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (!isMobile) {
                event.preventDefault();
                // Munculkan modal khusus PC
                document.getElementById('shopeefood-pc-modal').classList.remove('hidden');
            } else {
                window.location.href = shopeeFoodAppUrl;
            }
        }

        function initMap() {
            if (map) return;
            map = L.map('leaflet-map').setView([NOTTE_LAT, NOTTE_LNG], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            outletMarker = L.marker([NOTTE_LAT, NOTTE_LNG]).addTo(map)
                .bindPopup('<b>NOTTE Coffee Jatimurni</b>').openPopup();

            // EVENT KLIK DI PETA (PIN PINDAH OTOMATIS)
            map.on('click', function(e) {
                updateUserMarker(e.latlng.lat, e.latlng.lng, true);
            });
        }

        function toggleCart() { 
            const drawer = document.getElementById('cart-drawer');
            drawer.classList.toggle('hidden');
            if (!drawer.classList.contains('hidden')) {
                setTimeout(() => {
                    initMap();
                    const currentAddress = document.getElementById('shipping_address').value.trim();
                    if(currentAddress) searchLocationNominatim(currentAddress, false);
                }, 300);
            }
        }

        function toggleAddress() {
            const type = document.getElementById('order_type').value;
            const addrField = document.getElementById('address-field');
            if (type === 'delivery') {
                addrField.style.display = 'block';
                if(map) map.invalidateSize();
                const currentAddress = document.getElementById('shipping_address').value.trim();
                if(currentAddress) searchLocationNominatim(currentAddress, false);
            } else {
                addrField.style.display = 'none';
                enableCheckout("Pickup Outlet (Bebas Radius)", true);
            }
        }

        function getHaversineDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; 
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
        }

        // FUNGSI TARIK LOKASI GPS CUSTOMER
        function getCurrentLocation() {
            if (navigator.geolocation) {
                document.getElementById('distance-text').innerText = "Mencari GPS...";
                navigator.geolocation.getCurrentPosition(
                    (position) => updateUserMarker(position.coords.latitude, position.coords.longitude, true),
                    (error) => alert("Gagal akses GPS. Pastikan izin lokasi aktif atau ketik manual/geser peta.")
                );
            } else {
                alert("Browser kamu gak mendukung GPS.");
            }
        }

        // FUNGSI UTAMA PIN & JARAK
        function updateUserMarker(lat, lng, doReverseGeocode = false) {
            if (!map) initMap();

            if (userMarker) {
                userMarker.setLatLng([lat, lng]);
            } else {
                userMarker = L.marker([lat, lng], { draggable: true }).addTo(map)
                    .bindPopup('Geser pin ini ke titik pas rumah kamu').openPopup();
                
                userMarker.on('dragend', function(e) {
                    const pos = userMarker.getLatLng();
                    updateUserMarker(pos.lat, pos.lng, true);
                });
            }

            const group = L.featureGroup([outletMarker, userMarker]);
            map.fitBounds(group.getBounds().pad(0.3));

            const distance = getHaversineDistance(NOTTE_LAT, NOTTE_LNG, lat, lng);
            const distanceFormatted = distance.toFixed(1);

            if (distance <= 3.0) {
                enableCheckout(`Jarak ${distanceFormatted} KM (Bebas Ongkir)`, true);
            } else {
                disableCheckout(`Jarak ${distanceFormatted} KM. Maks delivery 3 KM.`);
            }

            if (doReverseGeocode) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            document.getElementById('shipping_address').value = data.display_name;
                        }
                    });
            }
        }

        // FUNGSI KETIK ALAMAT (AUTOCOMPLETE)
        function handleAddressInput() {
            clearTimeout(debounceTimer);
            const query = document.getElementById('shipping_address').value.trim();
            const suggestionsBox = document.getElementById('address-suggestions');

            if (query.length < 4) {
                suggestionsBox.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5`)
                    .then(res => res.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data && data.length > 0) {
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'p-3 hover:bg-neutral-800 cursor-pointer text-xs text-gray-300 transition';
                                div.innerText = item.display_name;
                                div.onclick = () => {
                                    document.getElementById('shipping_address').value = item.display_name;
                                    suggestionsBox.classList.add('hidden');
                                    updateUserMarker(parseFloat(item.lat), parseFloat(item.lon), false);
                                };
                                suggestionsBox.appendChild(div);
                            });
                            suggestionsBox.classList.remove('hidden');
                        } else {
                            suggestionsBox.classList.add('hidden');
                        }
                    }).catch(() => suggestionsBox.classList.add('hidden'));
            }, 500);
        }

        function searchLocationNominatim(address, doReverse = false) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&countrycodes=id&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        updateUserMarker(parseFloat(data[0].lat), parseFloat(data[0].lon), doReverse);
                    }
                });
        }

        function enableCheckout(msg, isFree) {
            document.getElementById('submit-checkout-btn').classList.remove('hidden');
            document.getElementById('shopeefood-btn').classList.add('hidden');
            document.getElementById('distance-text').innerText = msg;
            document.getElementById('distance-text').className = "text-emerald-400 font-bold";
            document.getElementById('delivery-fee-badge').innerText = isFree ? "Ongkir Rp0" : "";
        }

        function disableCheckout(msg) {
            document.getElementById('submit-checkout-btn').classList.add('hidden');
            document.getElementById('shopeefood-btn').classList.remove('hidden');
            document.getElementById('distance-text').innerText = msg;
            document.getElementById('distance-text').className = "text-rose-400 font-bold";
            document.getElementById('delivery-fee-badge').innerText = "Lebih dari 3 KM";
        }

        // FILTER KATEGORI MENU
        function filterCategory(category) {
            const cards = document.querySelectorAll('.menu-card');
            const btns = document.querySelectorAll('.category-btn');

            btns.forEach(btn => {
                btn.className = "category-btn text-xs font-bold px-6 py-2.5 rounded-full transition uppercase tracking-wider border border-neutral-800 text-gray-400 hover:border-notte-gold hover:text-notte-gold";
            });
            event.target.className = "category-btn text-xs font-bold px-6 py-2.5 rounded-full transition uppercase tracking-wider bg-notte-gold text-black";

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all') {
                    card.style.display = 'flex';
                } else {
                    card.style.display = (cardCat === category) ? 'flex' : 'none';
                }
            });
        }

        // TAMBAH LANGSUNG (FOOD / DESSERT / 1 LITER)
        function addDirectToCart(id, name, price) {
            if (!isAuthenticated) {
                window.location.href = loginUrl;
                return;
            }
            const cartKey = `${id}_Direct`;
            if (cart[cartKey]) cart[cartKey].quantity += 1;
            else cart[cartKey] = { menu_id: id, name: name, price: price, quantity: 1, note: '-' };
            renderCart();
            showToast(`"${name}" ditambahkan ke keranjang.`);
        }

        function openModifierModal(id, name, price) {
            if (!isAuthenticated) {
                window.location.href = loginUrl;
                return;
            }
            activeItem = { id: id, name: name, price: price };
            selectedIce = 'Normal Ice'; selectedSugar = 'Normal Sugar';
            document.getElementById('modal-custom-note').value = '';
            document.getElementById('modal-menu-name').innerText = name;
            document.getElementById('modal-menu-price').innerText = 'Rp' + price.toLocaleString('id-ID');
            resetOptionUI('ice', 'Normal Ice'); resetOptionUI('sugar', 'Normal Sugar');
            document.getElementById('modifier-modal').classList.remove('hidden');
        }

        function closeModifierModal() { document.getElementById('modifier-modal').classList.add('hidden'); }

        function selectOption(type, value) {
            if (type === 'ice') selectedIce = value;
            if (type === 'sugar') selectedSugar = value;
            resetOptionUI(type, value);
        }

        function resetOptionUI(type, selectedValue) {
            document.querySelectorAll(`.opt-${type}`).forEach(btn => {
                btn.className = btn.innerText === selectedValue 
                    ? `opt-${type} text-xs border border-notte-gold bg-notte-gold text-black py-2.5 rounded-lg font-bold`
                    : `opt-${type} text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg hover:border-notte-gold`;
            });
        }

        function confirmAddToCart() {
            if (!activeItem) return;
            const customNote = document.getElementById('modal-custom-note').value.trim();
            let noteCombined = `${selectedIce}, ${selectedSugar}`;
            if (customNote) noteCombined += ` (${customNote})`;
            const cartKey = `${activeItem.id}_${selectedIce}_${selectedSugar}_${customNote}`;

            if (cart[cartKey]) cart[cartKey].quantity += 1;
            else cart[cartKey] = { menu_id: activeItem.id, name: activeItem.name, price: activeItem.price, quantity: 1, note: noteCombined };

            closeModifierModal(); 
            renderCart();
            showToast(`"${activeItem.name}" ditambahkan.`);
        }

        function showToast(message) {
            const toast = document.getElementById('toast-notification');
            document.getElementById('toast-message').innerText = message;
            
            toast.classList.remove('translate-x-[150%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-[150%]', 'opacity-0');
            }, 3000);
        }

        function changeQty(key, delta) {
            if (cart[key]) {
                cart[key].quantity += delta;
                if (cart[key].quantity <= 0) delete cart[key];
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items-container');
            let html = '', total = 0, totalCount = 0;

            if (Object.keys(cart).length === 0) {
                container.innerHTML = '<p class="text-xs text-gray-500 text-center py-8 font-mono">Keranjang belanjaan masih kosong.</p>';
                document.getElementById('cart-total').innerText = 'Rp0';
                document.getElementById('cart-count').innerText = '0';
                return;
            }

            for (let key in cart) {
                let item = cart[key];
                total += item.price * item.quantity; totalCount += item.quantity;
                html += `
                    <div class="flex justify-between items-center border-b border-neutral-800 pb-3">
                        <div class="flex-1 pr-2">
                            <p class="font-serif-title text-sm text-gray-200">${item.name}</p>
                            <p class="text-[11px] text-notte-gold">${item.note}</p>
                            <p class="text-[11px] text-gray-400 font-mono">Rp${item.price.toLocaleString('id-ID')} x ${item.quantity}</p>
                            <input type="hidden" name="cart[${key}][menu_id]" value="${item.menu_id}">
                            <input type="hidden" name="cart[${key}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="cart[${key}][note]" value="${item.note}">
                        </div>
                        <div class="flex items-center space-x-2 border border-neutral-800 rounded-lg px-2 py-1 bg-notte-black">
                            <button type="button" onclick="changeQty('${key}', -1)" class="text-xs text-gray-400 hover:text-white">-</button>
                            <span class="text-xs text-gray-200 font-bold px-1">${item.quantity}</span>
                            <button type="button" onclick="changeQty('${key}', 1)" class="text-xs text-gray-400 hover:text-white">+</button>
                        </div>
                    </div>`;
            }
            container.innerHTML = html;
            document.getElementById('cart-total').innerText = 'Rp' + total.toLocaleString('id-ID');
            document.getElementById('cart-count').innerText = totalCount;
        }

        // Live Polling Status Pesanan Aktif
        let activeInvoice = "{{ session('active_invoice') }}" || localStorage.getItem('active_invoice');
        if ("{{ session('active_invoice') }}") {
            localStorage.setItem('active_invoice', "{{ session('active_invoice') }}");
        }

        let pollingInterval = null;
        let lastKnownStatus = activeInvoice ? localStorage.getItem('status_' + activeInvoice) : null;

        function initLiveTracker() {
            if (!activeInvoice) return;
            if (localStorage.getItem('closed_tracker_' + activeInvoice) === 'true') return;

            const bar = document.getElementById('live-tracker-bar');
            if (bar) {
                bar.classList.remove('hidden');
                document.getElementById('live-invoice-label').innerText = 'Pesanan Aktif: ' + activeInvoice;
                document.getElementById('live-tracker-link').href = '/order/' + activeInvoice;
            }

            checkLiveStatus();
            if (!pollingInterval) {
                pollingInterval = setInterval(checkLiveStatus, 4000);
            }
        }

        function closeTracker() {
            if (activeInvoice) {
                localStorage.setItem('closed_tracker_' + activeInvoice, 'true');
            }
            const bar = document.getElementById('live-tracker-bar');
            if (bar) bar.classList.add('hidden');
            if (pollingInterval) clearInterval(pollingInterval);
        }

        function checkLiveStatus() {
            if (!activeInvoice) return;
            if (localStorage.getItem('closed_tracker_' + activeInvoice) === 'true') return;

            fetch('/order/' + activeInvoice, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Order not found');
                return res.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const statusData = doc.getElementById('order-status-data');
                
                if (statusData) {
                    const status = statusData.getAttribute('data-status');
                    const bar = document.getElementById('live-tracker-bar');
                    const textEl = document.getElementById('live-status-text');
                    const pingDot = document.getElementById('tracker-ping-dot');
                    const solidDot = document.getElementById('tracker-solid-dot');

                    if (!bar) return;

                    localStorage.setItem('status_' + activeInvoice, status);

                    if (status === 'completed') {
                        textEl.innerText = "Pesanan Selesai! Silakan ambil pesanan kamu.";
                        // FIX: Stable fixed positioning — no animate-bounce, no oversized shadow
                        bar.className = "fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md bg-[#062412]/95 backdrop-blur-md border border-emerald-500 p-3.5 sm:p-4 rounded-xl shadow-lg flex items-center justify-between gap-3 transition-all duration-500";
                        if (pingDot && solidDot) {
                            pingDot.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75";
                            solidDot.className = "relative inline-flex rounded-full h-3 w-3 bg-emerald-500";
                        }
                        if (lastKnownStatus !== 'completed') {
                            // Web Audio API chime — primary alert for mobile browsers
                            try {
                                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                                const playTone = (freq, startAt, duration, gain = 0.4) => {
                                    const osc = ctx.createOscillator();
                                    const gainNode = ctx.createGain();
                                    osc.connect(gainNode);
                                    gainNode.connect(ctx.destination);
                                    osc.type = 'sine';
                                    osc.frequency.setValueAtTime(freq, ctx.currentTime + startAt);
                                    gainNode.gain.setValueAtTime(gain, ctx.currentTime + startAt);
                                    gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + startAt + duration);
                                    osc.start(ctx.currentTime + startAt);
                                    osc.stop(ctx.currentTime + startAt + duration);
                                };
                                playTone(880, 0.0,  0.18); // A5 — first ding
                                playTone(1046, 0.2, 0.25); // C6 — second ding (higher)
                                playTone(1318, 0.45, 0.35); // E6 — final bright note
                            } catch (e) { /* AudioContext not available — silent fallback */ }
                            // Vibration as secondary fallback
                            if ("vibrate" in navigator) {
                                navigator.vibrate([500, 200, 200, 100, 200, 100, 200]);
                            }
                        }
                        lastKnownStatus = 'completed';
                        clearInterval(pollingInterval);
                    } else if (status === 'processing') {
                        textEl.innerText = "Pesanan kamu sedang diracik oleh Barista";
                        bar.className = "fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md bg-[#121212]/95 backdrop-blur-md border border-blue-500/60 p-3.5 sm:p-4 rounded-xl shadow-lg flex items-center justify-between gap-3";
                        if (pingDot && solidDot) {
                            pingDot.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75";
                            solidDot.className = "relative inline-flex rounded-full h-3 w-3 bg-blue-500";
                        }
                        lastKnownStatus = 'processing';
                    } else if (status === 'cancelled') {
                        textEl.innerText = "Pesanan Dibatalkan oleh admin/staff.";
                        bar.className = "fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-md bg-rose-950/95 backdrop-blur-md border border-rose-600 p-3.5 sm:p-4 rounded-xl shadow-lg flex items-center justify-between gap-3";
                        if (pingDot && solidDot) {
                            pingDot.className = "animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75";
                            solidDot.className = "relative inline-flex rounded-full h-3 w-3 bg-rose-500";
                        }
                        lastKnownStatus = 'cancelled';
                        clearInterval(pollingInterval);
                    } else if (status === 'waiting_verification') {
                        textEl.innerText = "Menunggu kasir memverifikasi pembayaran kamu.";
                        lastKnownStatus = 'waiting_verification';
                    } else {
                        textEl.innerText = "Menunggu pembayaran QRIS.";
                        lastKnownStatus = status;
                    }
                }
            })
            .catch(err => {
                console.log(err);
            });
        }

        initLiveTracker();
    </script>
</body>
</html>