<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CashFlow; // Wajib import model CashFlow
use App\Traits\FilterableByDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use FilterableByDate;

    public function index(Request $request)
    {
        $dateFilter = $this->resolveDateFilter($request);

        $query = Order::with('orderItems.menu')
                      ->where('invoice_number', 'not like', 'NOTTE-POS-%')
                      ->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $this->applyDateFilter($query, $dateFilter, 'created_at');

        $orders = $query->paginate(20)->appends($request->all());
        return view('admin.orders.index', compact('orders', 'dateFilter'));
    }

    public function show(Order $order)
    {
        // PERBAIKAN: Load relasi material, bukan ingredient
        $order->load('orderItems.menu.recipes.material');
        return view('admin.orders.show', compact('order'));
    }

    public function servePaymentProof(Order $order)
    {
        if (!$order->payment_proof) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $filePath = 'payment_proofs/' . $order->payment_proof;
        
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
            return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($filePath));
        }

        // Backward compatibility for existing files in public folder if any
        $legacyPath = public_path('uploads/payment_proofs/' . $order->payment_proof);
        if (file_exists($legacyPath)) {
            return response()->file($legacyPath);
        }

        abort(404, 'File bukti pembayaran tidak ada di server.');
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
                $order->load(['orderItems.menu.recipes.material']);
                
                foreach ($order->orderItems as $item) {
                    $menu = $item->menu;
                    if (!$menu) continue;
                    
                    foreach ($menu->recipes as $recipe) {
                        $material = $recipe->material;
                        if (!$material) continue;

                        $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                        $deductAmount = $qtyRequired * $item->quantity;

                        $lockedMaterial = \App\Models\Material::lockForUpdate()->find($material->id);
                        if (!$lockedMaterial || $lockedMaterial->stock_quantity < $deductAmount) {
                            throw new \Exception("Gagal memproses! Stok bahan {$material->name} tidak cukup (Tersedia: " . ($lockedMaterial->stock_quantity ?? 0) . " {$material->unit}).");
                        }

                        $lockedMaterial->decrement('stock_quantity', $deductAmount);
                    }
                }
            }

            // LOGIKA 2: Restock otomatis jika pesanan dibatalkan
            if (in_array($oldStatus, ['processing', 'completed']) && $newStatus === 'cancelled') {
                $order->load(['orderItems.menu.recipes.material']);

                foreach ($order->orderItems as $item) {
                    $menu = $item->menu;
                    if (!$menu) continue;
                    
                    foreach ($menu->recipes as $recipe) {
                        $material = $recipe->material;
                        if ($material) {
                            $qtyRequired = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                            $restockAmount = $qtyRequired * $item->quantity;
                            
                            $lockedMaterial = \App\Models\Material::lockForUpdate()->find($material->id);
                            if ($lockedMaterial) {
                                $lockedMaterial->increment('stock_quantity', $restockAmount);
                            }
                        }
                    }
                }
            }

            // LOGIKA 3: Catat ke Arus Kas saat pesanan Online Selesai (Cegah double-counting dari POS)
            if ($order->order_source === 'online' && $oldStatus !== 'completed' && $newStatus === 'completed') {
                CashFlow::create([
                    'type' => 'inflow',
                    'category' => 'Penjualan',
                    'amount' => $order->total_amount,
                    'description' => "Penjualan Online (E-Commerce) - {$order->invoice_number}",
                    'date' => now()->toDateString(),
                ]);
            }

            // LOGIKA 4: Tarik uang dari Arus Kas (Refund) jika pesanan online selesai lalu dibatalkan
            if ($order->order_source === 'online' && $oldStatus === 'completed' && $newStatus === 'cancelled') {
                CashFlow::create([
                    'type' => 'outflow',
                    'category' => 'Refund',
                    'amount' => $order->total_amount,
                    'description' => "Refund/Pembatalan Pesanan Online - {$order->invoice_number}",
                    'date' => now()->toDateString(),
                ]);
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