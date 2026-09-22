<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // <-- Ini yang lu lupa kocak!
use App\Models\CashFlow; // <-- Ini 'F' nya harus gede sesuai nama Model lu!
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function index()
    {
        // 1. Ambil data cash flow manual
        $cashFlows = CashFlow::latest()->get();

        // 2. Hitung Total Inflow & Outflow
        // Catatan: Model CashFlow lu pake enum 'income'/'expense'
        $manualInflow = CashFlow::where('type', 'income')->sum('amount');
        $posInflow = Order::where('invoice_number', 'like', 'NOTTE-POS-%')->sum('total_amount');
        
        $totalInflow = $manualInflow + $posInflow;
        $totalOutflow = CashFlow::where('type', 'expense')->sum('amount');
        $netProfit = $totalInflow - $totalOutflow;

        // 3. Data Chart 1: Tren Arus Kas 7 Hari Terakhir
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $chartDates = [];
        $chartInflows = [];
        $chartOutflows = [];

        foreach ($dates as $date) {
            $chartDates[] = date('d M', strtotime($date));
            
            // Inflow hari ini (POS + Manual 'income')
            $dayPos = Order::where('invoice_number', 'like', 'NOTTE-POS-%')
                           ->whereDate('created_at', $date)
                           ->sum('total_amount');
            $dayManualIn = CashFlow::where('type', 'income')
                                   ->whereDate('created_at', $date)
                                   ->sum('amount');
            $chartInflows[] = $dayPos + $dayManualIn;

            // Outflow hari ini ('expense')
            $chartOutflows[] = CashFlow::where('type', 'expense')
                                       ->whereDate('created_at', $date)
                                       ->sum('amount');
        }

        // 4. Data Chart 2: Breakdown Pengeluaran per Kategori
        $expenseCategories = CashFlow::select('category', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->groupBy('category')
            ->get();

        $catLabels = $expenseCategories->pluck('category')->toArray();
        $catTotals = $expenseCategories->pluck('total')->toArray();

        if (empty($catLabels)) {
            $catLabels = ['Belum Ada Pengeluaran'];
            $catTotals = [0];
        }

        return view('admin.cash_flows.index', compact(
            'cashFlows', 'totalInflow', 'totalOutflow', 'netProfit',
            'chartDates', 'chartInflows', 'chartOutflows',
            'catLabels', 'catTotals'
        ));
    }

    public function store(Request $request)
    {
        // Validasi nerima format form lu
        $request->validate([
            'type'        => 'required|in:Outflow,Inflow',
            'category'    => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        // Mapping dari dropdown form ('Inflow'/'Outflow') ke Enum Database ('income'/'expense')
        CashFlow::create([
            'type'        => $request->type === 'Outflow' ? 'expense' : 'income',
            'category'    => $request->category,
            'amount'      => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Transaksi arus kas berhasil dicatat!');
    }
}