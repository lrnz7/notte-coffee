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
            // CEK LIMIT ANTREAN DAPUR
            $maxQueue = config('notte.max_kitchen_queue', 5);
            $activeOrders = Order::where('status', 'processing')->count();
            if ($activeOrders >= $maxQueue) {
                throw new \Exception("Mohon maaf Kak, antrean dapur saat ini sedang padat ({$activeOrders}/{$maxQueue} pesanan). Mohon tunggu beberapa saat sebelum membuat pesanan baru.");
            }

            $totalAmount = 0;
            $totalCogs = 0;
            $totalHpp = 0;
            $discountAmount = 0;
            
            $user = Auth::user();
            $isCustomer = ($user && $user->role === 'customer');

            // Hitung Total Belanja & HPP (Moving Average Cost dari Resep Bahan Baku)
            foreach ($request->cart as $item) {
                $menu = Menu::with('recipes.material')->lockForUpdate()->findOrFail($item['menu_id']);
                $quantity = (int)$item['quantity'];
                
                $itemPrice = $menu->selling_price;

                $itemHpp = 0;
                foreach ($menu->recipes as $recipe) {
                    if ($recipe->material) {
                        $qtyReq = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                        $itemHpp += ($qtyReq * (float) $recipe->material->unit_price);
                    }
                }
                if ($itemHpp == 0) {
                    $itemHpp = method_exists($menu, 'getCalculatedHppAttribute') ? $menu->calculated_hpp : ($menu->selling_price * 0.4);
                }

                $totalAmount += $itemPrice * $quantity;
                $totalCogs += $itemHpp * $quantity;
                $totalHpp += $itemHpp * $quantity;
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
                'order_source' => 'online',
                'source' => 'web',
                'status' => 'pending_payment',
                'total_amount' => $totalAmount,
                'total_cogs' => $totalCogs,
                'total_hpp' => $totalHpp,
                'discount_amount' => $discountAmount,
                'gross_profit' => $totalAmount - $totalHpp,
            ]);

            // Simpan Detail Item Order
            foreach ($request->cart as $item) {
                $menu = Menu::with('recipes.material')->findOrFail($item['menu_id']);
                $quantity = (int)$item['quantity'];
                
                $itemHpp = 0;
                foreach ($menu->recipes as $recipe) {
                    if ($recipe->material) {
                        $qtyReq = $recipe->quantity ?? $recipe->quantity_required ?? 0;
                        $itemHpp += ($qtyReq * (float) $recipe->material->unit_price);
                    }
                }
                if ($itemHpp == 0) {
                    $itemHpp = method_exists($menu, 'getCalculatedHppAttribute') ? $menu->calculated_hpp : ($menu->selling_price * 0.4);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price_at_purchase' => $menu->selling_price,
                    'cogs_at_purchase' => $itemHpp,
                    'note' => $item['note'] ?? '-',
                ]);
            }

            DB::commit();

            session(['active_invoice' => $order->invoice_number]);

            return redirect()->route('customer.order.track', $order->invoice_number)
                ->with('success', 'Pesanan berhasil dibuat! Silakan bayar via QRIS.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
                
                // Simpan secara aman di disk 'local' (storage/app/payment_proofs)
                $path = $file->store('payment_proofs', 'local');
                $filename = basename($path);

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