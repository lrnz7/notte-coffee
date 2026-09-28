<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashFlow; 
use App\Models\Order;
use App\Traits\FilterableByDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    use FilterableByDate;

    public function index(Request $request)
    {
        // 0. Resolve Filter Tanggal
        $dateFilter = $this->resolveDateFilter($request);

        // 1. Ambil data cash flow murni dengan paginasi & filter tanggal
        $cashFlowsQuery = CashFlow::latest();
        $this->applyDateFilter($cashFlowsQuery, $dateFilter, 'created_at');
        $cashFlows = $cashFlowsQuery->paginate(25)->appends($request->all());

        // 2. Hitung Financial P&L & Enterprise Metrics
        $effectiveOrders = Order::whereIn('status', ['completed', 'processing']);
        $this->applyDateFilter($effectiveOrders, $dateFilter, 'created_at');
        
        $totalRevenue = (clone $effectiveOrders)->sum('total_amount');
        $totalHpp = (clone $effectiveOrders)->selectRaw('SUM(CASE WHEN total_hpp > 0 THEN total_hpp ELSE total_cogs END) as aggregate')->value('aggregate') ?? 0;
        
        // 2a. Gross Profit = Revenue - HPP
        $grossProfit = $totalRevenue - $totalHpp;

        // 2b. Opex
        $opexQuery = CashFlow::whereIn('type', ['expense', 'outflow'])
            ->where('category', '!=', 'Material Restock')
            ->where('category', '!=', 'Belanja Bahan Baku');
        $this->applyDateFilter($opexQuery, $dateFilter, 'created_at');
        $opex = $opexQuery->sum('amount');

        // 2c. Net Profit = Gross Profit - Opex
        $netProfit = $grossProfit - $opex;

        // 2d. Omnichannel Breakdown
        $omnichannelSplit = (clone $effectiveOrders)
            ->selectRaw('COALESCE(order_source, "pos") as channel, SUM(total_amount) as total_sales, COUNT(*) as order_count')
            ->groupBy('channel')
            ->get()
            ->keyBy('channel');

        // Total Inflow & Outflow murni dari cash_flows
        $inflowQuery = CashFlow::whereIn('type', ['income', 'inflow']);
        $this->applyDateFilter($inflowQuery, $dateFilter, 'created_at');
        $totalInflow = $inflowQuery->sum('amount');

        $outflowQuery = CashFlow::whereIn('type', ['expense', 'outflow']);
        $this->applyDateFilter($outflowQuery, $dateFilter, 'created_at');
        $totalOutflow = $outflowQuery->sum('amount');

        // 3. Data Chart 1: Trend Dinamis berdasarkan Filter Tanggal
        $chartData = $this->generateChartTrendData(
            $dateFilter,
            Order::whereIn('status', ['completed', 'processing']),
            CashFlow::whereIn('type', ['expense', 'outflow'])
        );
        $chartDates = $chartData['labels'];
        $chartInflows = $chartData['revenues'];
        $chartOutflows = $chartData['expenses'];

        // 4. Data Chart 2: Breakdown Pengeluaran per Kategori (dengan Filter Tanggal)
        $expenseCategoriesQuery = CashFlow::select('category', DB::raw('SUM(amount) as total'))
            ->whereIn('type', ['expense', 'outflow']);
        $this->applyDateFilter($expenseCategoriesQuery, $dateFilter, 'created_at');
        $expenseCategories = $expenseCategoriesQuery->groupBy('category')->get();

        $catLabels = $expenseCategories->pluck('category')->toArray();
        $catTotals = $expenseCategories->pluck('total')->toArray();

        if (empty($catLabels)) {
            $catLabels = ['Belum Ada Pengeluaran'];
            $catTotals = [0];
        }

        return view('admin.cash_flows.index', compact(
            'cashFlows', 'totalInflow', 'totalOutflow', 'totalRevenue', 'totalHpp', 
            'grossProfit', 'opex', 'netProfit', 'omnichannelSplit',
            'chartDates', 'chartInflows', 'chartOutflows',
            'catLabels', 'catTotals', 'dateFilter'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:Outflow,Inflow',
            'category'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        // Standarisasi jadi huruf kecil (inflow / outflow) biar seragam di DB
        CashFlow::create([
            'type'        => strtolower($request->type),
            'category'    => $request->category,
            'amount'      => $request->amount,
            'description' => $request->description,
            'date'        => now()->toDateString(), 
        ]);

        return redirect()->back()->with('success', 'Transaksi arus kas berhasil dicatat!');
    }
}