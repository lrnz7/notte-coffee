<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { black: { deep: '#0A0A0A', rich: '#121212' }, cream: '#F2EDE3', gold: { warm: '#C9A468', muted: '#B08D52' }, grey: { text: '#B5B0A8' }, hairline: '#2A2A2A' },
                    fontFamily: { fraunces: ['Fraunces', 'serif'], inter: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style> body { background-color: #0A0A0A; color: #F2EDE3; } </style>
</head>
<body class="font-inter min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <nav class="border-b border-hairline px-8 py-6 flex justify-between items-center bg-black-deep sticky top-0 z-40">
        <a href="{{ route('home') }}" class="flex flex-col items-center">
            <span class="font-fraunces text-2xl tracking-widest text-cream">NOTTE</span>
            <span class="text-[10px] tracking-wide text-gold-warm uppercase mt-0.5">C o f f e e</span>
        </a>
        <div class="flex items-center space-x-6">
            <a href="{{ route('home') }}" class="text-xs uppercase tracking-wide text-grey-text hover:text-cream">← Home</a>
            <button onclick="toggleCart()" class="relative border border-gold-warm text-gold-warm text-xs px-4 py-2 rounded hover:bg-gold-warm hover:text-black-deep transition">
                Cart (<span id="cart-count">0</span>)
            </button>
        </div>
    </nav>

    <!-- Content: Grid Menu dengan Foto -->
    <main class="container mx-auto px-6 py-16 flex-1">
        <div class="text-center max-w-xl mx-auto mb-16">
            <h1 class="font-fraunces font-light text-5xl text-cream mb-4">OUR SELECTION</h1>
            <p class="font-fraunces italic text-lg text-gold-warm">Setiap tegukan adalah cerita rasa yang diracik presisi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($menus as $menu)
            <div class="bg-black-rich border border-hairline group flex flex-col justify-between hover:border-gold-warm transition overflow-hidden rounded-sm">
                
                <!-- Gambar Produk -->
                <div class="aspect-[4/5] overflow-hidden border-b border-hairline relative">
                    <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop' }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black-deep via-transparent to-transparent opacity-80"></div>
                </div>

                <!-- Info Teks -->
                <div class="p-6 flex flex-col flex-grow">
                    <span class="text-[10px] tracking-widest uppercase text-gold-warm font-medium block mb-2">{{ $menu->category }}</span>
                    <h3 class="font-fraunces text-2xl text-cream mb-2">{{ $menu->name }}</h3>
                    <p class="text-xs text-grey-text leading-relaxed mb-6 flex-grow">{{ $menu->description }}</p>

                    <div class="pt-4 border-t border-hairline flex justify-between items-center mt-auto">
                        <span class="font-fraunces text-lg text-cream">Rp{{ number_format($menu->selling_price, 0, ',', '.') }}</span>
                        <button onclick="openModifierModal({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{$menu->selling_price }})" 
                            class="text-xs border border-hairline text-cream px-4 py-2 hover:border-gold-warm hover:text-gold-warm transition">
                            + Tambah
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-grey-text">Belum ada menu yang tersedia.</div>
            @endforelse
        </div>
    </main>

    <!-- Modal Pop-up Modifier -->
    <div id="modifier-modal" class="fixed inset-0 bg-black-deep bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-black-rich border border-hairline w-full max-w-md p-6 rounded shadow-2xl">
            <div class="flex justify-between items-center border-b border-hairline pb-4 mb-4">
                <div>
                    <span class="text-[10px] uppercase tracking-widest text-gold-warm block">Sesuaikan Pesanan</span>
                    <h2 id="modal-menu-name" class="font-fraunces text-2xl text-cream">Nama Menu</h2>
                </div>
                <button onclick="closeModifierModal()" class="text-grey-text hover:text-cream text-lg">✕</button>
            </div>

            <!-- Options Form -->
            <div class="space-y-5">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-grey-text mb-2">Ice Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('ice', 'Normal Ice')" class="opt-ice text-xs border border-gold-warm bg-gold-warm text-black-deep py-2 rounded font-medium">Normal Ice</button>
                        <button type="button" onclick="selectOption('ice', 'Less Ice')" class="opt-ice text-xs border border-hairline text-cream py-2 rounded hover:border-gold-warm">Less Ice</button>
                        <button type="button" onclick="selectOption('ice', 'No Ice')" class="opt-ice text-xs border border-hairline text-cream py-2 rounded hover:border-gold-warm">No Ice</button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-grey-text mb-2">Sugar Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectOption('sugar', 'Normal Sugar')" class="opt-sugar text-xs border border-gold-warm bg-gold-warm text-black-deep py-2 rounded font-medium">Normal Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Less Sugar')" class="opt-sugar text-xs border border-hairline text-cream py-2 rounded hover:border-gold-warm">Less Sugar</button>
                        <button type="button" onclick="selectOption('sugar', 'Extra Sugar')" class="opt-sugar text-xs border border-hairline text-cream py-2 rounded hover:border-gold-warm">Extra Sugar</button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-grey-text mb-1">Catatan Khusus (Opsional)</label>
                    <input type="text" id="modal-custom-note" placeholder="Contoh: Pisah es batu" class="w-full bg-black-deep border border-hairline p-2 text-xs text-cream outline-none focus:border-gold-warm">
                </div>
            </div>

            <div class="border-t border-hairline pt-4 mt-6 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-grey-text block uppercase">Harga</span>
                    <span id="modal-menu-price" class="font-fraunces text-xl text-gold-warm">Rp0</span>
                </div>
                <button type="button" onclick="confirmAddToCart()" class="bg-gold-warm text-black-deep font-bold text-xs uppercase tracking-widest px-6 py-3 rounded hover:bg-gold-muted transition">
                    Masukkan Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- Modal / Drawer Cart & Checkout -->
    <div id="cart-drawer" class="fixed inset-0 bg-black-deep bg-opacity-80 z-50 hidden flex justify-end">
        <div class="w-full max-w-md bg-black-rich border-l border-hairline h-full p-8 flex flex-col justify-between overflow-y-auto">
            <div>
                <div class="flex justify-between items-center pb-6 border-b border-hairline mb-6">
                    <h2 class="font-fraunces text-2xl text-cream">Pesanan Anda</h2>
                    <button onclick="toggleCart()" class="text-grey-text hover:text-cream text-lg">✕</button>
                </div>
                <form id="checkout-form" action="{{ route('customer.checkout') }}" method="POST">
                    @csrf
                    <div id="cart-items-container" class="space-y-4 mb-8">
                        <p class="text-xs text-grey-text text-center py-8">Keranjang belanjaan masih kosong.</p>
                    </div>
                    <div class="space-y-4 border-t border-hairline pt-6">
                        <h3 class="text-xs uppercase tracking-widest text-gold-warm">Informasi Pemesan</h3>
                        <div>
                            <label class="block text-xs text-grey-text mb-1">Nama Lengkap</label>
                            <input type="text" name="customer_name" required class="w-full bg-black-deep border border-hairline p-2.5 text-xs text-cream focus:border-gold-warm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-grey-text mb-1">Nomor WhatsApp</label>
                            <input type="text" name="customer_phone" required placeholder="08123456789" class="w-full bg-black-deep border border-hairline p-2.5 text-xs text-cream focus:border-gold-warm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-grey-text mb-1">Tipe Pesanan</label>
                            <select name="order_type" id="order_type" onchange="toggleAddress()" class="w-full bg-black-deep border border-hairline p-2.5 text-xs text-cream focus:border-gold-warm outline-none">
                                <option value="delivery">Delivery (Anter ke Alamat)</option>
                                <option value="pickup">Pickup (Ambil di Outlet)</option>
                            </select>
                        </div>
                        <div id="address-field">
                            <label class="block text-xs text-grey-text mb-1">Alamat Lengkap Pengiriman</label>
                            <textarea name="shipping_address" rows="2" class="w-full bg-black-deep border border-hairline p-2.5 text-xs text-cream focus:border-gold-warm outline-none"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="border-t border-hairline pt-6 mt-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xs text-grey-text uppercase tracking-wider">Total</span>
                    <span id="cart-total" class="font-fraunces text-2xl text-gold-warm">Rp0</span>
                </div>
                <button type="submit" form="checkout-form" class="w-full bg-gold-warm text-black-deep font-inter text-xs tracking-widest uppercase py-4 font-bold hover:bg-gold-muted transition">
                    Konfirmasi Checkout
                </button>
            </div>
        </div>
    </div>

    <!-- Script Logic -->
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
                    ? `opt-${type} text-xs border border-gold-warm bg-gold-warm text-black-deep py-2 rounded font-medium`
                    : `opt-${type} text-xs border border-hairline text-cream py-2 rounded hover:border-gold-warm`;
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

            closeModifierModal(); renderCart(); toggleCart();
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
                container.innerHTML = '<p class="text-xs text-grey-text text-center py-8">Keranjang belanjaan masih kosong.</p>';
                document.getElementById('cart-total').innerText = 'Rp0';
                document.getElementById('cart-count').innerText = '0';
                return;
            }

            for (let key in cart) {
                let item = cart[key];
                total += item.price * item.quantity; totalCount += item.quantity;
                html += `
                    <div class="flex justify-between items-center border-b border-hairline pb-3">
                        <div class="flex-1 pr-2">
                            <p class="font-fraunces text-sm text-cream">${item.name}</p>
                            <p class="text-[11px] text-gold-warm">${item.note}</p>
                            <p class="text-[11px] text-grey-text">Rp${item.price.toLocaleString('id-ID')} x ${item.quantity}</p>
                            <input type="hidden" name="cart[${key}][menu_id]" value="${item.menu_id}">
                            <input type="hidden" name="cart[${key}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="cart[${key}][note]" value="${item.note}">
                        </div>
                        <div class="flex items-center space-x-2 border border-hairline px-2 py-1">
                            <button type="button" onclick="changeQty('${key}', -1)" class="text-xs text-grey-text hover:text-cream">-</button>
                            <span class="text-xs text-cream font-bold px-1">${item.quantity}</span>
                            <button type="button" onclick="changeQty('${key}', 1)" class="text-xs text-grey-text hover:text-cream">+</button>
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