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
        // Load relasi yang benar sesuai ERP
        $order->load('orderItems.menu.recipes.ingredient');
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
            // LOGIKA 1: Potong stok jika berubah dari status awal ke status diproses/selesai
            if (in_array($oldStatus, ['pending', 'pending_payment', 'waiting_verification']) && in_array($newStatus, ['processing', 'completed'])) {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('recipes.ingredient');
                    
                    foreach ($menu->recipes as $recipe) {
                        $ingredient = $recipe->ingredient;
                        if (!$ingredient) continue;

                        $qtyRequired = $recipe->quantity;
                        $deductAmount = $qtyRequired * $item->quantity;

                        if ($ingredient->stock < $deductAmount) {
                            throw new \Exception("Gagal memproses! Stok bahan {$ingredient->name} tidak cukup.");
                        }

                        $ingredient->decrement('stock', $deductAmount);
                    }
                }
            }

            // LOGIKA 2: Restock otomatis jika pesanan yang sudah diproses tiba-tiba dibatalkan
            if (in_array($oldStatus, ['processing', 'completed']) && $newStatus === 'cancelled') {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('recipes.ingredient');
                    
                    foreach ($menu->recipes as $recipe) {
                        $ingredient = $recipe->ingredient;
                        if ($ingredient) {
                            $restockAmount = $recipe->quantity * $item->quantity;
                            $ingredient->increment('stock', $restockAmount);
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