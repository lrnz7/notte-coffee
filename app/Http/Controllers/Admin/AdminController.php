<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Material;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Ringkasan Keuangan (Hanya hitung transaksi yang completed)
        $completedOrders = Order::where('status', 'completed');
        $totalRevenue = (clone $completedOrders)->sum('total_amount');
        $totalCogs = (clone $completedOrders)->sum('total_cogs');
        $netProfit = $totalRevenue - $totalCogs;

        // 2. Total Transaksi
        $totalOrdersCount = Order::count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();

        // 3. Low Stock Alert (Bahan baku yang stoknya <= min_stock_alert)
        $lowStockMaterials = Material::whereRaw('stock_quantity <= min_stock_alert')->get();

        // 4. Pesanan Terbaru (Ambil 30 transaksi terakhir biar semua tab kebagian data)
        $recentOrders = Order::latest()->take(30)->get()->map(function ($order) {
            // Jika order_source masih kosong di database, otomatis beri default berdasarkan source/invoice
            if (empty($order->order_source)) {
                if (str_contains($order->invoice_number, 'NOTTE-POS')) {
                    $order->order_source = 'offline_pos';
                } else {
                    $order->order_source = 'online_web';
                }
            }
            return $order;
        });

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalCogs',
            'netProfit',
            'totalOrdersCount',
            'pendingOrdersCount',
            'lowStockMaterials',
            'recentOrders'
        ));
    }
}