<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_active', true)->get();
        return view('admin.pos.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'cart' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalCogs = 0;

            // Bikin Order Baru untuk Kasir (Offline Transaction)
            $order = Order::create([
                'invoice_number' => 'INV-POS-' . time(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? '-',
                'order_type' => 'dine_in',
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => 'completed',
                'total_amount' => 0,
                'total_cogs' => 0,
                'gross_profit' => 0,
            ]);

            foreach ($request->cart as $menuId => $item) {
                $quantity = $item['quantity'];
                // Ubah 'recipes.material' jadi 'materials'
                $menu = Menu::with('materials')->findOrFail($menuId);

                $itemCogs = $menu->calculated_hpp;
                $totalAmount += $menu->selling_price * $quantity;
                $totalCogs += $itemCogs * $quantity;

                // 1. Simpan Item Pesanan
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price_at_purchase' => $menu->selling_price,
                    'cogs_at_purchase' => $itemCogs,
                ]);

                // 2. OTOMATIS POTONG STOK BAHAN BAKU (Inventory Deduct via Pivot)
                foreach ($menu->materials as $material) {
                    $pivotQty = $material->pivot->quantity_required ?? 0;
                    $deductAmount = $pivotQty * $quantity;

                    if ($material->stock_quantity < $deductAmount) {
                        throw new \Exception("Stok bahan {$material->name} tidak cukup!");
                    }

                    $material->decrement('stock_quantity', $deductAmount);
                }
            }

            // Update Total Order & Laba
            $order->update([
                'total_amount' => $totalAmount,
                'total_cogs' => $totalCogs,
                'gross_profit' => $totalAmount - $totalCogs,
            ]);

            DB::commit();
            return redirect()->route('pos.index')->with('success', 'Transaksi Kasir Berhasil! Stok bahan baku telah terpotong otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}