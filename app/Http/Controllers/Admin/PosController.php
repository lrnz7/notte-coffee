<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $menus = Menu::with('recipes.material')->where('is_active', true)->get();
        
        $recentPosOrders = Order::where('invoice_number', 'like', 'NOTTE-POS-%')
                                ->latest()
                                ->limit(20)
                                ->get();

        return view('admin.pos.index', compact('menus', 'recentPosOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'payment_method' => 'required|string',
            'order_source'   => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.ice_level' => 'nullable|string',
            'items.*.sugar_level' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $maxQueue = config('notte.max_kitchen_queue', 5);
            $activeOrders = Order::where('status', 'processing')->count();
            if ($activeOrders >= $maxQueue) {
                return response()->json([
                    'success' => false,
                    'message' => "Antrean dapur sedang penuh ({$activeOrders}/{$maxQueue} pesanan berjalan). Selesaikan pesanan di antrean sebelum memasukkan pesanan baru."
                ], 422);
            }

            $totalAmount = 0;
            $totalCogs = 0;
            $totalHpp = 0;
            $itemsToCreate = [];

            foreach ($request->items as $itemData) {
                $menu = Menu::with('recipes.material')->lockForUpdate()->findOrFail($itemData['menu_id']);
                $subtotal = $menu->selling_price * $itemData['quantity'];
                $totalAmount += $subtotal;

                $unitCogs = 0;
                foreach ($menu->recipes as $recipe) {
                    $material = $recipe->material;
                    if (!$material) continue;

                    $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                    $costPerUnit = (float) ($material->unit_price ?? 0);
                    $unitCogs += ($qtyRequired * $costPerUnit);

                    $totalRequired = $qtyRequired * $itemData['quantity'];
                    
                    $lockedMaterial = \App\Models\Material::lockForUpdate()->find($material->id);
                    if (!$lockedMaterial || $lockedMaterial->stock_quantity < $totalRequired) {
                        $available = $lockedMaterial ? $lockedMaterial->stock_quantity : 0;
                        throw new \Exception("Stok bahan {$material->name} tidak mencukupi untuk menu {$menu->name}. Dibutuhkan: {$totalRequired} {$material->unit}, Tersedia: {$available} {$material->unit}.");
                    }

                    $lockedMaterial->decrement('stock_quantity', $totalRequired);
                }

                $itemCogsTotal = $unitCogs * $itemData['quantity'];
                $totalCogs += $itemCogsTotal;
                $totalHpp += $itemCogsTotal;

                $itemsToCreate[] = [
                    'menu_id'           => $menu->id,
                    'quantity'          => $itemData['quantity'],
                    'price_at_purchase' => $menu->selling_price,
                    'unit_price'        => $menu->selling_price,
                    'cogs_at_purchase'  => $unitCogs,
                    'subtotal'          => $subtotal,
                    'ice_level'         => $itemData['ice_level'] ?? 'Normal Ice',
                    'sugar_level'       => $itemData['sugar_level'] ?? 'Normal Sugar',
                ];
            }

            $grossProfit = $totalAmount - $totalHpp;
            $invoiceNumber = 'NOTTE-POS-' . date('YmdHis') . '-' . rand(100, 999);

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'customer_name'  => $request->customer_name,
                'total_amount'   => $totalAmount,
                'total_cogs'     => $totalCogs,
                'total_hpp'      => $totalHpp,
                'gross_profit'   => $grossProfit,
                'status'         => 'processing', 
                'payment_status' => 'paid',
                'order_status'   => 'processing', 
                'payment_method' => $request->payment_method,
                'order_source'   => 'pos',
                'source'         => 'pos',
            ]);

            foreach ($itemsToCreate as $item) {
                OrderItem::create([
                    'order_id'          => $order->id,
                    'menu_id'           => $item['menu_id'],
                    'quantity'          => $item['quantity'],
                    'price_at_purchase' => $item['price_at_purchase'],
                    'unit_price'        => $item['unit_price'],
                    'cogs_at_purchase'  => $item['cogs_at_purchase'],
                    'subtotal'          => $item['subtotal'],
                    'ice_level'         => $item['ice_level'],
                    'sugar_level'       => $item['sugar_level'],
                ]);
            }

            try {
                if (class_exists(CashFlow::class)) {
                    CashFlow::create([
                        'type'             => 'inflow',
                        'category'         => 'POS Sales',
                        'amount'           => $totalAmount,
                        'description'      => 'Transaksi POS Kasir (' . ($request->order_source ?? 'POS') . '): ' . $invoiceNumber,
                        'transaction_date' => now(),
                    ]);
                }
            } catch (\Exception $ex) {
                // Ignore safe catch
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan dibayar dan masuk antrean dapur!',
                'invoice_number' => $invoiceNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // TAMBAHAN FASE 2: API Tarik Antrean Dapur Real-time
    public function activeQueue()
    {
        $orders = Order::with('orderItems.menu')
            ->where('status', 'processing')
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }

    // TAMBAHAN FASE 2: API Eksekusi Selesai KDS
    public function markCompleted(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->status !== 'processing') {
                return response()->json(['success' => false, 'message' => 'Pesanan sudah selesai atau belum diproses.']);
            }
            
            $order->update([
                'status' => 'completed',
                'order_status' => 'completed'
            ]);
            
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil diselesaikan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // FEATURE 1: SHIFT CLOSING & RECONCILIATION
    // ==========================================

    /**
     * Get system sales summary for current active shift.
     */
    public function getShiftSummary(Request $request)
    {
        $cashierId = auth()->id() ?? $request->user()?->id;

        // Cari closing terakhir oleh kasir ini
        $lastClosing = \App\Models\CashierClosing::where('user_id', $cashierId)
            ->latest('closing_time')
            ->first();

        $shiftStartTime = $lastClosing ? $lastClosing->closing_time : now()->startOfDay();

        // Ambil transaksi POS kasir sejak shift dimulai
        $posOrders = Order::where(function ($q) {
                $q->where('order_source', 'pos')
                  ->orWhere('invoice_number', 'like', 'NOTTE-POS-%')
                  ->orWhere('invoice_number', 'like', 'OFFLINE-POS-%');
            })
            ->where('created_at', '>=', $shiftStartTime)
            ->whereIn('status', ['processing', 'completed', 'paid'])
            ->get();

        $cashSales = (float) $posOrders->where('payment_method', 'Cash')->sum('total_amount');
        $qrisSales = (float) $posOrders->whereIn('payment_method', ['QRIS', 'qris'])->sum('total_amount');
        $debitSales = (float) $posOrders->whereIn('payment_method', ['Debit', 'debit', 'CC', 'transfer'])->sum('total_amount');
        $totalOrdersCount = $posOrders->count();

        return response()->json([
            'success' => true,
            'data' => [
                'shift_start_time'   => $shiftStartTime->format('d M Y H:i:s'),
                'system_cash_sales'  => $cashSales,
                'system_qris_sales'  => $qrisSales,
                'system_debit_sales' => $debitSales,
                'total_sales'        => $cashSales + $qrisSales + $debitSales,
                'total_orders'       => $totalOrdersCount,
                'cashier_name'       => auth()->user()?->name ?? 'Kasir NOTTE'
            ]
        ]);
    }

    /**
     * Execute cashier shift closing and record financial discrepancy.
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'opening_cash'        => 'required|numeric|min:0',
            'physical_cash_count' => 'required|numeric|min:0',
            'notes'               => 'nullable|string',
        ]);

        $cashierId = auth()->id() ?? 1;

        DB::beginTransaction();
        try {
            // Re-calculate live system sales
            $lastClosing = \App\Models\CashierClosing::where('user_id', $cashierId)
                ->latest('closing_time')
                ->first();

            $shiftStartTime = $lastClosing ? $lastClosing->closing_time : now()->startOfDay();

            $posOrders = Order::where(function ($q) {
                    $q->where('order_source', 'pos')
                      ->orWhere('invoice_number', 'like', 'NOTTE-POS-%')
                      ->orWhere('invoice_number', 'like', 'OFFLINE-POS-%');
                })
                ->where('created_at', '>=', $shiftStartTime)
                ->whereIn('status', ['processing', 'completed', 'paid'])
                ->get();

            $systemCash = (float) $posOrders->where('payment_method', 'Cash')->sum('total_amount');
            $systemQris = (float) $posOrders->whereIn('payment_method', ['QRIS', 'qris'])->sum('total_amount');
            
            $physicalCash = (float) $request->physical_cash_count;
            $openingCash = (float) $request->opening_cash;

            // Selisih = Uang Fisik - (Uang Kas Awal + Penjualan Tunai Sistem)
            $expectedTotalCash = $openingCash + $systemCash;
            $cashDifference = $physicalCash - $expectedTotalCash;

            $closing = \App\Models\CashierClosing::create([
                'user_id'             => $cashierId,
                'closing_time'        => now(),
                'opening_cash'        => $openingCash,
                'system_cash_sales'   => $systemCash,
                'system_qris_sales'   => $systemQris,
                'physical_cash_count' => $physicalCash,
                'cash_difference'     => $cashDifference,
                'notes'               => $request->notes,
            ]);

            // Jika ada selisih, catat otomatis ke Arus Kas (Adjustment Selisih Kasir)
            if ($cashDifference != 0 && class_exists(CashFlow::class)) {
                $isOutflow = ($cashDifference < 0); // tekor
                CashFlow::create([
                    'type'             => $isOutflow ? 'outflow' : 'inflow',
                    'category'         => 'Selisih Kas Kasir',
                    'amount'           => abs($cashDifference),
                    'description'      => "Adjustment Selisih Kasir Shift Closing (Ref ID: #{$closing->id}) - " . ($isOutflow ? 'Tekor Kas' : 'Surplus Kas'),
                    'date'             => now()->toDateString(),
                    'created_at'       => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Closing kasir shift berhasil disimpan!',
                'data'    => [
                    'closing_id'          => $closing->id,
                    'closing_time'        => $closing->closing_time->format('d M Y H:i:s'),
                    'cashier_name'        => auth()->user()?->name ?? 'Kasir NOTTE',
                    'opening_cash'        => $openingCash,
                    'system_cash_sales'   => $systemCash,
                    'system_qris_sales'   => $systemQris,
                    'physical_cash_count' => $physicalCash,
                    'cash_difference'     => $cashDifference,
                    'status'              => $cashDifference == 0 ? 'MATCH' : ($cashDifference < 0 ? 'SHORTAGE' : 'OVERAGE')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan closing kasir: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // FEATURE 2: OFFLINE POS BATCH SYNC ENGINE
    // ==========================================

    /**
     * Batch process offline orders stored in IndexedDB.
     */
    public function syncOffline(Request $request)
    {
        $request->validate([
            'orders'          => 'required|array|min:1',
            'orders.*.client_id'      => 'nullable|string',
            'orders.*.customer_name'  => 'required|string|max:255',
            'orders.*.payment_method' => 'required|string',
            'orders.*.order_source'   => 'nullable|string',
            'orders.*.items'          => 'required|array|min:1',
            'orders.*.items.*.menu_id'  => 'required|exists:menus,id',
            'orders.*.items.*.quantity' => 'required|integer|min:1',
        ]);

        $syncedInvoices = [];
        $failedOrders = [];

        foreach ($request->orders as $orderData) {
            DB::beginTransaction();
            try {
                $totalAmount = 0;
                $totalCogs = 0;
                $totalHpp = 0;
                $itemsToCreate = [];

                foreach ($orderData['items'] as $itemData) {
                    $menu = Menu::with('recipes.material')->lockForUpdate()->findOrFail($itemData['menu_id']);
                    $subtotal = $menu->selling_price * $itemData['quantity'];
                    $totalAmount += $subtotal;

                    $unitCogs = 0;
                    foreach ($menu->recipes as $recipe) {
                        $material = $recipe->material;
                        if (!$material) continue;

                        $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                        $costPerUnit = (float) ($material->unit_price ?? 0);
                        $unitCogs += ($qtyRequired * $costPerUnit);

                        $totalRequired = $qtyRequired * $itemData['quantity'];
                        
                        $lockedMaterial = \App\Models\Material::lockForUpdate()->find($material->id);
                        if ($lockedMaterial) {
                            $lockedMaterial->decrement('stock_quantity', $totalRequired);
                        }
                    }

                    $itemCogsTotal = $unitCogs * $itemData['quantity'];
                    $totalCogs += $itemCogsTotal;
                    $totalHpp += $itemCogsTotal;

                    $itemsToCreate[] = [
                        'menu_id'           => $menu->id,
                        'quantity'          => $itemData['quantity'],
                        'price_at_purchase' => $menu->selling_price,
                        'unit_price'        => $menu->selling_price,
                        'cogs_at_purchase'  => $unitCogs,
                        'subtotal'          => $subtotal,
                        'ice_level'         => $itemData['ice_level'] ?? 'Normal Ice',
                        'sugar_level'       => $itemData['sugar_level'] ?? 'Normal Sugar',
                    ];
                }

                $grossProfit = $totalAmount - $totalHpp;
                $clientInvoice = $orderData['client_id'] ?? ('OFFLINE-POS-' . date('YmdHis') . '-' . rand(100, 999));
                
                // Pastikan invoice unik
                if (Order::where('invoice_number', $clientInvoice)->exists()) {
                    $invoiceNumber = 'NOTTE-SYNC-' . date('YmdHis') . '-' . rand(100, 999);
                } else {
                    $invoiceNumber = $clientInvoice;
                }

                $order = Order::create([
                    'invoice_number' => $invoiceNumber,
                    'customer_name'  => $orderData['customer_name'],
                    'total_amount'   => $totalAmount,
                    'total_cogs'     => $totalCogs,
                    'total_hpp'      => $totalHpp,
                    'gross_profit'   => $grossProfit,
                    'status'         => 'completed', // Offline POS orders are already paid and handed over
                    'payment_status' => 'paid',
                    'order_status'   => 'completed', 
                    'payment_method' => $orderData['payment_method'],
                    'order_source'   => 'pos',
                    'source'         => 'pos',
                    'created_at'     => isset($orderData['timestamp']) ? \Carbon\Carbon::parse($orderData['timestamp']) : now(),
                ]);

                foreach ($itemsToCreate as $item) {
                    OrderItem::create([
                        'order_id'          => $order->id,
                        'menu_id'           => $item['menu_id'],
                        'quantity'          => $item['quantity'],
                        'price_at_purchase' => $item['price_at_purchase'],
                        'unit_price'        => $item['unit_price'],
                        'cogs_at_purchase'  => $item['cogs_at_purchase'],
                        'subtotal'          => $item['subtotal'],
                        'ice_level'         => $item['ice_level'],
                        'sugar_level'       => $item['sugar_level'],
                    ]);
                }

                if (class_exists(CashFlow::class)) {
                    CashFlow::create([
                        'type'             => 'inflow',
                        'category'         => 'POS Sales',
                        'amount'           => $totalAmount,
                        'description'      => 'Transaksi POS Offline Sync: ' . $invoiceNumber,
                        'date'             => now()->toDateString(),
                        'created_at'       => $order->created_at,
                    ]);
                }

                DB::commit();
                $syncedInvoices[] = [
                    'client_id' => $orderData['client_id'] ?? null,
                    'invoice_number' => $invoiceNumber,
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                $failedOrders[] = [
                    'client_id' => $orderData['client_id'] ?? null,
                    'error'     => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($syncedInvoices) . ' transaksi offline berhasil disinkronkan!',
            'synced'  => $syncedInvoices,
            'failed'  => $failedOrders
        ]);
    }
}