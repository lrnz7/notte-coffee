@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Katalog Menu & Recipe Costing (HPP)</h2>
        <p class="text-gray-600 text-sm">Daftar produk jual beserta kalkulasi HPP otomatis dari bahan baku.</p>
    </div>
    <a href="{{ route('menus.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded shadow-sm text-sm">
        + Buat Menu & Resep Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($menus as $menu)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-start mb-2">
                <div>
                    <span class="text-xs font-semibold uppercase px-2 py-1 bg-amber-100 text-amber-800 rounded">
                        {{ $menu->category }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $menu->name }}</h3>
                </div>
                <p class="text-lg font-extrabold text-amber-600">
                    Rp{{ number_format($menu->selling_price, 0, ',', '.') }}
                </p>
            </div>
            <p class="text-gray-500 text-sm mb-4">{{ $menu->description ?? 'Tidak ada deskripsi.' }}</p>

            <!-- Table Resep / BOM -->
            <div class="bg-gray-50 p-3 rounded-md border border-gray-100 mb-4">
                <p class="text-xs font-bold text-gray-600 uppercase mb-2">Komposisi Resep (BOM):</p>
                <ul class="space-y-1 text-xs text-gray-700">
                    @forelse($menu->recipes as $recipe)
                    <li class="flex justify-between border-b border-gray-200 py-1">
                        <span>• {{ $recipe->ingredient->name ?? 'Bahan Tidak Ditemukan' }} ({{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? 'unit' }})</span>
                        <span class="font-medium text-gray-500">
                            Rp{{ number_format($recipe->quantity * ($recipe->ingredient->cost_per_unit ?? 0), 0, ',', '.') }}
                        </span>
                    </li>
                    @empty
                    <li class="text-gray-400 italic py-1">Belum ada resep bahan baku.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="border-t pt-3 flex justify-between items-center text-sm">
            <div>
                <span class="text-xs text-gray-500">Estimasi HPP:</span>
                <p class="font-bold text-gray-800">Rp{{ number_format($menu->calculated_hpp, 0, ',', '.') }}</p>
            </div>
            <div>
                <span class="text-xs text-gray-500">Estimasi Laba/Cup:</span>
                <p class="font-bold text-emerald-600">
                    Rp{{ number_format($menu->selling_price - $menu->calculated_hpp, 0, ',', '.') }}
                </p>
            </div>
            
            <!-- Tombol Aksi Edit & Hapus -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('menus.edit', $menu->id) }}" class="text-xs text-amber-600 hover:text-amber-800 font-medium">Edit</a>
                <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-2 bg-white p-8 rounded-lg text-center text-gray-500">
        Belum ada menu yang dibuat. Klik tombol di atas untuk menambah menu dan resep HPP.
    </div>
    @endforelse
</div>
@endsection