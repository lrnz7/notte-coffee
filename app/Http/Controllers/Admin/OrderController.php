<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('orderItems.menu')
                      ->where('invoice_number', 'not like', 'NOTTE-POS-%')
                      ->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // PERBAIKAN: Load relasi material, bukan ingredient
        $order->load('orderItems.menu.recipes.material');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,pending_payment,waiting_verification,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            // LOGIKA 1: Potong stok
            if (in_array($oldStatus, ['pending', 'pending_payment', 'waiting_verification']) && in_array($newStatus, ['processing', 'completed'])) {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('recipes.material');
                    
                    foreach ($menu->recipes as $recipe) {
                        $material = $recipe->material;
                        if (!$material) continue;

                        $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                        $deductAmount = $qtyRequired * $item->quantity;

                        if ($material->stock_quantity < $deductAmount) {
                            throw new \Exception("Gagal memproses! Stok bahan {$material->name} tidak cukup.");
                        }

                        $material->decrement('stock_quantity', $deductAmount);
                    }
                }
            }

            // LOGIKA 2: Restock otomatis jika pesanan dibatalkan
            if (in_array($oldStatus, ['processing', 'completed']) && $newStatus === 'cancelled') {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('recipes.material');
                    
                    foreach ($menu->recipes as $recipe) {
                        $material = $recipe->material;
                        if ($material) {
                            $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                            $restockAmount = $qtyRequired * $item->quantity;
                            $material->increment('stock_quantity', $restockAmount);
                        }
                    }
                }
            }

            $order->update(['status' => $newStatus]);
            DB::commit();

            return redirect()->back()->with('success', "Status pesanan {$order->invoice_number} berhasil diperbarui menjadi " . strtoupper(str_replace('_', ' ', $newStatus)));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}