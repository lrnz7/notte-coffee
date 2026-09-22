<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Filter: Cuma tarik pesanan yang nomor invoice-nya BUKAN dari POS Kasir (Online Only)
        $query = Order::with('orderItems.menu')
                      ->where('invoice_number', 'not like', 'NOTTE-POS-%')
                      ->latest();

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('orderItems.menu.materials');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            // Potong stok HANYA jika dari pending/paid ke processing/completed
            if (in_array($oldStatus, ['pending', 'paid']) && in_array($newStatus, ['processing', 'completed'])) {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('materials');
                    
                    foreach ($menu->materials as $material) {
                        $pivotQty = $material->pivot->quantity_required ?? 0;
                        $deductAmount = $pivotQty * $item->quantity;

                        if ($material->stock_quantity < $deductAmount) {
                            throw new \Exception("Gagal memproses! Stok bahan {$material->name} tidak cukup.");
                        }

                        $material->decrement('stock_quantity', $deductAmount);
                    }
                }
            }

            $order->update(['status' => $newStatus]);
            DB::commit();

            return redirect()->back()->with('success', "Status pesanan {$order->invoice_number} berhasil diperbarui menjadi " . strtoupper($newStatus));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}