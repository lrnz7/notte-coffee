@extends('layouts.admin')

@section('content')
<!-- HEADER & FORM UPDATE STATUS (Mobile-Optimized Responsive Flex) -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white md:bg-transparent p-4 md:p-0 rounded-lg border md:border-none border-gray-200 shadow-sm md:shadow-none">
    <div>
        @if(str_contains($order->invoice_number, 'NOTTE-POS-'))
            <a href="{{ route('pos.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 mb-1">
                ← Kembali ke POS Kasir
            </a>
        @else
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 block mb-1">
                ← Kembali ke Pesanan Online
            </a>
        @endif
        <h2 class="text-lg md:text-2xl font-black text-gray-800 break-all leading-tight">
            Detail: {{ $order->invoice_number }}
        </h2>
    </div>

    <!-- Form Update Status -->
    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
        @csrf
        @method('PATCH')
        <select name="status" class="p-2.5 text-xs md:text-sm border border-gray-300 rounded-md font-bold bg-gray-50 md:bg-white text-slate-800 focus:ring-amber-500 focus:border-amber-500">
            <option value="pending_payment" {{ $order->status == 'pending_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
            <option value="waiting_verification" {{ $order->status == 'waiting_verification' ? 'selected' : '' }}>Verifikasi Pembayaran</option>
            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Proses Dapur (Potong Stok)</option>
            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan</option>
        </select>
        <button type="submit" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs md:text-sm font-bold px-4 py-2.5 rounded-md uppercase tracking-wide transition shadow-sm">
            Update Status
        </button>
    </form>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm font-semibold shadow-sm">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-6 text-sm font-semibold shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Item Pesanan & Bukti Bayar -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 text-sm md:text-base">Item Yang Dipesan</h3>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="flex justify-between items-start md:items-center border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="font-bold text-gray-900 text-xs md:text-sm">{{ $item->menu->name ?? 'Menu Dihapus' }}</h4>
                        <p class="text-[11px] font-semibold text-amber-700">
                            Catatan/Option: {{ $item->note ?? 'Normal' }}
                        </p>
                        <p class="text-[10px] md:text-xs text-gray-500">Rp{{ number_format($item->price_at_purchase ?? $item->unit_price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                    </div>
                    <p class="font-extrabold text-gray-800 text-xs md:text-sm">
                        Rp{{ number_format(($item->price_at_purchase ?? $item->unit_price) * $item->quantity, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center text-base md:text-lg font-black">
                <span>Total Tagihan:</span>
                <span class="text-amber-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- BUKTI PEMBAYARAN USER -->
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex justify-between items-center text-sm md:text-base">
                <span>Bukti Transfer QRIS / Pembayaran</span>
                @if($order->status == 'waiting_verification')
                    <span class="text-[10px] bg-purple-100 text-purple-700 font-bold px-2 py-0.5 rounded-full uppercase">Perlu Verifikasi</span>
                @endif
            </h3>

            @if($order->payment_proof)
                <div class="space-y-4">
                    <div class="max-w-md mx-auto overflow-hidden rounded-xl border border-gray-200 shadow-md">
                        <a href="{{ route('orders.paymentProof', $order->id) }}" target="_blank">
                            <img src="{{ route('orders.paymentProof', $order->id) }}" alt="Bukti Bayar Pembeli" class="w-full h-auto object-cover hover:opacity-95 transition">
                        </a>
                    </div>
                    <p class="text-[10px] text-center text-gray-500 italic">Klik gambar untuk melihat resolusi penuh.</p>

                    @if($order->status == 'waiting_verification' || $order->status == 'pending_payment')
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs py-3 rounded-lg uppercase tracking-wider shadow-sm transition">
                                ✓ Terima & Kirim ke Dapur
                            </button>
                        </form>
                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs py-3 rounded-lg uppercase tracking-wider shadow-sm transition">
                                ✕ Tolak & Batalkan
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            @else
                <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <p class="text-xs font-semibold">Pembeli belum mengunggah bukti pembayaran.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Pembeli & Pengiriman -->
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200 space-y-4 h-fit">
        <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm md:text-base">Informasi Transaksi</h3>
        <div>
            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Nama Pelanggan</span>
            <p class="font-bold text-gray-800 text-xs md:text-sm">{{ $order->customer_name }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Nomor HP / WhatsApp</span>
            <p class="font-bold text-gray-800 text-xs md:text-sm">{{ $order->customer_phone ?? '-' }}</p>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider mb-1">Tipe Pesanan</span>
            <span class="uppercase font-extrabold text-[10px] bg-slate-100 text-slate-800 px-2 py-0.5 rounded inline-block">
                {{ str_contains($order->invoice_number, 'NOTTE-POS-') ? 'POS Offline' : ($order->order_type ?? 'Online') }}
            </span>
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Alamat Pengiriman</span>
            <p class="text-xs text-gray-700 mt-0.5 leading-relaxed">{{ $order->shipping_address ?? 'Pickup / Transaksi Kasir Toko' }}</p>
        </div>
        <hr class="border-gray-100">
        <div>
            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Metode Pembayaran</span>
            <p class="font-bold uppercase text-amber-700 text-xs md:text-sm">{{ $order->payment_method }}</p>
        </div>
    </div>
</div>
@endsection