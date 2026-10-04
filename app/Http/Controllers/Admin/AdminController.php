<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Material;
use App\Models\Menu;
use App\Models\CashFlow;
use App\Traits\FilterableByDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use FilterableByDate;

    public function index(Request $request)
    {
        // 0. Resolve Filter Tanggal
        $dateFilter = $this->resolveDateFilter($request);

        // 1. Base Query Pesanan yang Berjalan/Selesai
        $completedOrdersQuery = Order::whereIn('status', ['completed', 'processing']);
        $this->applyDateFilter($completedOrdersQuery, $dateFilter, 'created_at');

        // Gross Revenue / Total Penjualan Kotor
        $grossRevenue = (clone $completedOrdersQuery)->sum('total_amount');
        $totalRevenue = $grossRevenue;
        // Snapshot HPP
        $totalHpp = (clone $completedOrdersQuery)->selectRaw('SUM(CASE WHEN total_hpp > 0 THEN total_hpp ELSE total_cogs END) as aggregate')->value('aggregate') ?? 0;
        
        // 1a. Gross Profit
        $grossProfit = $grossRevenue - $totalHpp;

        // 1b. Operational Expenditure (Opex) dengan Filter Tanggal
        $opexQuery = CashFlow::whereIn('type', ['expense', 'outflow'])
            ->where('category', '!=', 'Material Restock')
            ->where('category', '!=', 'Belanja Bahan Baku');
        $this->applyDateFilter($opexQuery, $dateFilter, 'created_at');
        $opex = $opexQuery->sum('amount');

        // 1c. Net Profit = Gross Profit - Opex
        $netProfit = $grossProfit - $opex;

        // 1d. Omnichannel Split (Sesuai rentang tanggal)
        $omnichannelSplit = (clone $completedOrdersQuery)
            ->selectRaw('COALESCE(order_source, "pos") as channel, SUM(total_amount) as total_sales, COUNT(*) as order_count')
            ->groupBy('channel')
            ->get()
            ->keyBy('channel');

        // 2. Chart Visual: Trend Penjualan vs Pengeluaran
        $chartData = $this->generateChartTrendData(
            $dateFilter,
            Order::whereIn('status', ['completed', 'processing']),
            CashFlow::whereIn('type', ['expense', 'outflow'])->where('category', '!=', 'Material Restock')
        );

        // 3. Total Transaksi (Sesuai Filter)
        $totalOrdersQuery = Order::query();
        $this->applyDateFilter($totalOrdersQuery, $dateFilter, 'created_at');
        $totalOrdersCount = $totalOrdersQuery->count();
        
        $pendingOrdersCount = Order::where('status', 'pending_payment')->count();

        // 4. Low Stock Alert (Bahan baku yang stoknya <= min_stock_alert)
        $lowStockMaterials = Material::whereRaw('stock_quantity <= min_stock_alert')->get();

        // 5. Pesanan Terbaru
        $recentOrdersQuery = Order::latest()->take(30);
        $this->applyDateFilter($recentOrdersQuery, $dateFilter, 'created_at');
        $recentOrders = $recentOrdersQuery->get()->map(function ($order) {
            if (empty($order->order_source)) {
                if (str_contains($order->invoice_number, 'NOTTE-POS')) {
                    $order->order_source = 'pos';
                } else {
                    $order->order_source = 'online';
                }
            }
            return $order;
        });

        return view('admin.dashboard', compact(
            'grossRevenue',
            'totalRevenue',
            'totalHpp',
            'grossProfit',
            'opex',
            'netProfit',
            'omnichannelSplit',
            'chartData',
            'totalOrdersCount',
            'pendingOrdersCount',
            'lowStockMaterials',
            'recentOrders',
            'dateFilter'
        ));
    }
}