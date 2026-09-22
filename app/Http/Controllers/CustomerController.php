<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_active', true)->get();
        return view('landing', compact('menus'));
    }

    public function menu()
    {
        $menus = Menu::where('is_active', true)->get();
        return view('customer.menu', compact('menus'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:delivery,pickup',
            'shipping_address' => 'required_if:order_type,delivery',
            'cart' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $totalCogs = 0;

            $order = Order::create([
                'invoice_number' => 'INV-ONLINE-' . time(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'order_type' => $request->order_type,
                'payment_method' => $request->payment_method ?? 'qris',
                'status' => 'pending_payment',
                'total_amount' => 0,
                'total_cogs' => 0,
                'gross_profit' => 0,
            ]);

            foreach ($request->cart as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $itemCogs = $menu->calculated_hpp;
                $totalAmount += $menu->selling_price * $item['quantity'];
                $totalCogs += $itemCogs * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $menu->selling_price,
                    'cogs_at_purchase' => $itemCogs,
                    'note' => $item['note'] ?? '-',
                ]);
            }

            $order->update([
                'total_amount' => $totalAmount,
                'total_cogs' => $totalCogs,
                'gross_profit' => $totalAmount - $totalCogs,
            ]);

            DB::commit();

            // Simpan nomor invoice aktif ke session buat Floating Tracker Bar di customer menu
            session(['active_invoice' => $order->invoice_number]);

            return redirect()->route('customer.order.track', $order->invoice_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function trackOrder($invoice)
    {
        $order = Order::with('orderItems.menu')->where('invoice_number', $invoice)->firstOrFail();
        return view('customer.track', compact('order'));
    }

    public function uploadProof(Request $request, $invoice)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Kapasitas 5MB
        ]);

        try {
            $order = Order::where('invoice_number', $invoice)->firstOrFail();

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'proof_' . time() . '.' . $file->getClientOriginalExtension();
                
                // FUNGSI KRUSIAL: Bikin folder otomatis kalau belum ada
                $destinationPath = public_path('uploads/payment_proofs');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);

                $order->update([
                    'payment_proof' => $filename,
                    'status' => 'waiting_verification',
                ]);

                return back()->with('success', 'Bukti pembayaran berhasil dikirim! Kasir sedang memverifikasi pesanan Anda.');
            }

            return back()->with('error', 'Gagal: File foto tidak terdeteksi oleh sistem.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}