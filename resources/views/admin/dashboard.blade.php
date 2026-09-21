@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard & Ringkasan Laporan</h2>
    <p class="text-gray-600 text-sm">Pantau performa penjualan, laba rugi, dan stok bahan baku NOTTE Coffee secara real-time.</p>
</div>

<!-- Low Stock Alert Banner -->
@if($lowStockMaterials->count() > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg mb-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-amber-800">⚠️ Peringatan Stok Bahan Baku Menipis!</h3>
                <p class="text-xs text-amber-700 mt-0.5">
                    Terdapat {{ $lowStockMaterials->count() }} bahan baku yang sudah menyentuh/dibawah batas minimal stok.
                </p>
            </div>
            <a href="{{ route('materials.index') }}" class="text-xs bg-amber-600 hover:bg-amber-700 text-white font-bold px-3 py-1.5 rounded transition">
                Cek Stok Bahan
            </a>
        </div>
    </div>
@endif

<!-- Metric Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Omzet (Penjualan)</p>
        <h3 class="text-2xl font-extrabold text-gray-900 mt-2">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        <p class="text-xs text-emerald-600 mt-2 font-medium">Dari transaksi selesai</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total HPP (Modal Bahan)</p>
        <h3 class="text-2xl font-extrabold text-gray-900 mt-2">Rp{{ number_format($totalCogs, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Berdasarkan resep terpakai</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Laba Bersih (Gross Profit)</p>
        <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">Rp{{ number_format($netProfit, 0, ',', '.') }}</h3>
        <p class="text-xs text-emerald-600 mt-2 font-medium">Omzet - Total HPP</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pesanan Pending (Online)</p>
        <h3 class="text-2xl font-extrabold text-amber-500 mt-2">{{ $pendingOrdersCount }}</h3>
        <p class="text-xs text-gray-500 mt-2 font-medium">Menunggu konfirmasi admin</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tabel Transaksi Terbaru -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
            <a href="{{ route('orders.index') }}" class="text-xs text-amber-600 font-bold hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b">
                        <th class="pb-3">Invoice</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Tipe</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="py-3 font-semibold text-gray-800">{{ $order->invoice_number }}</td>
                        <td class="py-3">{{ $order->customer_name }}</td>
                        <td class="py-3 uppercase text-xs font-bold text-gray-500">{{ $order->order_type }}</td>
                        <td class="py-3 font-bold text-amber-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                                {{ $order->status == 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-400">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Bahan Baku Kritis -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Daftar Stok Kritis</h3>
        <div class="space-y-3">
            @forelse($lowStockMaterials as $material)
            <div class="flex justify-between items-center border-b pb-2">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $material->name }}</p>
                    <p class="text-xs text-gray-400">Min. Alert: {{ $material->min_stock_alert }} {{ $material->unit }}</p>
                </div>
                <span class="px-2 py-1 bg-red-100 text-red-700 font-extrabold text-xs rounded">
                    {{ $material->stock_quantity }} {{ $material->unit }}
                </span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-6">Semua stok bahan baku aman.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection