@extends('layouts.admin')

@section('content')
<div x-data="{ showModal: false }">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan & Arus Kas</h2>
            <p class="text-gray-600 text-sm">Monitoring pemasukan, pengeluaran, dan tren laba rugi NOTTE Coffee.</p>
        </div>
        
        <!-- Tombol Catat Transaksi Kas Manual -->
        <button @click="showModal = true" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2.5 rounded-md shadow-sm transition">
            + Catat Transaksi Kas
        </button>
    </div>

    <!-- DYNAMIC TIME-RANGE FILTER -->
    @include('admin.partials.date_filter')

    <!-- MODAL INPUT KAS MANUAL -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl relative" @click.away="showModal = false">
            <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Catat Arus Kas Manual</h3>
            
            <form action="{{ route('cash_flows.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tipe Transaksi</label>
                    <select name="type" class="w-full border border-gray-300 p-2 rounded text-xs bg-white">
                        <option value="Outflow">Pengeluaran (Outflow)</option>
                        <option value="Inflow">Pemasukan Lainnya (Inflow)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kategori</label>
                    <input type="text" name="category" placeholder="misal: Restok Bahan, Listrik, Gaji" required class="w-full border border-gray-300 p-2 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jumlah (Rp)</label>
                    <input type="number" name="amount" placeholder="50000" required class="w-full border border-gray-300 p-2 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Keterangan / Catatan</label>
                    <textarea name="description" rows="2" placeholder="Catatan tambahan..." class="w-full border border-gray-300 p-2 rounded text-xs"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-3 border-t">
                    <button type="button" @click="showModal = false" class="px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-bold bg-amber-600 text-white rounded hover:bg-amber-700">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ENTERPRISE FINANCIAL P&L & OMNICHANNEL METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1: Gross Profit -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-wider">
                <span>Gross Profit</span>
            </div>
            <h3 class="text-2xl font-black text-emerald-600 mt-2">Rp{{ number_format($grossProfit, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-gray-500 mt-1">
                Omzet (Rp{{ number_format($totalRevenue, 0, ',', '.') }}) - HPP (Rp{{ number_format($totalHpp, 0, ',', '.') }})
            </p>
        </div>

        <!-- Card 2: Operational Expenditure (Opex) -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-wider">
                <span>Pengeluaran</span>
            </div>
            <h3 class="text-2xl font-black text-rose-600 mt-2">Rp{{ number_format($opex, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-gray-500 mt-1">Biaya Operasional (Non-Bahan Baku)</p>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-wider">
                <span>Net Profit</span>
            </div>
            <h3 class="text-2xl font-black {{ $netProfit >= 0 ? 'text-blue-600' : 'text-rose-700' }} mt-2">
                Rp{{ number_format($netProfit, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-gray-500 mt-1">Laba Kotor - Operasional</p>
        </div>

        <!-- Card 4: Omnichannel Sales Breakdown -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                <span>Penjualan</span>
            </div>
            <div class="space-y-1 text-xs">
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

    <!-- SECTION GRAFIK VISUALISASI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart 1: Tren Arus Kas -->
        <div class="lg:col-span-2 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b">Tren Arus Kas (7 Hari Terakhir)</h3>
            <div class="h-64 relative">
                <canvas id="cashFlowTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Proporsi Pengeluaran -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b">Breakdown Pengeluaran</h3>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT TRANSAKSI KAS MANUAL -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Riwayat Transaksi Kas Manual</h3>
            <span class="text-xs text-gray-500">Total {{ $cashFlows->total() }} Catatan</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase">
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Tipe</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4">Keterangan</th>
                    <th class="p-4 text-right">Nominal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse($cashFlows as $cf)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium text-gray-500">{{ $cf->created_at->format('d M Y H:i') }}</td>
                    <td class="p-4">
                        @if(strtolower($cf->type) == 'inflow')
                            <span class="px-2 py-0.5 font-bold rounded bg-emerald-100 text-emerald-800 text-[10px] uppercase">Pemasukan</span>
                        @else
                            <span class="px-2 py-0.5 font-bold rounded bg-rose-100 text-rose-800 text-[10px] uppercase">Pengeluaran</span>
                        @endif
                    </td>
                    <td class="p-4 font-bold text-gray-800">{{ $cf->category }}</td>
                    <td class="p-4 text-gray-600">{{ $cf->description ?? '-' }}</td>
                    <td class="p-4 text-right font-extrabold {{ strtolower($cf->type) == 'inflow' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ strtolower($cf->type) == 'inflow' ? '+' : '-' }}Rp{{ number_format($cf->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400">Belum ada catatan pengeluaran/pemasukan manual.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($cashFlows->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $cashFlows->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Alpine JS & Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Line Chart
        const ctxTrend = document.getElementById('cashFlowTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($chartDates),
                datasets: [
                    {
                        label: 'Pemasukan (Inflow)',
                        data: @json($chartInflows),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pengeluaran (Outflow)',
                        data: @json($chartOutflows),
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        fill: true,
                        tension: 0.3
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

        // 2. Doughnut Chart
        const ctxExpense = document.getElementById('expenseCategoryChart').getContext('2d');
        new Chart(ctxExpense, {
            type: 'doughnut',
            data: {
                labels: @json($catLabels),
                datasets: [{
                    data: @json($catTotals),
                    backgroundColor: ['#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#64748b']
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