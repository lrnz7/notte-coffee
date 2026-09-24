<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index()
    {
        // Ambil semua customer beserta relasi orders untuk hitung LTV (Lifetime Value)
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->orderBy('total_spent', 'desc')
            ->get();
            
        return view('admin.crm.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $orders = $customer->orders()->latest()->get();
        return view('admin.crm.show', compact('customer', 'orders'));
    }
}