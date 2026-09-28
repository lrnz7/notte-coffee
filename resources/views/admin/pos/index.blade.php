@extends('layouts.admin')

@section('content')
<!-- Include POS Offline Engine Script -->
<script src="{{ asset('js/pos-offline-sync.js') }}"></script>

<div class="flex flex-col md:flex-row relative pb-24 md:pb-0 min-h-screen md:min-h-0 md:h-[calc(100vh-80px)] gap-4" x-data="posSystem()">
    
    <!-- KIRI: KDS & Grid Menu -->
    <div class="flex-1 flex flex-col w-full">
        
        <!-- TOP TOOLBAR: Status Online/Offline, Sync Button & Shift Closing -->
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 md:p-3.5 mb-3 shadow-sm flex flex-wrap items-center justify-between gap-2">
            <!-- Left: Network status & Offline queue badge -->
            <div class="flex items-center gap-2">
                <!-- Status Network Pill -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border"
                     :class="isOnline ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'">
                    <span class="w-2 h-2 rounded-full"
                          :class="isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                    <span x-text="isOnline ? 'ONLINE' : 'OFFLINE MODE'"></span>
                </div>

                <!-- Pending Offline Count Badge -->
                <template x-if="pendingOfflineCount > 0">
                    <span class="bg-amber-100 border border-amber-300 text-amber-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full flex items-center gap-1 animate-bounce">
                        ⚡ <span x-text="pendingOfflineCount"></span> Belum Sync
                    </span>
                </template>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex items-center gap-2">
                <!-- Manual Sync Button -->
                <button @click="triggerManualSync()" 
                        :disabled="isSyncing || pendingOfflineCount === 0"
                        class="bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold px-2.5 py-1.5 rounded-md text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition">
                    <svg class="w-3.5 h-3.5" :class="isSyncing ? 'animate-spin text-amber-600' : 'text-slate-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span x-text="isSyncing ? 'Syncing...' : 'Sync Offline'"></span>
                    <span x-show="pendingOfflineCount > 0" class="bg-amber-500 text-slate-900 font-black text-[9px] px-1.5 py-0.2 rounded-full" x-text="pendingOfflineCount"></span>
                </button>

                <!-- Tutup Kasir / Closing Shift Button -->
                <button @click="openClosingModal()" 
                        class="bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-900 font-extrabold px-3 py-1.5 rounded-md text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <span>Closing Shift</span>
                </button>
            </div>
        </div>
        
        <!-- PANEL ANTREAN DAPUR (KDS) -->
        <div class="bg-slate-900 p-3 md:p-4 shadow-md text-white mb-3 md:rounded-lg shrink-0 w-full relative z-10">
            <div class="flex justify-between items-center mb-2.5">
                <h3 class="font-bold text-xs md:text-sm tracking-wide flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    DAPUR (AKTIF: <span x-text="activeOrders.length"></span>/5)
                </h3>
            </div>
            
            <div class="flex overflow-x-auto gap-2.5 pb-1 snap-x hide-scrollbar">
                <template x-for="order in activeOrders" :key="order.id">
                    <div class="min-w-[85vw] md:min-w-[260px] bg-slate-800 border border-slate-700 rounded-lg p-3 flex flex-col justify-between snap-center shadow-inner">
                        <div>
                            <div class="flex justify-between items-start mb-2 border-b border-slate-700 pb-2">
                                <span class="font-bold text-amber-400 text-sm truncate pr-2" x-text="order.customer_name"></span>
                                <span class="text-[9px] font-mono bg-slate-700 px-1.5 py-0.5 rounded text-gray-300" x-text="order.invoice_number.split('-').pop()"></span>
                            </div>
                            <ul class="text-[11px] md:text-xs space-y-1.5 mb-3">
                                <template x-for="item in order.order_items" :key="item.id">
                                    <li class="flex flex-col">
                                        <span class="font-semibold text-gray-200" x-text="item.quantity + 'x ' + (item.menu ? item.menu.name : 'Item')"></span>
                                        <span class="text-[9px] md:text-[10px] text-gray-400" x-text="item.ice_level + ' | ' + item.sugar_level"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <button @click="markAsCompleted(order.id)" class="w-full bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold py-2 rounded text-[11px] md:text-xs uppercase tracking-wider transition shadow-sm">
                            SELESAI
                        </button>
                    </div>
                </template>
                <template x-if="activeOrders.length === 0">
                    <div class="text-[10px] md:text-xs text-slate-400 italic py-4 w-full text-center border border-dashed border-slate-700 rounded-lg">
                        Dapur kosong, siap nerima order.
                    </div>
                </template>
            </div>
        </div>

        <!-- AREA KATALOG MENU -->
        <div class="bg-white md:rounded-lg md:shadow-sm md:border md:border-gray-200 p-2 md:p-6 flex flex-col flex-1">
            <div class="flex justify-between items-center mb-3 px-1 md:px-0">
                <h2 class="text-base md:text-xl font-black text-gray-800">Menu Kasir</h2>
                <div class="flex space-x-2">
                    <input type="text" x-model="search" placeholder="Cari..." class="border border-gray-300 px-2.5 py-1.5 rounded text-xs w-28 md:w-48 focus:outline-none focus:ring-1 focus:ring-amber-500 bg-gray-50">
                    <button @click="showHistory = true" class="bg-slate-900 text-white px-2.5 py-1.5 rounded text-xs font-bold shadow-sm active:bg-slate-800">
                        Riwayat
                    </button>
                </div>
            </div>

            <!-- Grid Menu Compact buat HP -->
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2 md:gap-4 pb-2">
                <template x-for="menu in filteredMenus" :key="menu.id">
                    <div @click="addToCart(menu)" class="border border-gray-200 rounded-lg p-2 hover:border-amber-500 active:scale-95 cursor-pointer transition flex flex-col justify-between bg-white shadow-sm">
                        <div>
                            <div class="aspect-square md:aspect-video w-full rounded bg-gray-100 overflow-hidden mb-1.5 relative">
                                <img :src="menu.image || 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=400&auto=format&fit=crop'" :alt="menu.name" class="w-full h-full object-cover">
                                <span class="absolute top-1 right-1 bg-black/80 text-amber-400 text-[8px] md:text-[10px] px-1 py-0.5 rounded uppercase font-bold" x-text="menu.category"></span>
                            </div>
                            <h4 class="font-bold text-[11px] md:text-sm text-gray-800 leading-tight line-clamp-2" x-text="menu.name"></h4>
                        </div>
                        <div class="mt-2 flex justify-between items-center border-t border-gray-100 pt-1.5">
                            <span class="text-[10px] md:text-xs font-black text-amber-700" x-text="formatRupiah(menu.selling_price)"></span>
                            <span class="bg-slate-900 text-white text-[8px] md:text-[10px] font-bold px-1.5 py-0.5 rounded shadow">+</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- STICKY BOTTOM BAR (Mobile Only) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.15)] z-[60] flex justify-between items-center pb-safe">
        <div class="flex flex-col">
            <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider">Total Pembayaran</span>
            <span class="text-lg font-black text-amber-600 leading-none" x-text="formatRupiah(calculateTotal())"></span>
        </div>
        <button @click="showCartMobile = true" class="bg-slate-900 active:bg-slate-800 text-white font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2">
            <span class="text-[11px] uppercase tracking-wide">Checkout</span>
            <span class="bg-amber-500 text-slate-900 text-[10px] px-2 py-0.5 rounded-md font-black" x-text="cart.length"></span>
        </button>
    </div>

    <!-- KANAN: KERANJANG (Drawer Fullscreen di Mobile, Sidebar di Desktop) -->
    <div :class="showCartMobile ? 'fixed inset-0 z-[100] flex flex-col bg-white h-full' : 'hidden md:flex w-[350px] bg-white rounded-lg shadow-sm border border-gray-200 flex-col justify-between overflow-hidden shrink-0'">
        
        <!-- Header Keranjang Mobile -->
        <div class="p-4 md:p-6 flex flex-col h-full bg-gray-50 md:bg-white">
            <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200">
                <h3 class="text-base md:text-lg font-black text-gray-800">Keranjang Kasir</h3>
                <div class="flex items-center gap-3">
                    <button @click="clearCart()" class="text-[10px] md:text-[11px] text-red-500 font-bold uppercase tracking-wider bg-red-50 px-2 py-1 rounded" x-show="cart.length > 0">Reset</button>
                    <!-- Tombol Close Besar untuk Mobile -->
                    <button @click="showCartMobile = false" class="md:hidden bg-gray-200 text-gray-600 font-bold w-8 h-8 rounded-full flex items-center justify-center active:bg-gray-300">&times;</button>
                </div>
            </div>

            <!-- Form Detail Pesanan -->
            <div class="space-y-2.5 mb-3">
                <input type="text" x-model="customerName" placeholder="Nama Pelanggan / Meja" class="w-full border border-gray-300 p-2.5 rounded-md text-xs font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white shadow-sm">
                
                <div class="grid grid-cols-2 gap-2">
                    <select x-model="orderSource" class="w-full border border-gray-300 p-2 rounded-md text-[11px] font-bold text-slate-700 bg-white shadow-sm">
                        <option value="offline_pos">POS Kasir</option>
                        <option value="merchant_shopee">ShopeeFood</option>
                        <option value="merchant_gofood">GoFood</option>
                        <option value="merchant_grab">GrabFood</option>
                    </select>
                    <select x-model="paymentMethod" class="w-full border border-gray-300 p-2 rounded-md text-[11px] font-bold text-slate-700 bg-white shadow-sm">
                        <option value="Cash">Tunai / Cash</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Debit">Debit/CC</option>
                    </select>
                </div>
            </div>

            <!-- List Item Cart -->
            <div class="flex-1 overflow-y-auto space-y-2.5 pr-1 border-t border-gray-200 pt-3">
                <template x-if="cart.length === 0">
                    <div class="text-center py-12 text-gray-400 flex flex-col items-center">
                        <span class="text-4xl mb-2 opacity-50">🛒</span>
                        <p class="text-[11px] font-bold">Keranjang masih kosong</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex flex-col bg-white p-3 rounded-lg border border-gray-200 shadow-sm space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="font-bold text-xs md:text-sm text-gray-800 pr-2" x-text="item.name"></span>
                            <span class="text-[11px] md:text-xs font-black text-amber-600" x-text="formatRupiah(item.selling_price * item.quantity)"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                            <select x-model="item.ice_level" class="border border-gray-200 p-1.5 rounded bg-gray-50 font-semibold text-gray-600">
                                <option value="Normal Ice">Normal Ice</option>
                                <option value="Less Ice">Less Ice</option>
                                <option value="No Ice">No Ice</option>
                            </select>
                            <select x-model="item.sugar_level" class="border border-gray-200 p-1.5 rounded bg-gray-50 font-semibold text-gray-600">
                                <option value="Normal Sugar">Normal Sugar</option>
                                <option value="Less Sugar">Less Sugar</option>
                                <option value="Extra Sugar">Extra Sugar</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <button @click="removeFromCart(index)" class="text-[10px] text-red-500 font-bold uppercase tracking-wider active:bg-red-50 px-1 rounded">Hapus</button>
                            <div class="flex items-center space-x-3 bg-gray-100 border border-gray-200 rounded-md p-1">
                                <button @click="updateQty(index, -1)" class="w-6 h-6 bg-white text-gray-800 rounded flex items-center justify-center font-bold text-xs shadow-sm active:bg-gray-200">-</button>
                                <span class="text-xs font-black w-4 text-center" x-text="item.quantity"></span>
                                <button @click="updateQty(index, 1)" class="w-6 h-6 bg-amber-400 text-slate-900 rounded flex items-center justify-center font-bold text-xs shadow-sm active:bg-amber-500">+</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Total & Checkout Action -->
            <div class="pt-4 border-t border-gray-200 mt-2 bg-gray-50 md:bg-white pb-6 md:pb-0">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Bayar</span>
                    <span class="text-2xl font-black text-amber-600" x-text="formatRupiah(calculateTotal())"></span>
                </div>

                <button @click="processPayment()" :disabled="cart.length === 0 || loading" class="w-full bg-slate-900 hover:bg-slate-800 disabled:bg-gray-300 text-white font-black py-4 rounded-lg text-sm uppercase tracking-widest shadow-lg active:scale-[0.98] transition flex justify-center items-center space-x-2">
                    <span x-show="!loading" x-text="isOnline ? 'PROSES PEMBAYARAN' : 'SIMPAN OFFLINE (LOKAL)'"></span>
                    <span x-show="loading" class="animate-pulse">MEMPROSES...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL RIWAYAT TRANSAKSI -->
    <div x-show="showHistory" style="display: none;" class="fixed inset-0 z-[110] flex justify-end">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showHistory = false"></div>
        
        <div class="w-full md:w-[400px] bg-white h-full relative z-10 shadow-2xl flex flex-col transform transition-transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-slate-900 text-white">
                <div>
                    <h3 class="text-base font-bold">Riwayat Transaksi</h3>
                    <p class="text-[10px] text-gray-300">20 Transaksi Terakhir</p>
                </div>
                <button @click="showHistory = false" class="text-gray-300 hover:text-white font-bold text-2xl">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-2.5 bg-gray-50">
                @forelse($recentPosOrders as $ro)
                    <div class="border border-gray-200 rounded-lg p-3 shadow-sm bg-white">
                        <div class="flex justify-between items-start mb-1.5">
                            <div>
                                <span class="text-[9px] text-gray-400 font-bold">{{ $ro->created_at->format('d M, H:i') }}</span>
                                <h4 class="font-bold text-[11px] text-gray-800">{{ $ro->invoice_number }}</h4>
                            </div>
                            @if(strtolower($ro->status) == 'processing')
                                <span class="bg-blue-100 text-blue-800 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase">DAPUR</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-800 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase">SELESAI</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-end mt-2 pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-[11px] font-bold text-gray-700">{{ $ro->customer_name }}</p>
                                <p class="text-[8px] font-bold text-gray-500 uppercase">{{ $ro->payment_method }}</p>
                            </div>
                            <div class="text-right flex flex-col items-end">
                                <span class="font-black text-xs text-amber-600 mb-1">Rp{{ number_format($ro->total_amount, 0, ',', '.') }}</span>
                                <a href="{{ route('orders.show', $ro->id) }}" class="text-[9px] font-bold bg-slate-200 text-slate-700 px-2 py-1 rounded active:bg-slate-300">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-[11px] font-bold">Belum ada transaksi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL CLOSING KASIR / SHIFT RECONCILIATION -->
    <!-- ============================================================== -->
    <div x-show="showClosingModal" style="display: none;" class="fixed inset-0 z-[120] flex items-center justify-center p-3 md:p-6">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="if(!closingLoading) showClosingModal = false"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="bg-slate-900 text-white p-4 md:p-5 flex justify-between items-center border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-lg">
                        💼
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-wide">Closing Shift Kasir</h3>
                        <p class="text-[11px] text-gray-400">Rekonsiliasi Uang Fisik vs Penjualan Sistem</p>
                    </div>
                </div>
                <button @click="showClosingModal = false" class="text-gray-400 hover:text-white font-bold text-2xl leading-none">&times;</button>
            </div>

            <!-- Modal Body (Form / Preview) -->
            <div class="p-4 md:p-6 overflow-y-auto space-y-4 flex-1">
                
                <!-- View Mode: FORM CLOSING -->
                <template x-if="!closingSuccessData">
                    <div class="space-y-4">
                        <!-- Shift Meta Pill -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center text-xs">
                            <div>
                                <span class="text-gray-500 font-semibold text-[10px] block">KASIR BERTUGAS</span>
                                <span class="font-bold text-slate-800" x-text="shiftSummary.cashier_name || 'Kasir NOTTE'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-gray-500 font-semibold text-[10px] block">SHIFT DIMULAI</span>
                                <span class="font-mono text-[11px] font-bold text-slate-700" x-text="shiftSummary.shift_start_time || '-'"></span>
                            </div>
                        </div>

                        <!-- System Sales Summary Card -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-3">
                                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Penjualan Kas (Sistem)</span>
                                <span class="text-base font-black text-amber-900 font-mono" x-text="formatRupiah(shiftSummary.system_cash_sales || 0)"></span>
                            </div>
                            <div class="bg-blue-50/70 border border-blue-200 rounded-xl p-3">
                                <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">Penjualan QRIS (Sistem)</span>
                                <span class="text-base font-black text-blue-900 font-mono" x-text="formatRupiah(shiftSummary.system_qris_sales || 0)"></span>
                            </div>
                        </div>

                        <!-- Input Saldo Kas Awal & Uang Fisik -->
                        <div class="space-y-3 pt-2">
                            <div>
                                <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                    1. Saldo Kas Awal / Modal Laci (Rp)
                                </label>
                                <input type="number" 
                                       x-model.number="closingForm.opening_cash" 
                                       placeholder="Contoh: 100000" 
                                       class="w-full border border-gray-300 px-3 py-2.5 rounded-lg text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                    2. Total Uang Kas Fisik di Laci (Rp)
                                </label>
                                <input type="number" 
                                       x-model.number="closingForm.physical_cash_count" 
                                       placeholder="Hitung seluruh uang lembaran & koin di laci..." 
                                       class="w-full border-2 border-amber-400 px-3 py-2.5 rounded-lg text-base font-black text-slate-900 focus:ring-2 focus:ring-amber-500 bg-amber-50/30">
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                    3. Catatan / Alasan Selisih (Opsional)
                                </label>
                                <textarea x-model="closingForm.notes" 
                                          rows="2" 
                                          placeholder="Tuliskan jika ada kendala uang kembalian / tips / pembulatan..." 
                                          class="w-full border border-gray-300 p-2.5 rounded-lg text-xs font-medium focus:ring-2 focus:ring-amber-500 bg-white"></textarea>
                            </div>
                        </div>

                        <!-- LIVE RECONCILIATION RESULT BOX -->
                        <div class="rounded-xl border p-4 transition"
                             :class="{
                                 'bg-emerald-50 border-emerald-300 text-emerald-900': calculatedDiff === 0,
                                 'bg-rose-50 border-rose-300 text-rose-900': calculatedDiff < 0,
                                 'bg-sky-50 border-sky-300 text-sky-900': calculatedDiff > 0
                             }">
                            <div class="flex justify-between items-center text-xs font-semibold mb-1.5">
                                <span>Kas Seharusnya (Modal Awal + Penjualan Kas):</span>
                                <span class="font-mono font-bold" x-text="formatRupiah(expectedCashInDrawer)"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-2 pb-2 border-b border-gray-200/50">
                                <span>Total Uang Fisik Terhitung:</span>
                                <span class="font-mono font-bold" x-text="formatRupiah(closingForm.physical_cash_count || 0)"></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider block">Status Rekonsiliasi:</span>
                                    <span class="text-sm font-black uppercase tracking-wide"
                                          x-text="calculatedDiff === 0 ? '✓ MATCH / LENGKAP' : (calculatedDiff < 0 ? '⚠ TEKOR (SHORTAGE)' : '★ SURPLUS (LEBIH)')"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black uppercase tracking-wider block">Selisih Kas:</span>
                                    <span class="text-base font-black font-mono"
                                          x-text="(calculatedDiff > 0 ? '+' : '') + formatRupiah(calculatedDiff)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- View Mode: CLOSING SUCCESS RECEIPT -->
                <template x-if="closingSuccessData">
                    <div class="space-y-4 text-center py-2" id="closing-receipt-area">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-1">
                            ✓
                        </div>
                        <h4 class="text-lg font-black text-slate-800">Closing Shift Selesai!</h4>
                        <p class="text-xs text-gray-500">Data telah tercatat permanen di Database & Jurnal Arus Kas.</p>

                        <!-- Struk Ringkasan Closing -->
                        <div class="bg-gray-50 border border-gray-300 rounded-xl p-4 text-left font-mono text-xs space-y-2 max-w-sm mx-auto shadow-inner">
                            <div class="text-center pb-2 border-b border-dashed border-gray-300">
                                <h5 class="font-black text-sm text-slate-900">NOTTE COFFEE</h5>
                                <p class="text-[10px] text-gray-500">STRUK PENUTUPAN SHIFT KASIR</p>
                                <p class="text-[9px] text-gray-400 mt-1" x-text="'Ref ID: #' + closingSuccessData.closing_id + ' | ' + closingSuccessData.closing_time"></p>
                            </div>

                            <div class="space-y-1 pt-1 text-[11px]">
                                <div class="flex justify-between">
                                    <span>Kasir:</span>
                                    <span class="font-bold" x-text="closingSuccessData.cashier_name"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Modal Awal:</span>
                                    <span x-text="formatRupiah(closingSuccessData.opening_cash)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Penjualan Tunai:</span>
                                    <span x-text="formatRupiah(closingSuccessData.system_cash_sales)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Penjualan QRIS:</span>
                                    <span x-text="formatRupiah(closingSuccessData.system_qris_sales)"></span>
                                </div>
                                <div class="flex justify-between pt-1 border-t border-dashed border-gray-300">
                                    <span class="font-bold">Kas Fisik Laci:</span>
                                    <span class="font-bold" x-text="formatRupiah(closingSuccessData.physical_cash_count)"></span>
                                </div>
                                <div class="flex justify-between font-black"
                                     :class="closingSuccessData.cash_difference < 0 ? 'text-rose-600' : (closingSuccessData.cash_difference > 0 ? 'text-blue-600' : 'text-emerald-600')">
                                    <span>Selisih Kas:</span>
                                    <span x-text="(closingSuccessData.cash_difference > 0 ? '+' : '') + formatRupiah(closingSuccessData.cash_difference)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2.5">
                <template x-if="!closingSuccessData">
                    <div class="flex gap-2 w-full justify-end">
                        <button type="button" 
                                @click="showClosingModal = false" 
                                class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="button" 
                                @click="submitClosingShift()" 
                                :disabled="closingLoading"
                                class="px-5 py-2 text-xs font-extrabold text-slate-900 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 rounded-lg shadow transition flex items-center gap-1.5">
                            <span x-show="!closingLoading">SIMPAN & CLOSING</span>
                            <span x-show="closingLoading" class="animate-pulse">MENYIMPAN...</span>
                        </button>
                    </div>
                </template>

                <template x-if="closingSuccessData">
                    <div class="flex gap-2 w-full justify-end">
                        <button type="button" 
                                @click="window.print()" 
                                class="px-4 py-2 text-xs font-bold text-slate-800 bg-slate-200 hover:bg-slate-300 rounded-lg transition flex items-center gap-1">
                            🖨️ Cetak Struk
                        </button>
                        <button type="button" 
                                @click="showClosingModal = false; closingSuccessData = null;" 
                                class="px-5 py-2 text-xs font-black text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition">
                            Selesai & Tutup
                        </button>
                    </div>
                </template>
            </div>

        </div>
    </div>

</div>

<style>
    .pb-safe { padding-bottom: env(safe-area-inset-bottom, 1rem); }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
    function posSystem() {
        return {
            showHistory: false,
            showCartMobile: false,
            menus: @json($menus),
            search: '',
            customerName: '',
            orderSource: 'offline_pos',
            paymentMethod: 'Cash',
            cart: [],
            loading: false,
            activeOrders: [],
            pollInterval: null,

            // Offline Sync Engine State
            isOnline: navigator.onLine,
            isSyncing: false,
            pendingOfflineCount: 0,

            // Shift Closing State
            showClosingModal: false,
            closingLoading: false,
            closingSuccessData: null,
            shiftSummary: {
                shift_start_time: '-',
                system_cash_sales: 0,
                system_qris_sales: 0,
                cashier_name: ''
            },
            closingForm: {
                opening_cash: 0,
                physical_cash_count: 0,
                notes: ''
            },

            async init() {
                // 1. Cache Menu katalog ke IndexedDB
                if (window.posOfflineEngine) {
                    if (this.menus && this.menus.length > 0) {
                        await window.posOfflineEngine.cacheMenus(this.menus);
                    } else {
                        // Jika saat buka halaman lagi offline, ambil dari IndexedDB
                        const cached = await window.posOfflineEngine.getCachedMenus();
                        if (cached && cached.length > 0) {
                            this.menus = cached;
                        }
                    }
                    await this.updatePendingCount();
                }

                // 2. Listen Network & Sync events
                window.addEventListener('pos-network-change', (e) => {
                    this.isOnline = e.detail.online;
                    if (this.isOnline) {
                        this.fetchActiveQueue();
                    }
                });

                window.addEventListener('pos-sync-complete', (e) => {
                    this.updatePendingCount();
                    this.fetchActiveQueue();
                    alert(`✅ ${e.detail.count} Transaksi offline berhasil disinkronkan ke server!`);
                });

                // 3. Queue Polling jika online
                this.fetchActiveQueue();
                this.pollInterval = setInterval(() => {
                    if (navigator.onLine) {
                        this.fetchActiveQueue();
                    }
                    this.updatePendingCount();
                }, 5000);
            },

            async updatePendingCount() {
                if (window.posOfflineEngine) {
                    this.pendingOfflineCount = await window.posOfflineEngine.getPendingOrdersCount();
                }
            },

            async triggerManualSync() {
                if (!navigator.onLine) {
                    alert('Tidak dapat sinkronisasi: Perangkat sedang OFFLINE. Pastikan terhubung internet.');
                    return;
                }
                this.isSyncing = true;
                try {
                    const res = await window.posOfflineEngine.syncOfflineOrders(
                        "{{ csrf_token() }}", 
                        "{{ route('pos.sync_offline') }}"
                    );
                    await this.updatePendingCount();
                    if (res && res.synced > 0) {
                        alert(`Berhasil sinkronisasi ${res.synced} transaksi offline ke server!`);
                    } else if (res && res.error) {
                        alert(`Sinkronisasi gagal: ${res.error}`);
                    }
                } catch (e) {
                    console.error('Manual sync error', e);
                    alert('Gagal melakukan sinkronisasi: ' + e.message);
                } finally {
                    this.isSyncing = false;
                }
            },

            async fetchActiveQueue() {
                if (!navigator.onLine) return;
                try {
                    const response = await fetch("{{ route('pos.active_queue') }}");
                    const result = await response.json();
                    if (result.success) {
                        this.activeOrders = result.data;
                    }
                } catch (e) {
                    console.error("Gagal menarik data KDS", e);
                }
            },

            async markAsCompleted(orderId) {
                try {
                    const response = await fetch(`/admin/pos/mark-completed/${orderId}`, {
                        method: "POST", 
                        headers: { 
                            "Content-Type": "application/json", 
                            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.fetchActiveQueue();
                    } else {
                        alert(result.message);
                    }
                } catch (e) {
                    alert('Gagal mengeksekusi status pesanan.');
                }
            },

            get filteredMenus() {
                if (!this.search) return this.menus;
                return this.menus.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()));
            },

            addToCart(menu) {
                let existingIndex = this.cart.findIndex(i => i.id === menu.id);
                if (existingIndex > -1) {
                    this.cart[existingIndex].quantity++;
                } else {
                    this.cart.push({ 
                        id: menu.id, 
                        name: menu.name, 
                        selling_price: parseFloat(menu.selling_price), 
                        quantity: 1, 
                        ice_level: 'Normal Ice', 
                        sugar_level: 'Normal Sugar' 
                    });
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
            
            // ==========================================
            // CHECKOUT LOGIC DENGAN OFFLINE INTERCEPTION
            // ==========================================
            async processPayment() {
                if (!this.customerName.trim()) { 
                    alert('Nama pelanggan harus diisi!'); 
                    return; 
                }
                this.loading = true;

                const payload = {
                    customer_name: this.customerName,
                    order_source: this.orderSource,
                    payment_method: this.paymentMethod,
                    total_amount: this.calculateTotal(),
                    items: this.cart.map(item => ({ 
                        menu_id: item.id, 
                        quantity: item.quantity, 
                        ice_level: item.ice_level, 
                        sugar_level: item.sugar_level 
                    }))
                };
                
                // SKENARIO 1: Perangkat sedang OFFLINE
                if (!navigator.onLine) {
                    try {
                        const offlineOrder = await window.posOfflineEngine.saveOfflineOrder(payload);
                        await this.updatePendingCount();
                        this.clearCart();
                        this.customerName = '';
                        this.showCartMobile = false;
                        alert(`⚡ Mode Offline Aktif:\nTransaksi berhasil disimpan di memori lokal!\nNo Ref: ${offlineOrder.client_id}\n\nTransaksi akan otomatis disinkronkan saat online.`);
                    } catch (e) {
                        alert('Gagal menyimpan pesanan offline: ' + e.message);
                    } finally {
                        this.loading = false;
                    }
                    return;
                }

                // SKENARIO 2: Perangkat ONLINE (dengan fallback offline jika koneksi gagal)
                try {
                    const response = await fetch("{{ route('pos.store') }}", {
                        method: "POST", 
                        headers: { 
                            "Content-Type": "application/json", 
                            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                        }, 
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.clearCart();
                        this.customerName = '';
                        this.showCartMobile = false; 
                        this.fetchActiveQueue(); 
                    } else { 
                        alert('Gagal: ' + result.message); 
                    }
                } catch (error) { 
                    console.warn("Fetch gagal (jaringan tidak stabil), menyimpan transaksi ke IndexedDB...", error);
                    try {
                        const offlineOrder = await window.posOfflineEngine.saveOfflineOrder(payload);
                        await this.updatePendingCount();
                        this.clearCart();
                        this.customerName = '';
                        this.showCartMobile = false;
                        alert(`⚠️ Koneksi Server Terputus:\nTransaksi disimpan otomatis di penyimpanan lokal (IndexedDB).\nNo Ref: ${offlineOrder.client_id}`);
                    } catch (saveErr) {
                        alert('Gagal memproses transaksi & gagal menyimpan lokal: ' + saveErr.message);
                    }
                } finally { 
                    this.loading = false; 
                }
            },

            // ==========================================
            // SHIFT CLOSING METHODS
            // ==========================================
            async openClosingModal() {
                this.closingSuccessData = null;
                this.closingLoading = false;
                this.showClosingModal = true;

                try {
                    const res = await fetch("{{ route('pos.shift_summary') }}");
                    const data = await res.json();
                    if (data.success) {
                        this.shiftSummary = data.data;
                    }
                } catch (e) {
                    console.error("Gagal menarik data shift summary", e);
                }
            },

            get expectedCashInDrawer() {
                const opening = parseFloat(this.closingForm.opening_cash) || 0;
                const systemCash = parseFloat(this.shiftSummary.system_cash_sales) || 0;
                return opening + systemCash;
            },

            get calculatedDiff() {
                const physical = parseFloat(this.closingForm.physical_cash_count) || 0;
                return physical - this.expectedCashInDrawer;
            },

            async submitClosingShift() {
                if (this.closingForm.physical_cash_count === null || this.closingForm.physical_cash_count === undefined || this.closingForm.physical_cash_count === '') {
                    alert('Harap isi total uang fisik di laci kasir.');
                    return;
                }

                if (!confirm(`Konfirmasi Closing Shift?\nKas Seharusnya: ${this.formatRupiah(this.expectedCashInDrawer)}\nKas Fisik: ${this.formatRupiah(this.closingForm.physical_cash_count)}\nSelisih: ${(this.calculatedDiff > 0 ? '+' : '') + this.formatRupiah(this.calculatedDiff)}`)) {
                    return;
                }

                this.closingLoading = true;
                try {
                    const response = await fetch("{{ route('pos.close_shift') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(this.closingForm)
                    });

                    const result = await response.json();
                    if (result.success) {
                        this.closingSuccessData = result.data;
                    } else {
                        alert('Gagal closing shift: ' + result.message);
                    }
                } catch (e) {
                    alert('Koneksi terputus saat submit closing.');
                } finally {
                    this.closingLoading = false;
                }
            }
        }
    }
</script>
@endsection