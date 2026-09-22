@extends('layouts.admin')

@section('content')
<div class="h-[calc(100vh-100px)] flex flex-col md:flex-row gap-6 relative" x-data="posSystem()">
    
    <!-- KIRI: Grid Menu Katalog Kasir -->
    <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col overflow-hidden">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">POS Kasir NOTTE</h2>
                <p class="text-xs text-gray-500">Pilih menu & sesuaikan varian pesanan kasir</p>
            </div>
            <div class="flex space-x-3">
                <input type="text" x-model="search" placeholder="Cari menu..." class="border border-gray-300 px-3 py-1.5 rounded-md text-xs w-48 focus:outline-none focus:ring-1 focus:ring-amber-500">
                <button @click="showHistory = true" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-1.5 rounded-md text-xs font-bold shadow-sm flex items-center space-x-2">
                    <span>Riwayat POS</span>
                </button>
            </div>
        </div>

        <!-- Scrollable Grid Menu -->
        <div class="flex-1 overflow-y-auto pr-2 grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <template x-for="menu in filteredMenus" :key="menu.id">
                <div @click="addToCart(menu)" class="border border-gray-200 rounded-lg p-3 hover:border-amber-500 hover:shadow-md cursor-pointer transition flex flex-col justify-between bg-white group">
                    <div>
                        <div class="aspect-video w-full rounded bg-gray-100 overflow-hidden mb-2 relative">
                            <img :src="menu.image || 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=400&auto=format&fit=crop'" :alt="menu.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span class="absolute top-1 right-1 bg-black/70 text-amber-400 text-[10px] px-1.5 py-0.5 rounded uppercase tracking-wider font-bold" x-text="menu.category"></span>
                        </div>
                        <h4 class="font-bold text-sm text-gray-800 leading-tight" x-text="menu.name"></h4>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-xs font-bold text-amber-600" x-text="formatRupiah(menu.selling_price)"></span>
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">+ Tambah</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- KANAN: Sidebar Keranjang & Checkout -->
    <div class="w-full md:w-96 bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex justify-between items-center">
                <span>Keranjang Pesanan</span>
                <button @click="clearCart()" class="text-xs text-red-500 hover:underline font-normal" x-show="cart.length > 0">Kosongkan</button>
            </h3>

            <!-- Nama Pelanggan & Metode Bayar -->
            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Nama Pelanggan / Meja</label>
                    <input type="text" x-model="customerName" placeholder="Walk-in Customer" class="w-full border border-gray-300 p-2 rounded text-xs focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Metode Pembayaran</label>
                    <select x-model="paymentMethod" class="w-full border border-gray-300 p-2 rounded text-xs bg-white">
                        <option value="Cash">Cash / Tunai</option>
                        <option value="QRIS">QRIS / E-Wallet</option>
                        <option value="Debit">Kartu Debit/Kredit</option>
                    </select>
                </div>
            </div>

            <!-- List Items in Cart -->
            <div class="max-h-[300px] overflow-y-auto space-y-3 pr-1 border-t border-b border-gray-100 py-3">
                <template x-if="cart.length === 0">
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-xs">Keranjang masih kosong</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex flex-col bg-gray-50 p-2.5 rounded border border-gray-100 space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-xs text-gray-800" x-text="item.name"></span>
                            <span class="text-xs font-bold text-gray-700" x-text="formatRupiah(item.selling_price * item.quantity)"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                            <select x-model="item.ice_level" class="border border-gray-200 p-1 rounded bg-white text-gray-600">
                                <option value="Normal Ice">Normal Ice</option>
                                <option value="Less Ice">Less Ice</option>
                                <option value="No Ice">No Ice</option>
                            </select>
                            <select x-model="item.sugar_level" class="border border-gray-200 p-1 rounded bg-white text-gray-600">
                                <option value="Normal Sugar">Normal Sugar</option>
                                <option value="Less Sugar">Less Sugar</option>
                                <option value="Extra Sugar">Extra Sugar</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <button @click="removeFromCart(index)" class="text-[10px] text-red-500 hover:underline">Hapus</button>
                            <div class="flex items-center space-x-2">
                                <button @click="updateQty(index, -1)" class="w-5 h-5 bg-gray-200 text-gray-700 rounded flex items-center justify-center font-bold text-xs hover:bg-gray-300">-</button>
                                <span class="text-xs font-bold w-4 text-center" x-text="item.quantity"></span>
                                <button @click="updateQty(index, 1)" class="w-5 h-5 bg-gray-200 text-gray-700 rounded flex items-center justify-center font-bold text-xs hover:bg-gray-300">+</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Total & Tombol Bayar -->
        <div class="pt-4 border-t border-gray-200 mt-4">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-gray-600 uppercase">Total Tagihan</span>
                <span class="text-lg font-bold text-amber-600" x-text="formatRupiah(calculateTotal())"></span>
            </div>

            <button @click="processPayment()" :disabled="cart.length === 0 || loading" class="w-full bg-amber-600 hover:bg-amber-700 disabled:bg-gray-300 text-white font-bold py-3 rounded text-xs uppercase tracking-wider transition shadow flex justify-center items-center space-x-2">
                <span x-show="!loading">Proses & Bayar Transaksi</span>
                <span x-show="loading" class="animate-spin">⏳</span>
            </button>
        </div>
    </div>

    <!-- MODAL / DRAWER RIWAYAT OFFLINE -->
    <div x-show="showHistory" style="display: none;" class="absolute inset-0 z-50 flex justify-end">
        <div class="absolute inset-0 bg-black/50" @click="showHistory = false"></div>
        
        <div class="w-full md:w-[450px] bg-white h-full relative z-10 shadow-xl border-l border-gray-200 flex flex-col transform transition-transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Riwayat Transaksi POS</h3>
                    <p class="text-[11px] text-gray-500">20 Transaksi Offline Terakhir</p>
                </div>
                <button @click="showHistory = false" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                @forelse($recentPosOrders as $ro)
                    <div class="border border-gray-200 rounded-lg p-4 shadow-sm relative bg-white">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-[10px] text-gray-400">{{ $ro->created_at->format('d M Y, H:i') }}</span>
                                <h4 class="font-bold text-sm text-gray-800">{{ $ro->invoice_number }}</h4>
                            </div>
                            @if(strtolower($ro->status) == 'pending' || strtolower($ro->status) == '')
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-1 rounded uppercase">PENDING</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-1 rounded uppercase">SELESAI</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-end mt-3 pt-3 border-t border-gray-100">
                            <div>
                                <p class="text-xs font-semibold text-gray-700">{{ $ro->customer_name }}</p>
                                <p class="text-[10px] text-gray-500 uppercase">{{ $ro->payment_method }}</p>
                            </div>
                            <div class="text-right flex flex-col items-end">
                                <span class="font-extrabold text-sm text-amber-600 mb-1.5">Rp{{ number_format($ro->total_amount, 0, ',', '.') }}</span>
                                <!-- Tombol Detail yang Baru Ditambahin -->
                                <a href="{{ route('orders.show', $ro->id) }}" class="text-[10px] font-bold bg-slate-900 text-white px-3 py-1.5 rounded hover:bg-slate-800 transition">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-xs">Belum ada transaksi POS offline.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Alpine JS -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function posSystem() {
        return {
            showHistory: false,
            menus: @json($menus),
            search: '',
            customerName: 'Walk-in Customer',
            paymentMethod: 'Cash',
            cart: [],
            loading: false,

            get filteredMenus() {
                if (!this.search) return this.menus;
                return this.menus.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()));
            },
            addToCart(menu) {
                let existingIndex = this.cart.findIndex(i => i.id === menu.id);
                if (existingIndex > -1) {
                    this.cart[existingIndex].quantity++;
                } else {
                    this.cart.push({ id: menu.id, name: menu.name, selling_price: parseFloat(menu.selling_price), quantity: 1, ice_level: 'Normal Ice', sugar_level: 'Normal Sugar' });
                }
            },
            removeFromCart(index) { this.cart.splice(index, 1); },
            updateQty(index, change) {
                this.cart[index].quantity += change;
                if (this.cart[index].quantity <= 0) this.removeFromCart(index);
            },
            clearCart() { this.cart = []; },
            calculateTotal() { return this.cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0); },
            formatRupiah(number) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number); },
            async processPayment() {
                if (!this.customerName.trim()) { alert('Isi nama pelanggan/meja.'); return; }
                this.loading = true;
                const payload = {
                    customer_name: this.customerName, payment_method: this.paymentMethod,
                    items: this.cart.map(item => ({ menu_id: item.id, quantity: item.quantity, ice_level: item.ice_level, sugar_level: item.sugar_level }))
                };
                try {
                    const response = await fetch("{{ route('pos.store') }}", {
                        method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" }, body: JSON.stringify(payload)
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.clearCart();
                        this.customerName = 'Walk-in Customer';
                        window.location.reload(); 
                    } else { alert('Error: ' + result.message); }
                } catch (error) { alert('Gagal memproses.'); } finally { this.loading = false; }
            }
        }
    }
</script>
@endsection