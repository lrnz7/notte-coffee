@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Menu & Formulasi Resep (HPP)</h2>

    <form action="{{ route('menus.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Info Produk -->
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nama Menu</label>
                <input type="text" name="name" required placeholder="Contoh: Caramel Macchiato" class="w-full mt-1 p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <input type="text" name="category" required placeholder="Kopi / Non-Kopi / Snack" class="w-full mt-1 p-2 border rounded-md">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                <input type="number" name="selling_price" required placeholder="25000" class="w-full mt-1 p-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                <input type="text" name="description" placeholder="Es kopi dengan sirup karamel..." class="w-full mt-1 p-2 border rounded-md">
            </div>
        </div>

        <hr class="my-6">

        <!-- Formulasi Resep -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Formulasi Bahan Baku (Bill of Materials)</h3>
            <p class="text-xs text-gray-500 mb-4">Pilih bahan baku dan takaran yang dibutuhkan untuk membuat 1 cup/porsi menu ini.</p>

            <div id="recipe-rows" class="space-y-3">
                <div class="flex items-center space-x-3">
                    <select name="materials[]" required class="flex-1 p-2 border rounded-md">
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->name }} (stok: {{ $mat->unit }})</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="amounts[]" required placeholder="Takaran (misal: 18)" class="w-32 p-2 border rounded-md">
                </div>
            </div>

            <button type="button" onclick="addRecipeRow()" class="mt-3 text-sm text-amber-600 font-semibold hover:text-amber-800">
                + Tambah Bahan Baku Lain
            </button>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('menus.index') }}" class="px-4 py-2 border rounded-md text-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md">Simpan Menu & Resep</button>
        </div>
    </form>
</div>

<script>
    function addRecipeRow() {
        const container = document.getElementById('recipe-rows');
        const firstRow = container.children[0].cloneNode(true);
        firstRow.querySelector('select').value = '';
        firstRow.querySelector('input').value = '';
        container.appendChild(firstRow);
    }
</script>
@endsection