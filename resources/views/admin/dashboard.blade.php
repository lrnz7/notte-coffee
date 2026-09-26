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
        <p class="text-xs text-emerald-600 mt-2 font-medium">Dari seluruh transaksi selesai</p>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ tab: 'all' }">
    <!-- Tabel Transaksi Terbaru dengan Tab PEMISAH SOURCING -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 pb-3 border-b gap-3">
            <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
            
            <!-- TAB NAVIGASI JALUR TRANSAKSI -->
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg text-xs font-bold">
                <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Semua</button>
                <button @click="tab = 'offline_pos'" :class="tab === 'offline_pos' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Offline POS</button>
                <button @click="tab = 'online_web'" :class="tab === 'online_web' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Online Web</button>
                <button @click="tab = 'merchant'" :class="tab === 'merchant' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Merchant</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b">
                        <th class="pb-3">Invoice</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Jalur (Source)</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr x-show="tab === 'all' || 
                                (tab === 'offline_pos' && '{{ $order->order_source }}' === 'offline_pos') ||
                                (tab === 'online_web' && '{{ $order->order_source }}' === 'online_web') ||
                                (tab === 'merchant' && ('{{ $order->order_source }}'.includes('merchant') || '{{ $order->order_source }}' === 'merchant'))">
                        <td class="py-3 font-semibold text-gray-800">{{ $order->invoice_number }}</td>
                        <td class="py-3">{{ $order->customer_name }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 text-[10px] font-extrabold rounded uppercase 
                                {{ $order->order_source == 'offline_pos' ? 'bg-slate-100 text-slate-700' : '' }}
                                {{ $order->order_source == 'online_web' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ str_contains($order->order_source, 'merchant') ? 'bg-orange-100 text-orange-700' : '' }}">
                                {{ str_replace('_', ' ', $order->order_source ?? 'online_web') }}
                            </span>
                        </td>
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