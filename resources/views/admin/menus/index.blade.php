@extends('layouts.admin')

@section('content')
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Katalog Menu & Recipe Costing (HPP)</h2>
        <p class="text-gray-600 text-xs md:text-sm">Daftar produk jual beserta kalkulasi HPP otomatis dari bahan baku.</p>
    </div>
    <a href="{{ route('menus.create') }}" class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold px-4 py-2.5 md:py-2 rounded text-center shadow-sm text-xs md:text-sm uppercase tracking-wide transition w-full md:w-auto">
        + Buat Menu & Resep Baru
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    @forelse($menus as $menu)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-start mb-2">
                <div>
                    <span class="text-[10px] md:text-xs font-bold uppercase px-2 py-1 bg-amber-100 text-amber-800 rounded tracking-wider">
                        {{ $menu->category }}
                    </span>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mt-2 leading-tight">{{ $menu->name }}</h3>
                </div>
                <p class="text-base md:text-lg font-black text-amber-600">
                    Rp{{ number_format($menu->selling_price, 0, ',', '.') }}
                </p>
            </div>
            <p class="text-gray-500 text-xs md:text-sm mb-4 line-clamp-2">{{ $menu->description ?? 'Tidak ada deskripsi.' }}</p>

            <!-- Table Resep / BOM -->
            <div class="bg-gray-50 p-3 rounded-md border border-gray-100 mb-4">
                <p class="text-[10px] md:text-xs font-bold text-gray-500 uppercase mb-2 tracking-widest border-b border-gray-200 pb-1">Komposisi Resep (BOM):</p>
                <ul class="space-y-1.5 text-[11px] md:text-xs text-gray-700">
                    @forelse($menu->recipes as $recipe)
                    <li class="flex justify-between items-center py-0.5">
                        <span class="truncate pr-2">
                            <span class="text-gray-400 mr-1">•</span>
                            <span class="font-semibold">{{ $recipe->ingredient->name ?? 'Bahan Tidak Ditemukan' }}</span> 
                            <span class="text-gray-500">({{ $recipe->quantity }} {{ $recipe->ingredient->unit ?? 'unit' }})</span>
                        </span>
                        <span class="font-bold text-gray-800 shrink-0">
                            Rp{{ number_format($recipe->quantity * ($recipe->ingredient->unit_price ?? 0), 0, ',', '.') }}
                        </span>
                    </li>
                    @empty
                    <li class="text-gray-400 italic py-1 font-semibold">Belum ada resep bahan baku.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-3 flex justify-between items-center mt-auto">
            <div>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Estimasi HPP</span>
                <p class="font-black text-slate-800 text-sm">Rp{{ number_format($menu->calculated_hpp, 0, ',', '.') }}</p>
            </div>
            <div>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Laba Bersih/Cup</span>
                <p class="font-black text-emerald-600 text-sm">
                    Rp{{ number_format($menu->selling_price - $menu->calculated_hpp, 0, ',', '.') }}
                </p>
            </div>
            
            <!-- Tombol Aksi Edit & Hapus -->
            <div class="flex items-center space-x-2 border-l border-gray-100 pl-3">
                <a href="{{ route('menus.edit', $menu->id) }}" class="text-[10px] md:text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2 py-1.5 rounded uppercase transition">Edit</a>
                <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[10px] md:text-xs bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2 py-1.5 rounded uppercase transition">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white p-8 rounded-lg border border-gray-200 text-center text-gray-500 shadow-sm">
        <p class="text-sm font-semibold">Belum ada menu yang dibuat. Klik tombol di atas untuk menambah menu dan resep HPP.</p>
    </div>
    @endforelse
</div>
@endsection