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
        $query = Order::with('orderItems.menu')->where('order_type', '!=', 'dine_in')->latest();

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ubah 'recipes.material' menjadi 'materials' sesuai relasi database
        $order->load('orderItems.menu.materials');
        return view('admin.orders.show', compact('order'));
    }

    // Proses pesanan online (Ubah status & Potong Stok jika status baru 'processing')
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            // Jika status berubah dari pending/paid ke processing/completed, POTONG STOK BAHAN BAKU
            if (in_array($oldStatus, ['pending', 'paid']) && in_array($newStatus, ['processing', 'completed'])) {
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu->load('materials');
                    
                    foreach ($menu->materials as $material) {
                        // Ambil jumlah kebutuhan dari tabel pivot (quantity_required)
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