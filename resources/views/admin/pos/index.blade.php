@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Point of Sale (POS) - Kasir Toko</h2>
    <p class="text-gray-600 text-sm">Pilih menu pesanan pembeli walk-in untuk memotong stok & mencatat omzet.</p>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('pos.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Katalog Menu Kasir -->
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($menus as $menu)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col justify-between hover:border-amber-500 transition">
                <div>
                    <span class="text-xs font-semibold px-2 py-0.5 bg-amber-100 text-amber-800 rounded">
                        {{ $menu->category }}
                    </span>
                    <h4 class="font-bold text-gray-900 mt-2">{{ $menu->name }}</h4>
                    <p class="text-amber-600 font-extrabold mt-1">Rp{{ number_format($menu->selling_price, 0, ',', '.') }}</p>
                </div>
                <button type="button" onclick="addToCart({{ $menu->id }}, '{{ $menu->name }}', {{$menu->selling_price }})" 
                    class="mt-4 w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2 rounded">
                    + Tambah Item
                </button>
            </div>
            @endforeach
        </div>

        <!-- Ringkasan Keranjang Kasir -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col justify-between h-fit">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Detail Transaksi</h3>
                
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700">Nama Pelanggan Walk-In</label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Kak Budi" class="w-full mt-1 p-2 text-sm border rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full mt-1 p-2 text-sm border rounded-md">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS / E-Wallet</option>
                    </select>
                </div>

                <div id="cart-items" class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada item yang dipilih.</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-gray-700">Total Tagihan:</span>
                    <span id="grand-total" class="text-xl font-extrabold text-amber-600">Rp0</span>
                </div>
                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-lg shadow transition">
                    Proses Transaksi & Potong Stok
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].quantity += 1;
        } else {
            cart[id] = { name: name, price: price, quantity: 1 };
        }
        renderCart();
    }

    function changeQty(id, delta) {
        if (cart[id]) {
            cart[id].quantity += delta;
            if (cart[id].quantity <= 0) {
                delete cart[id];
            }
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        let html = '';
        let total = 0;

        if (Object.keys(cart).length === 0) {
            container.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">Belum ada item yang dipilih.</p>';
            document.getElementById('grand-total').innerText = 'Rp0';
            return;
        }

        for (let id in cart) {
            let item = cart[id];
            let subtotal = item.price * item.quantity;
            total += subtotal;

            html += `
                <div class="flex justify-between items-center text-sm border-b pb-2">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">${item.name}</p>
                        <p class="text-xs text-gray-500">Rp${item.price.toLocaleString('id-ID')} x ${item.quantity}</p>
                        <input type="hidden" name="cart[${id}][quantity]" value="${item.quantity}">
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="changeQty(${id}, -1)" class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">-</button>
                        <span class="font-bold text-xs">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${id}, 1)" class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs font-bold">+</button>
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;
        document.getElementById('grand-total').innerText = 'Rp' + total.toLocaleString('id-ID');
    }
</script>
@endsection