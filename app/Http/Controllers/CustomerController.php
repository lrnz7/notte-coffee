<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $discountAmount = 0;
            
            $user = Auth::user();
            $isCustomer = ($user && $user->role === 'customer');

            // Hitung Total Belanja & HPP (COGS)
            foreach ($request->cart as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $quantity = (int)$item['quantity'];
                
                $itemPrice = $menu->selling_price;
                // Ambil HPP dari relasi resep bahan baku di ERP
                $itemCogs = method_exists($menu, 'getCalculatedHppAttribute') ? $menu->calculated_hpp : ($menu->selling_price * 0.4);

                $totalAmount += $itemPrice * $quantity;
                $totalCogs += $itemCogs * $quantity;
            }

            // Potongan Diskon 50% Pengguna Baru (Hanya untuk Customer)
            if ($isCustomer && !$user->has_claimed_welcome_discount) {
                $discountAmount = $totalAmount * 0.5;
                $totalAmount = $totalAmount - $discountAmount;
                
                User::where('id', $user->id)->update(['has_claimed_welcome_discount' => true]);
            }

            // Buat Master Order di ERP Admin
            $order = Order::create([
                'user_id' => $isCustomer ? $user->id : null,
                'invoice_number' => 'INV-ONLINE-' . time(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address ?? '-',
                'order_type' => $request->order_type,
                'payment_method' => 'qris',
                'order_source' => 'online_web', // SINKRONISASI TAB DASHBOARD
                'source' => 'web',
                'status' => 'pending_payment',
                'total_amount' => $totalAmount,
                'total_cogs' => $totalCogs, // HPP Masuk ERP
                'discount_amount' => $discountAmount,
                'gross_profit' => $totalAmount - $totalCogs, // Profit Bersih Masuk ERP
            ]);

            // Simpan Detail Item Order
            foreach ($request->cart as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $quantity = (int)$item['quantity'];
                $itemCogs = method_exists($menu, 'getCalculatedHppAttribute') ? $menu->calculated_hpp : ($menu->selling_price * 0.4);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price_at_purchase' => $menu->selling_price,
                    'cogs_at_purchase' => $itemCogs,
                    'note' => $item['note'] ?? '-',
                ]);
            }

            DB::commit();

            session(['active_invoice' => $order->invoice_number]);

            return redirect()->route('customer.order.track', $order->invoice_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan bayar via QRIS.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    public function trackOrder($invoice)
    {
        $order = Order::with('orderItems.menu')->where('invoice_number', $invoice)->firstOrFail();
        session(['active_invoice' => $order->invoice_number]);
        return view('customer.track', compact('order'));
    }

    public function uploadProof(Request $request, $invoice)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $order = Order::where('invoice_number', $invoice)->firstOrFail();

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'proof_' . time() . '.' . $file->getClientOriginalExtension();
                
                $destinationPath = public_path('uploads/payment_proofs');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $filename);

                $order->update([
                    'payment_proof' => $filename,
                    'status' => 'waiting_verification',
                ]);

                return back()->with('success', 'Bukti pembayaran berhasil terkirim!');
            }

            return back()->with('error', 'File bukti pembayaran tidak ditemukan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function account()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('customer.account', compact('user', 'orders'));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        User::where('id', $user->id)->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}