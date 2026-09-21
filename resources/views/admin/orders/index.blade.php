@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pesanan Online</h2>
        <p class="text-gray-600 text-sm">Daftar pesanan masuk dari website e-commerce (Delivery & Pickup).</p>
    </div>
    
    <!-- Filter Status -->
    <div class="flex space-x-2">
        <a href="{{ route('orders.index') }}" class="px-3 py-1.5 text-xs rounded-md bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300">Semua</a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="px-3 py-1.5 text-xs rounded-md bg-amber-100 text-amber-800 font-semibold hover:bg-amber-200">Pending</a>
        <a href="{{ route('orders.index', ['status' => 'processing']) }}" class="px-3 py-1.5 text-xs rounded-md bg-blue-100 text-blue-800 font-semibold hover:bg-blue-200">Diproses</a>
        <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-3 py-1.5 text-xs rounded-md bg-emerald-100 text-emerald-800 font-semibold hover:bg-emerald-200">Selesai</a>
    </div>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase">
                <th class="p-4">Invoice</th>
                <th class="p-4">Pelanggan</th>
                <th class="p-4">Tipe & Pembayaran</th>
                <th class="p-4">Total</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-bold text-slate-800">
                    {{ $order->invoice_number }}
                    <span class="block text-xs font-normal text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</span>
                </td>
                <td class="p-4">
                    <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                    <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                </td>
                <td class="p-4">
                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded uppercase bg-slate-100 text-slate-700">
                        {{ $order->order_type }}
                    </span>
                    <span class="block text-xs text-gray-500 uppercase mt-0.5">{{ $order->payment_method }}</span>
                </td>
                <td class="p-4 font-extrabold text-amber-600">
                    Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
                <td class="p-4">
                    @if($order->status == 'pending')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">Pending</span>
                    @elseif($order->status == 'processing')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">Diproses</span>
                    @elseif($order->status == 'completed')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Selesai</span>
                    @elseif($order->status == 'cancelled')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">Batal</span>
                    @endif
                </td>
                <td class="p-4 text-center">
                    <a href="{{ route('orders.show', $order->id) }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white text-xs px-3 py-1.5 rounded font-semibold">
                        Detail & Ubah Status
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-gray-500">Belum ada pesanan online yang masuk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection