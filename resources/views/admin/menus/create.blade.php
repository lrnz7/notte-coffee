@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Tambah Menu Baru & Komposisi Resep</h2>
    <p class="text-gray-600 text-sm mb-6">Buat produk jual baru, upload foto, dan tentukan takaran bahan bakunya (BOM).</p>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
            <p class="text-sm font-bold text-red-800 mb-1">Gagal Menambahkan Menu:</p>
            <ul class="list-disc list-inside text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-4 mb-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Menu</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: NOTTE SUBUH" required class="w-full border border-gray-300 p-2.5 rounded text-sm focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="Contoh: Coffee / Non-Coffee" required class="w-full border border-gray-300 p-2.5 rounded text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price') }}" placeholder="25000" required class="w-full border border-gray-300 p-2.5 rounded text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Foto Produk</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded text-sm file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                <p class="text-[11px] text-gray-500 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB). Opsional.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="2" placeholder="Penjelasan singkat rasa/karakter menu..." class="w-full border border-gray-300 p-2.5 rounded text-sm">{{ old('description') }}</textarea>
            </div>
        </div>

        <hr class="my-6 border-gray-200">

        <div class="mb-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">Komposisi Bahan Baku (BOM)</h3>
            <div id="materials-container" class="space-y-3">
                <div class="flex items-center space-x-3 material-row">
                    <select name="materials[]" required class="flex-1 border border-gray-300 p-2 rounded text-sm">
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->id }}">
                                {{ $mat->name }} (Stok: {{ $mat->stock_quantity }} {{$mat->unit }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="amounts[]" placeholder="Jumlah" required class="w-32 border border-gray-300 p-2 rounded text-sm">
                    <button type="button" onclick="removeRow(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded text-xs font-bold hover:bg-red-200">Hapus</button>
                </div>
            </div>

            <button type="button" onclick="addMaterialRow()" class="mt-4 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold px-4 py-2 rounded">
                + Tambah Bahan Lain
            </button>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('menus.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-5 py-2.5 rounded">Batal</a>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-5 py-2.5 rounded shadow">Simpan Menu Baru</button>
        </div>
    </form>
</div>

<script>
    const materialsData = @json($materials);

    function addMaterialRow() {
        const container = document.getElementById('materials-container');
        let optionsHtml = '<option value="">-- Pilih Bahan Baku --</option>';
        materialsData.forEach(mat => {
            optionsHtml += `<option value="${mat.id}">${mat.name} (Stok: ${mat.stock_quantity} ${mat.unit})</option>`;
        });

        const row = document.createElement('div');
        row.className = 'flex items-center space-x-3 material-row';
        row.innerHTML = `
            <select name="materials[]" required class="flex-1 border border-gray-300 p-2 rounded text-sm">${optionsHtml}</select>
            <input type="number" step="0.01" name="amounts[]" placeholder="Jumlah" required class="w-32 border border-gray-300 p-2 rounded text-sm">
            <button type="button" onclick="removeRow(this)" class="bg-red-100 text-red-600 px-3 py-2 rounded text-xs font-bold hover:bg-red-200">Hapus</button>
        `;
        container.appendChild(row);
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.material-row');
        if (rows.length > 1) {
            btn.closest('.material-row').remove();
        } else {
            alert('Menu minimal harus memiliki 1 bahan baku.');
        }
    }
</script>
@endsection