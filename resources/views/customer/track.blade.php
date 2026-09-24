<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->invoice_number }} — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#080808] text-gray-200 antialiased min-h-screen flex flex-col justify-between p-4 md:p-10 relative">

    <!-- DATA STATUS UNTUK LIVE POLLING JS -->
    <div id="order-status-data" data-status="{{ $order->status }}" class="hidden"></div>

    <div class="max-w-5xl mx-auto w-full my-auto bg-[#121212] border border-neutral-800/80 rounded-3xl shadow-2xl relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            <!-- KOLOM KIRI: BREAKDOWN PESANAN -->
            <div class="lg:col-span-7 p-8 md:p-12 border-b lg:border-b-0 lg:border-r border-neutral-800/80 flex flex-col">
                <div class="pb-8 border-b border-neutral-800/80 mb-8 space-y-2">
                    <span class="text-[10px] font-mono uppercase tracking-[0.3em] text-[#c5a880] block">Official Digital Receipt</span>
                    <h1 class="font-serif-title text-3xl md:text-5xl font-medium text-gray-100 tracking-tight">{{ $order->invoice_number }}</h1>
                    <p class="text-xs text-gray-400 font-light mt-2">Atas Nama: <strong class="text-gray-200">{{ $order->customer_name }}</strong> &nbsp;|&nbsp; WA: <strong class="text-gray-200">{{ $order->customer_phone }}</strong></p>
                </div>

                <div class="flex-1 space-y-4 mb-8">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-[#c5a880] block mb-4">Item Breakdown</span>
                    @foreach($order->orderItems as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div>
                            <span class="text-gray-200 font-medium block text-base">{{ $item->menu->name ?? 'Menu Terhapus' }}</span>
                            <span class="text-[11px] text-gray-500 font-mono">Note: {{ $item->note }}</span>
                        </div>
                        <div class="text-right font-mono">
                            <span class="text-gray-400">x{{ $item->quantity }}</span>
                            <span class="text-gray-200 ml-3">Rp{{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach

                    @if($order->discount_amount > 0)
                    <div class="flex justify-between items-center text-xs pt-4 border-t border-neutral-800/60 text-emerald-400 font-mono">
                        <span>Diskon 50% Pengguna Baru</span>
                        <span>-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="pt-6 border-t border-neutral-800/80 flex justify-between items-end">
                    <span class="text-xs text-gray-400 uppercase tracking-widest">Total Pembayaran</span>
                    <span class="font-serif-title text-3xl md:text-4xl text-[#c5a880]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KOLOM KANAN: QRIS & UPLOAD BUKTI -->
            <div class="lg:col-span-5 p-8 md:p-12 bg-[#0a0a0a] flex flex-col justify-center">
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-950/40 border border-rose-800/60 rounded-xl text-rose-400 text-xs text-center">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800/60 rounded-xl text-emerald-400 text-xs text-center">{{ session('success') }}</div>
                @endif

                <div class="mb-8 p-4 rounded-xl border border-neutral-800 bg-[#121212] flex flex-col gap-2 items-center text-center">
                    <span class="text-[10px] text-gray-500 uppercase tracking-widest block">Status Saat Ini</span>
                    <span class="font-mono font-bold text-sm uppercase text-[#c5a880] tracking-wider text-center">
                        @if($order->status == 'pending_payment') Menunggu Pembayaran
                        @elseif($order->status == 'waiting_verification') Menunggu Verifikasi Kasir
                        @elseif($order->status == 'processing') Sedang Diracik Dapur
                        @elseif($order->status == 'completed') Pesanan Selesai
                        @elseif($order->status == 'cancelled') Dibatalkan
                        @else {{ $order->status }}
                        @endif
                    </span>
                </div>

                <!-- JIKA BELUM BAYAR: TAMPILKAN QRIS & FORM UPLOAD -->
                @if($order->status == 'pending_payment')
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="inline-block p-4 bg-white rounded-2xl shadow-2xl mb-2">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=NOTTE-COFFEE-PAYMENT-{{ $order->invoice_number }}" alt="QRIS NOTTE" class="w-40 h-40 mx-auto">
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Scan QRIS Resmi NOTTE Coffee</p>
                    </div>

                    <form action="{{ route('customer.order.uploadProof', $order->invoice_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <label for="payment_proof" class="flex flex-col items-center justify-center w-full h-32 border border-dashed border-neutral-700 hover:border-[#c5a880] bg-[#121212] rounded-xl cursor-pointer transition p-4 text-center group">
                            <svg class="w-6 h-6 text-gray-500 group-hover:text-[#c5a880] mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span id="file-label-text" class="text-xs text-gray-400 group-hover:text-[#c5a880] transition">Klik untuk unggah bukti bayar</span>
                            <span class="text-[10px] text-gray-600 mt-1">Format: JPG, PNG (Maks 5MB)</span>
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required class="hidden" onchange="previewFileName(this)">
                        </label>

                        <button type="submit" class="w-full bg-[#c5a880] hover:bg-amber-600 text-black font-extrabold text-xs tracking-widest uppercase py-4 rounded-full transition shadow-lg">
                            Kirim Bukti Pembayaran →
                        </button>
                    </form>
                </div>
                @endif

                <!-- JIKA SUDAH BAYAR / DI-VERIFIKASI -->
                @if($order->status != 'pending_payment')
                <div class="p-6 bg-[#121212] border border-neutral-800 rounded-xl text-center space-y-4">
                    @if($order->payment_proof)
                    <div class="w-32 h-32 mx-auto rounded-lg overflow-hidden border border-neutral-700 shadow-lg">
                        <img src="{{ asset('uploads/payment_proofs/' . $order->payment_proof) }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                    </div>
                    @endif
                    
                    @if($order->status == 'waiting_verification')
                        <p class="text-xs text-gray-400 font-light leading-relaxed">Kasir sedang mengonfirmasi pesanan kamu.<br>Halaman ini akan otomatis diperbarui.</p>
                    @elseif($order->status == 'processing')
                        <p class="text-xs text-[#c5a880] font-bold leading-relaxed">Pesanan kamu sedang diracik oleh Barista kami. Mohon ditunggu!</p>
                    @elseif($order->status == 'completed')
                        <p class="text-sm text-emerald-400 font-bold leading-relaxed">Pesanan Selesai!<br><span class="text-xs text-gray-400 font-normal">Silakan ambil pesanan lu di counter.</span></p>
                    @endif
                </div>
                @endif
                
                <a href="{{ route('customer.menu') }}" class="mt-auto pt-6 text-center text-[10px] text-gray-500 hover:text-[#c5a880] uppercase tracking-widest transition font-bold block">
                    ← Kembali ke Katalog Menu
                </a>
            </div>

        </div>
    </div>

    <script>
        // Paksa bersihkan item tracker yang ter-close sebelumnya untuk invoice aktif ini
        localStorage.removeItem('closed_tracker_{{ $order->invoice_number }}');

        function previewFileName(input) {
            const label = document.getElementById('file-label-text');
            if (input.files && input.files[0]) {
                label.innerText = '✓ File terpilih: ' + input.files[0].name;
                label.classList.add('text-[#c5a880]', 'font-bold');
            }
        }
        
        @if(in_array($order->status, ['waiting_verification', 'processing']))
        setTimeout(function(){
            window.location.reload(1);
        }, 10000);
        @endif
    </script>
</body>
</html>