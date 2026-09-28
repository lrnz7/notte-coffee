<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-notte-black { background-color: #080808; }
        .bg-notte-card { background-color: #121212; }
        .border-notte-gold { border-color: #c5a880; }
        .text-notte-gold { color: #c5a880; }
        .bg-notte-gold { background-color: #c5a880; }
    </style>
</head>
<body class="bg-notte-black min-h-screen flex flex-col justify-between antialiased selection:bg-amber-900 selection:text-amber-100">

    <!-- NAVBAR (100% Identik & Seirama dengan Landing Page) -->
    <nav class="fixed top-0 w-full z-50 bg-notte-black/90 backdrop-blur-md border-b border-neutral-800/60">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            
            <!-- Logo Official -->
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-8 md:h-10 w-auto object-contain">
            </a>

            <!-- Nav Links (Disamakan persis dengan Landing Page) -->
            <div class="hidden md:flex items-center gap-8 text-[11px] font-bold tracking-widest text-gray-400 uppercase">
                <a href="{{ route('home') }}#philosophy" class="hover:text-notte-gold transition">Our Philosophy</a>
                <a href="{{ route('home') }}#crafted" class="hover:text-notte-gold transition">The Craft</a>
                <a href="{{ route('home') }}#visit" class="hover:text-notte-gold transition">Visit Us</a>
            </div>

            <!-- Cart Trigger Button -->
            <div class="flex items-center space-x-4">
                <button onclick="toggleCart()" class="relative bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-md transition uppercase tracking-widest flex items-center gap-2">
                    <span>Keranjang</span>
                    <span id="cart-count" class="bg-black text-notte-gold text-[10px] w-5 h-5 rounded-sm flex items-center justify-center font-mono">0</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT: GRID MENU -->
    <main class="max-w-7xl mx-auto px-6 pt-32 pb-24 flex-1">
        <div class="text-center max-w-xl mx-auto mb-16 space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold block">Our Selection</span>
            <h1 class="font-serif-title font-bold text-4xl md:text-5xl text-gray-100">Artisanal Menu</h1>
            <p class="text-xs text-gray-400 font-light leading-relaxed">
                <em>"Setiap tegukan adalah cerita rasa yang diracik presisi."</em>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($menus as$menu)
            <div class="bg-notte-card border border-neutral-800/80 group flex flex-col justify-between hover:border-notte-gold/50 transition duration-500 overflow-hidden rounded-lg">
                
                <!-- Gambar Produk -->
                <div class="aspect-[4/5] overflow-hidden border-b border-neutral-800 relative bg-neutral-900">
                    <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop' }}" 
                         alt="{{ $menu->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-700 opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-notte-card via-transparent to-transparent opacity-80"></div>
                </div>

                <!-- Detail Produk -->
                <div class="p-6 flex flex-col flex-grow justify-between">
                    <div>
                        <span class="text-[10px] font-mono tracking-widest uppercase text-notte-gold block mb-1">{{ $menu->category }}</span>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2">{{ $menu->name }}</h3>
                        <p class="text-xs text-gray-400 leading-relaxed font-light mb-6 line-clamp-2">{{ $menu->description }}</p>
                    </div>

                    <div class="pt-4 border-t border-neutral-800/80 flex justify-between items-center mt-auto">
                        <span class="font-serif-title text-xl text-gray-200">Rp{{ number_format($menu->selling_price, 0, ',', '.') }}</span>
                        <button onclick="openModifierModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{$menu->selling_price }})" 
                            class="text-xs font-bold border border-neutral-700 text-gray-300 hover:border-notte-gold hover:text-notte-gold px-5 py-2.5 rounded-md transition uppercase tracking-wider">
                            + Tambah
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-20 text-gray-500 font-mono text-xs">Belum ada menu yang tersedia saat ini.</div>
            @endforelse
        </div>
    </main>

    <!-- MODAL POP-UP MODIFIER -->
    <div id="modifier-modal" class="fixed inset-0 bg-notte-black/90 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-notte-card border border-neutral-800 w-full max-w-md p-8 rounded-lg shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-neutral-800 pb-4">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-notte-gold block">Custom Order</span>
                    <h2 id="modal-menu-name" class="font-serif-title text-2xl text-gray-100">Nama Menu</h2>
                </div>
                <button onclick="closeModifierModal()" class="text-gray-500 hover:text-white text-lg">✕</button>
            </div>

            <!-- Form Opsi -->
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Ice Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('ice', 'Normal Ice')" class="opt-ice text-xs border border-notte-gold bg-notte-gold text-black py-2.5 rounded-lg font-bold">Normal Ice</button>
                        <button type="button" onclick="selectOption('ice', 'Less Ice')" class="opt-ice text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg hover:border-notte-gold">Less Ice</button>
                        <button type="button" onclick="selectOption('ice', 'No Ice')" class="opt-ice text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg hover:border-notte-gold">No Ice</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Sugar Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('sugar', 'Normal Sugar')" class="opt-sugar text-xs border border-notte-gold bg-notte-gold text-black py-2.5 rounded-lg font-bold">Normal Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Less Sugar')" class="opt-sugar text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg hover:border-notte-gold">Less Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Extra Sugar')" class="opt-sugar text-xs border border-neutral-800 text-gray-300 py-2.5 rounded-lg hover:border-notte-gold">Extra Sugar</button>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Catatan Khusus (Opsional)</label>
                    <input type="text" id="modal-custom-note" placeholder="Contoh: Pisah es batu" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 outline-none focus:border-notte-gold">
                </div>
            </div>

            <div class="border-t border-neutral-800 pt-6 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-500 uppercase block">Total Harga</span>
                    <span id="modal-menu-price" class="font-serif-title text-xl text-notte-gold">Rp0</span>
                </div>
                <button type="button" onclick="confirmAddToCart()" class="bg-notte-gold text-black font-extrabold text-xs uppercase tracking-widest px-6 py-3.5 rounded-md hover:bg-amber-600 transition">
                    Masukkan Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- DRAWER CART & CHECKOUT (Hanya buka pas diklik tombol Keranjang di Navbar) -->
    <div id="cart-drawer" class="fixed inset-0 bg-notte-black/80 backdrop-blur-sm z-50 hidden flex justify-end">
        <div class="w-full max-w-md bg-notte-card border-l border-neutral-800 h-full p-8 flex flex-col justify-between overflow-y-auto">
            <div>
                <div class="flex justify-between items-center pb-6 border-b border-neutral-800 mb-6">
                    <h2 class="font-serif-title text-2xl text-gray-100">Pesanan Anda</h2>
                    <button onclick="toggleCart()" class="text-gray-500 hover:text-white text-lg">✕</button>
                </div>
                
                <form id="checkout-form" action="{{ route('customer.checkout') }}" method="POST">
                    @csrf
                    <div id="cart-items-container" class="space-y-4 mb-8">
                        <p class="text-xs text-gray-500 text-center py-8 font-mono">Keranjang belanjaan masih kosong.</p>
                    </div>
                    
                    <div class="space-y-4 border-t border-neutral-800 pt-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-notte-gold">Informasi Pemesan</h3>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                            <input type="text" name="customer_name" required class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Nomor WhatsApp</label>
                            <input type="text" name="customer_phone" required placeholder="08123456789" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Tipe Pesanan</label>
                            <select name="order_type" id="order_type" onchange="toggleAddress()" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
                                <option value="delivery">Delivery (Anter ke Alamat)</option>
                                <option value="pickup">Pickup (Ambil di Outlet)</option>
                            </select>
                        </div>
                        <div id="address-field">
                            <label class="block text-xs text-gray-400 mb-1">Alamat Lengkap Pengiriman</label>
                            <textarea name="shipping_address" rows="2" class="w-full bg-notte-black border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="border-t border-neutral-800 pt-6 mt-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Total Pembayaran</span>
                    <span id="cart-total" class="font-serif-title text-2xl text-notte-gold">Rp0</span>
                </div>
                <button type="submit" form="checkout-form" class="w-full bg-notte-gold text-black font-extrabold text-xs tracking-widest uppercase py-4 rounded-md hover:bg-amber-600 transition">
                    Konfirmasi Checkout
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="border-t border-neutral-800 bg-notte-black py-8 px-6 text-xs text-gray-500 font-mono">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; {{ date('Y') }} NOTTE Coffee. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="hover:text-notte-gold transition">← Kembali ke Landing Page</a>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let cart = {};
        let activeItem = null;
        let selectedIce = 'Normal Ice';
        let selectedSugar = 'Normal Sugar';

        function toggleCart() { document.getElementById('cart-drawer').classList.toggle('hidden'); }
        function toggleAddress() {
            const type = document.getElementById('order_type').value;
            document.getElementById('address-field').style.display = type === 'delivery' ? 'block' : 'none';
        }

        function openModifierModal(id, name, price) {
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
            // toggleCart() DIHAPUS: Drawer keranjang tidak akan auto-open, user bisa bebas nambah menu sepuasnya!
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
    </script>
</body>
</html>