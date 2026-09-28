@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manajemen Bahan Baku</h2>
        <p class="text-gray-600 text-xs md:text-sm mt-1">Daftar stok dan harga beli bahan baku per unit.</p>
    </div>
    <a href="{{ route('materials.create') }}" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold px-4 py-2.5 md:py-2 rounded text-center shadow-sm text-xs md:text-sm uppercase tracking-wide transition w-full md:w-auto">
        + Tambah Bahan Baku
    </a>
</div>

<!-- TAMPILAN MOBILE (KARTU) -->
<div class="block md:hidden space-y-4">
    @forelse($materials as $material)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col gap-2">
        <div class="flex justify-between items-start border-b border-gray-100 pb-2">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">{{ $material->name }}</h3>
                <p class="text-[10px] text-gray-500 font-semibold mt-0.5">Satuan: {{ $material->unit }}</p>
            </div>
            <span class="font-black text-amber-600 text-sm">Rp{{ number_format($material->unit_price, 0, ',', '.') }}</span>
        </div>
        
        <div class="flex justify-between items-center text-xs py-1.5">
            <span class="text-gray-500 font-semibold">Stok Saat Ini:</span>
            <span class="font-black text-sm {{ $material->stock_quantity <= $material->min_stock_alert ? 'text-red-600 bg-red-50 px-2 py-0.5 rounded' : 'text-slate-800' }}">
                {{ $material->stock_quantity }}
            </span>
        </div>
        <div class="flex justify-between items-center text-xs py-1.5">
            <span class="text-gray-500 font-semibold">Min. Alert:</span>
            <span class="font-bold text-gray-700">{{ $material->min_stock_alert }}</span>
        </div>
        
        <div class="flex gap-2 pt-3 mt-1 border-t border-gray-100">
            <a href="{{ route('materials.edit', $material->id) }}" class="flex-1 bg-slate-100 active:bg-slate-200 text-slate-700 text-center py-2.5 rounded text-[11px] font-bold uppercase tracking-wider transition">
                Edit
            </a>
            <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin mau hapus bahan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-50 hover:bg-red-100 active:bg-red-200 text-red-600 text-center py-2.5 rounded text-[11px] font-bold uppercase tracking-wider transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-500 shadow-sm">
        <p class="text-sm font-semibold">Belum ada data bahan baku.</p>
    </div>
    @endforelse
</div>

<!-- TAMPILAN DESKTOP (TABEL) -->
<div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-bold text-gray-600">
            <tr>
                <th class="px-6 py-4">Nama Bahan</th>
                <th class="px-6 py-4">Satuan</th>
                <th class="px-6 py-4">Harga / Unit</th>
                <th class="px-6 py-4">Stok Saat Ini</th>
                <th class="px-6 py-4">Min. Alert</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($materials as $material)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-bold text-gray-900">{{ $material->name }}</td>
                <td class="px-6 py-4 text-gray-600 font-semibold">{{ $material->unit }}</td>
                <td class="px-6 py-4 font-bold text-gray-900">Rp{{ number_format($material->unit_price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 font-black {{ $material->stock_quantity <= $material->min_stock_alert ? 'text-red-600' : 'text-slate-800' }}">
                    {{ $material->stock_quantity }}
                </td>
                <td class="px-6 py-4 text-gray-500 font-semibold">{{ $material->min_stock_alert }}</td>
                <td class="px-6 py-4 text-center space-x-3">
                    <a href="{{ route('materials.edit', $material->id) }}" class="text-amber-600 hover:text-amber-800 font-bold transition">Edit</a>
                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus bahan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-bold transition">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-semibold">Belum ada data bahan baku.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection