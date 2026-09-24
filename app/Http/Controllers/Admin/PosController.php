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
        // Panggil relasi recipes dan ingredient
        $menus = Menu::with('recipes.ingredient')->where('is_active', true)->get();
        
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
            'items'          => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.ice_level' => 'nullable|string',
            'items.*.sugar_level' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalCogs = 0;
            $itemsToCreate = [];

            foreach ($request->items as $itemData) {
                $menu = Menu::with('recipes.ingredient')->findOrFail($itemData['menu_id']);
                $subtotal = $menu->selling_price * $itemData['quantity'];
                $totalAmount += $subtotal;

                $unitCogs = 0;
                foreach ($menu->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (!$ingredient) continue;

                    $qtyRequired = $recipe->quantity;
                    $costPerUnit = $ingredient->cost_per_unit ?? 0;
                    $unitCogs += ($qtyRequired * $costPerUnit);

                    // Potong Stok Fisik di tabel ingredients
                    $totalRequired = $qtyRequired * $itemData['quantity'];
                    $ingredient->decrement('stock', $totalRequired);
                }

                $itemCogsTotal = $unitCogs * $itemData['quantity'];
                $totalCogs += $itemCogsTotal;

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

            $grossProfit = $totalAmount - $totalCogs;
            $invoiceNumber = 'NOTTE-POS-' . date('YmdHis') . '-' . rand(100, 999);

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'customer_name'  => $request->customer_name,
                'total_amount'   => $totalAmount,
                'total_cogs'     => $totalCogs,
                'gross_profit'   => $grossProfit,
                'status'         => 'completed', 
                'payment_status' => 'paid',
                'order_status'   => 'completed', 
                'payment_method' => $request->payment_method,
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
                        'description'      => 'Transaksi POS Kasir: ' . $invoiceNumber,
                        'transaction_date' => now(),
                    ]);
                }
            } catch (\Exception $ex) {
                // Ignore safe catch
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Transaksi berhasil disimpan & selesai!',
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
}