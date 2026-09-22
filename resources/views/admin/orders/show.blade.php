@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <!-- Tombol Kembali Dinamis Sesuai Asal Transaksi -->
        @if(str_contains($order->invoice_number, 'NOTTE-POS-'))
            <a href="{{ route('pos.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 mb-1">
                ← Kembali ke POS Kasir
            </a>
        @else
            <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 block mb-1">
                ← Kembali ke Pesanan Online
            </a>
        @endif
        <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan: {{ $order->invoice_number }}</h2>
    </div>

    <!-- Form Update Status -->
    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex items-center space-x-2">
        @csrf
        @method('PATCH')
        <select name="status" class="p-2 text-sm border rounded-md font-semibold">
            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Proses (Potong Stok)</option>
            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan</option>
        </select>
        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold px-4 py-2 rounded-md">
            Update Status
        </button>
    </form>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Item Pesanan -->
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Item Yang Dipesan</h3>
        <div class="space-y-4">
            @foreach($order->orderItems as $item)
            <div class="flex justify-between items-center border-b pb-3">
                <div>
                    <h4 class="font-bold text-gray-900">{{ $item->menu->name ?? 'Menu Dihapus' }}</h4>
                    <p class="text-xs font-semibold text-amber-700">
                        Ice: {{ $item->ice_level ?? 'Normal' }} | Sugar: {{ $item->sugar_level ?? 'Normal' }}
                    </p>
                    <p class="text-xs text-gray-500">Rp{{ number_format($item->price_at_purchase ?? $item->unit_price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                </div>
                <p class="font-extrabold text-gray-800">
                    Rp{{ number_format(($item->price_at_purchase ?? $item->unit_price) * $item->quantity, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>

        <div class="mt-6 pt-4 border-t flex justify-between items-center text-lg font-bold">
            <span>Total Tagihan:</span>
            <span class="text-amber-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Info Pembeli & Pengiriman -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4 h-fit">
        <h3 class="font-bold text-gray-800 pb-2 border-b">Informasi Transaksi</h3>
        <div>
            <span class="text-xs text-gray-400 block">Nama Pelanggan:</span>
            <p class="font-semibold text-gray-800">{{ $order->customer_name }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-400 block">Nomor HP / WhatsApp:</span>
            <p class="font-semibold text-gray-800">{{ $order->customer_phone ?? '-' }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-400 block">Tipe Pesanan:</span>
            <span class="uppercase font-bold text-xs bg-slate-100 text-slate-800 px-2 py-0.5 rounded">
                {{ str_contains($order->invoice_number, 'NOTTE-POS-') ? 'POS Offline' : ($order->order_type ?? 'Online') }}
            </span>
        </div>
        <div>
            <span class="text-xs text-gray-400 block">Alamat Pengiriman:</span>
            <p class="text-sm text-gray-700">{{ $order->shipping_address ?? 'Pickup / Transaksi Kasir Toko' }}</p>
        </div>
        <hr>
        <div>
            <span class="text-xs text-gray-400 block">Metode Pembayaran:</span>
            <p class="font-semibold uppercase text-gray-800">{{ $order->payment_method }}</p>
        </div>
    </div>
</div>
@endsection