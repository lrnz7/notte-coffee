@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manajemen Pesanan Online</h2>
        <p class="text-gray-600 text-xs md:text-sm mt-1">Daftar pesanan masuk eksklusif dari website e-commerce NOTTE.</p>
    </div>
    
    <!-- Filter Status (Bisa di-swipe horizontal di HP) -->
    <div class="flex overflow-x-auto hide-scrollbar space-x-2 pb-2 md:pb-0 w-full md:w-auto snap-x">
        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="shrink-0 px-3 py-2 md:py-1.5 text-[11px] md:text-xs rounded-md {{ !request('status') ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-700' }} font-semibold hover:bg-amber-700 hover:text-white transition snap-start">Semua</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'waiting_verification']) }}" class="shrink-0 px-3 py-2 md:py-1.5 text-[11px] md:text-xs rounded-md {{ request('status') == 'waiting_verification' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-800' }} font-semibold hover:bg-purple-200 snap-start">Perlu Verifikasi</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}" class="shrink-0 px-3 py-2 md:py-1.5 text-[11px] md:text-xs rounded-md {{ request('status') == 'processing' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800' }} font-semibold hover:bg-blue-200 snap-start">Diproses</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}" class="shrink-0 px-3 py-2 md:py-1.5 text-[11px] md:text-xs rounded-md {{ request('status') == 'completed' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-800' }} font-semibold hover:bg-emerald-200 snap-start">Selesai</a>
    </div>
</div>

<!-- DYNAMIC TIME-RANGE FILTER -->
@include('admin.partials.date_filter')

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm font-semibold">
        {{ session('error') }}
    </div>
@endif

<!-- TAMPILAN MOBILE (KARTU) -->
<div class="block md:hidden space-y-4">
    @forelse($orders as $order)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col gap-3">
        <div class="flex justify-between items-start border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">{{ $order->invoice_number }}</h3>
                <p class="text-[10px] text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                @if($order->status == 'pending_payment')
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-amber-100 text-amber-800 uppercase">Menunggu Bayar</span>
                @elseif($order->status == 'waiting_verification')
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-purple-100 text-purple-800 uppercase animate-pulse">Cek Bukti</span>
                @elseif($order->status == 'processing')
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-blue-100 text-blue-800 uppercase">Dapur</span>
                @elseif($order->status == 'completed')
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-emerald-100 text-emerald-800 uppercase">Selesai</span>
                @elseif($order->status == 'cancelled')
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-red-100 text-red-800 uppercase">Batal</span>
                @else
                    <span class="px-2 py-1 text-[9px] font-bold rounded bg-gray-100 text-gray-800 uppercase">{{ $order->status }}</span>
                @endif
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-md p-3 flex justify-between items-center border border-gray-100">
            <div>
                <p class="font-bold text-xs text-gray-800">{{ $order->customer_name }}</p>
                <p class="text-[10px] text-gray-500 mt-0.5">{{ $order->customer_phone ?? '-' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[9px] font-bold text-gray-500 uppercase bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded inline-block mb-1">{{ $order->payment_method }}</p>
                <p class="font-black text-amber-600 text-sm block">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <a href="{{ route('orders.show', $order->id) }}" class="w-full bg-slate-900 active:bg-slate-800 text-white text-center py-2.5 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm transition">
            Detail & Verifikasi
        </a>
    </div>
    @empty
    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-500 shadow-sm">
        <p class="text-sm font-semibold">Belum ada pesanan online.</p>
    </div>
    @endforelse
</div>

<!-- TAMPILAN DESKTOP (TABEL) -->
<div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
                    <p class="text-xs text-gray-500">{{ $order->customer_phone ?? '-' }}</p>
                </td>
                <td class="p-4">
                    <span class="inline-block px-2 py-0.5 text-xs font-bold rounded uppercase bg-amber-100 text-amber-800">E-COMMERCE</span>
                    <span class="block text-xs text-gray-500 uppercase mt-1">{{ $order->payment_method }}</span>
                </td>
                <td class="p-4 font-extrabold text-amber-600">
                    Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
                <td class="p-4">
                    @if($order->status == 'pending_payment')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">Menunggu Bayar</span>
                    @elseif($order->status == 'waiting_verification')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 animate-pulse">Cek Bukti Bayar</span>
                    @elseif($order->status == 'processing')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">Diproses Dapur</span>
                    @elseif($order->status == 'completed')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">Selesai</span>
                    @elseif($order->status == 'cancelled')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">Dibatalkan</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800">{{ $order->status }}</span>
                    @endif
                </td>
                <td class="p-4 text-center">
                    <a href="{{ route('orders.show', $order->id) }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white text-xs px-3 py-1.5 rounded font-semibold transition">
                        Detail & Verifikasi
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-gray-500 font-semibold">Belum ada pesanan online yang masuk pada rentang waktu ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100 bg-gray-50">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection