@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Bahan Baku & HPP Base</h2>
        <p class="text-gray-600 text-sm">Daftar stok dan harga beli bahan baku per unit.</p>
    </div>
    <a href="{{ route('materials.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-4 py-2 rounded shadow-sm text-sm">
        + Tambah Bahan Baku
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-6 py-3">Nama Bahan</th>
                <th class="px-6 py-3">Satuan</th>
                <th class="px-6 py-3">Harga / Unit</th>
                <th class="px-6 py-3">Stok Saat Ini</th>
                <th class="px-6 py-3">Min. Alert</th>
                <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
            @forelse($materials as $material)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-semibold text-gray-900">{{ $material->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $material->unit }}</td>
                <td class="px-6 py-4 font-medium text-gray-900">Rp{{ number_format($material->unit_price, 2, ',', '.') }}</td>
                <td class="px-6 py-4 font-bold {{ $material->stock_quantity <= $material->min_stock_alert ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $material->stock_quantity }}
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $material->min_stock_alert }}</td>
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="{{ route('materials.edit', $material->id) }}" class="text-amber-600 hover:text-amber-800 font-medium">Edit</a>
                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus bahan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data bahan baku.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection