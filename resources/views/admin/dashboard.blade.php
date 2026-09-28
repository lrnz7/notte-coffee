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

<!-- DYNAMIC TIME-RANGE FILTER -->
@include('admin.partials.date_filter')

<!-- Metric Cards: Financial P&L & Omnichannel Analytics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Gross Profit -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
            <span>Gross Profit</span>
        </div>
        <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">Rp{{ number_format($grossProfit, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-500 mt-1">Omzet (Rp{{ number_format($totalRevenue, 0, ',', '.') }}) - HPP (Rp{{ number_format($totalHpp, 0, ',', '.') }})</p>
    </div>

    <!-- Operational Expenditure (Opex) -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
            <span>Pengeluaran</span>
        </div>
        <h3 class="text-2xl font-extrabold text-rose-600 mt-2">Rp{{ number_format($opex, 0, ',', '.') }}</h3>
        <p class="text-xs text-gray-500 mt-1">Total Biaya Operasional (Cash Flow Expense)</p>
    </div>

    <!-- Net Profit -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
            <span>Net Profit</span>
        </div>
        <h3 class="text-2xl font-extrabold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-rose-700' }} mt-2">
            Rp{{ number_format($netProfit, 0, ',', '.') }}
        </h3>
        <p class="text-xs text-gray-500 mt-1">Laba Kotor - Operasional</p>
    </div>

    <!-- Omnichannel Sales Split -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Penjualan</p>
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between items-center">
                <span class="font-medium text-gray-600">POS (Offline Store):</span>
                <span class="font-bold text-gray-800">Rp{{ number_format($omnichannelSplit->get('pos')->total_sales ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-medium text-gray-600">Online Web:</span>
                <span class="font-bold text-gray-800">Rp{{ number_format($omnichannelSplit->get('online')->total_sales ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-medium text-gray-600">Event TFEST:</span>
                <span class="font-bold text-gray-800">Rp{{ number_format($omnichannelSplit->get('tfest_2026')->total_sales ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- SECTION GRAFIK VISUALISASI DINAMIS -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart 1: Tren Penjualan vs Pengeluaran -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Tren Penjualan & Pengeluaran</h3>
            <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">{{ $dateFilter['label'] ?? 'Bulan Ini' }}</span>
        </div>
        <div class="h-64 relative">
            <canvas id="dashboardTrendChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Proporsi Channel Omnichannel -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Distribusi Jalur Penjualan</h3>
        </div>
        <div class="h-64 relative flex items-center justify-center">
            <canvas id="omnichannelChart"></canvas>
        </div>
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
                <button @click="tab = 'pos'" :class="tab === 'pos' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">POS</button>
                <button @click="tab = 'online'" :class="tab === 'online' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Online</button>
                <button @click="tab = 'tfest_2026'" :class="tab === 'tfest_2026' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'" class="px-3 py-1.5 rounded-md transition">Event</button>
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
                    <tr x-show="tab === 'all' || tab === '{{ $order->order_source }}'">
                        <td class="py-3 font-semibold text-gray-800">{{ $order->invoice_number }}</td>
                        <td class="py-3">{{ $order->customer_name }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 text-[10px] font-extrabold rounded uppercase 
                                {{ $order->order_source == 'pos' || $order->order_source == 'offline_pos' ? 'bg-slate-100 text-slate-700' : '' }}
                                {{ $order->order_source == 'online' || $order->order_source == 'online_web' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $order->order_source == 'tfest_2026' ? 'bg-purple-100 text-purple-700' : '' }}">
                                {{ str_replace('_', ' ', $order->order_source ?? 'pos') }}
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

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Dashboard Sales vs Expense Bar/Line Trend
        const ctxTrend = document.getElementById('dashboardTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Penjualan (Omzet)',
                        data: @json($chartData['revenues']),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran (Opex)',
                        data: @json($chartData['expenses']),
                        backgroundColor: 'rgba(244, 63, 94, 0.8)',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // 2. Omnichannel Donut Chart
        const posTotal = {{ $omnichannelSplit->get('pos')->total_sales ?? 0 }};
        const onlineTotal = {{ $omnichannelSplit->get('online')->total_sales ?? 0 }};
        const tfestTotal = {{ $omnichannelSplit->get('tfest_2026')->total_sales ?? 0 }};

        const ctxOmni = document.getElementById('omnichannelChart').getContext('2d');
        new Chart(ctxOmni, {
            type: 'doughnut',
            data: {
                labels: ['POS Store', 'Online Web', 'Event TFEST'],
                datasets: [{
                    data: [posTotal, onlineTotal, tfestTotal],
                    backgroundColor: ['#64748b', '#3b82f6', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } 
                }
            }
        });
    });
</script>
@endsection