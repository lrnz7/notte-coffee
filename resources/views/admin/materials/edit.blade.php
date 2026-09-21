@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Bahan Baku</h2>
    <form action="{{ route('materials.update', $material->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Bahan</label>
            <input type="text" name="name" value="{{ $material->name }}" required class="w-full mt-1 p-2 border rounded-md">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Satuan (Unit)</label>
                <input type="text" name="unit" value="{{ $material->unit }}" required class="w-full mt-1 p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Harga per Unit (Rp)</label>
                <input type="number" step="0.01" name="unit_price" value="{{ $material->unit_price }}" required class="w-full mt-1 p-2 border rounded-md">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Jumlah Stok</label>
                <input type="number" step="0.01" name="stock_quantity" value="{{ $material->stock_quantity }}" required class="w-full mt-1 p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Batas Minimal Alert</label>
                <input type="number" step="0.01" name="min_stock_alert" value="{{ $material->min_stock_alert }}" required class="w-full mt-1 p-2 border rounded-md">
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('materials.index') }}" class="px-4 py-2 border rounded-md text-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md">Update Bahan</button>
        </div>
    </form>
</div>
@endsection