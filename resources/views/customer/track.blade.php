<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order->invoice_number }} — NOTTE Coffee</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-notte.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800;900&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#080808] text-gray-200 antialiased min-h-screen flex flex-col justify-between p-4 md:p-10 relative">

    <!-- INDIKATOR STATUS BUAT AJAX POLLING -->
    <div id="order-status-data" data-status="{{ $order->status }}" class="hidden"></div>

    @if($order->status == 'completed')
    <!-- BRUTALIST GREEN SCREEN (Fase 3 - Selesai) -->
    <div class="fixed inset-0 z-[100] bg-green-500 flex flex-col items-center justify-center p-4">
        <h1 class="text-black font-black text-6xl md:text-8xl lg:text-[10rem] uppercase tracking-tighter text-center leading-none">
            AMBIL<br>SEKARANG
        </h1>
        <div class="mt-8 mb-12 text-center">
            <p class="text-black font-bold text-lg md:text-2xl uppercase tracking-widest">Nomor Invoice</p>
            <p class="text-black font-black text-2xl md:text-4xl bg-white px-4 py-2 mt-2 border-4 border-black">{{ explode('-', $order->invoice_number)[2] ?? $order->invoice_number }}</p>
        </div>
        <a href="{{ route('customer.menu') }}" class="border-4 border-black text-black font-black uppercase tracking-widest text-lg px-8 py-4 hover:bg-black hover:text-green-500 transition active:scale-95">
            Selesai & Tutup
        </a>
    </div>
    @endif

    <div class="max-w-5xl mx-auto w-full my-auto bg-[#121212] border border-neutral-800/80 rounded-lg shadow-sm relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            
            <!-- KOLOM KIRI: BREAKDOWN PESANAN -->
            <div class="lg:col-span-7 p-6 md:p-12 border-b lg:border-b-0 lg:border-r border-neutral-800/80 flex flex-col">
                <div class="pb-6 md:pb-8 border-b border-neutral-800/80 mb-6 md:mb-8 space-y-2">
                    <span class="text-[10px] font-mono uppercase tracking-[0.3em] text-[#c5a880] block">Official Digital Receipt</span>
                    <h1 class="font-serif-title text-2xl md:text-5xl font-medium text-gray-100 tracking-tight">{{ $order->invoice_number }}</h1>
                    <p class="text-xs text-gray-400 font-light mt-2">Atas Nama: <strong class="text-gray-200">{{ $order->customer_name }}</strong> &nbsp;|&nbsp; WA: <strong class="text-gray-200">{{ $order->customer_phone }}</strong></p>
                </div>

                <div class="flex-1 space-y-4 mb-8">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-[#c5a880] block mb-4">Item Breakdown</span>
                    @foreach($order->orderItems as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div>
                            <span class="text-gray-200 font-bold block text-sm md:text-base">{{ $item->menu->name ?? 'Menu Terhapus' }}</span>
                            <span class="text-[11px] text-gray-500 font-mono">Note: {{ $item->note }}</span>
                        </div>
                        <div class="text-right font-mono">
                            <span class="text-gray-400">x{{ $item->quantity }}</span>
                            <span class="text-gray-200 font-bold ml-3">Rp{{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
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
                    <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-widest">Total Bayar</span>
                    <span class="font-serif-title text-3xl md:text-4xl text-[#c5a880]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KOLOM KANAN: TRACKER & UPLOAD -->
            <div class="lg:col-span-5 p-6 md:p-12 bg-[#0a0a0a] flex flex-col justify-center">
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-950/40 border border-rose-800/60 rounded-md text-rose-400 text-xs font-bold text-center">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-950/40 border border-emerald-800/60 rounded-md text-emerald-400 text-xs font-bold text-center">{{ session('success') }}</div>
                @endif

                @if($order->status != 'pending_payment')
                <!-- BRUTALIST STEPPER (Ganti status teks lama) -->
                <div class="mb-8">
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 text-center">Status Pesanan</h3>
                    <div class="grid grid-cols-3 gap-1.5 md:gap-2">
                        <!-- Step 1: Dibayar (Aktif kalau status nunggu verifikasi, diproses, atau selesai) -->
                        <div class="py-2.5 text-center text-[9px] md:text-[10px] font-black uppercase tracking-wider transition-colors duration-500 {{ in_array($order->status, ['waiting_verification', 'processing', 'completed']) ? 'bg-[#c5a880] text-black' : 'bg-neutral-800 text-gray-500' }}">
                            1. Dibayar
                        </div>
                        
                        <!-- Step 2: Diracik (Aktif kalau status processing atau selesai) -->
                        <div class="py-2.5 text-center text-[9px] md:text-[10px] font-black uppercase tracking-wider transition-colors duration-500 {{ in_array($order->status, ['processing', 'completed']) ? 'bg-[#c5a880] text-black' : 'bg-neutral-800 text-gray-500' }}">
                            2. Diracik
                        </div>
                        
                        <!-- Step 3: Diambil (Aktif kalau status completed) -->
                        <div class="py-2.5 text-center text-[9px] md:text-[10px] font-black uppercase tracking-wider transition-colors duration-500 {{ $order->status == 'completed' ? 'bg-[#c5a880] text-black' : 'bg-neutral-800 text-gray-500' }}">
                            3. Diambil
                        </div>
                    </div>
                </div>
                @endif

                <!-- JIKA BELUM BAYAR: TAMPILKAN QRIS & FORM UPLOAD -->
                @if($order->status == 'pending_payment')
                <div class="mb-6 p-3 rounded bg-amber-500/10 border border-amber-500/20 text-center">
                    <span class="font-bold text-sm uppercase text-amber-500 tracking-wider">Menunggu Pembayaran</span>
                </div>

                <div class="space-y-6">
                    <div class="text-center">
                        <div class="inline-block p-4 bg-white rounded-md shadow-sm mb-2">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=NOTTE-COFFEE-PAYMENT-{{ $order->invoice_number }}" alt="QRIS NOTTE" class="w-32 h-32 md:w-40 md:h-40 mx-auto">
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-wider">Scan QRIS NOTTE Coffee</p>
                    </div>

                    <form action="{{ route('customer.order.uploadProof', $order->invoice_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <label for="payment_proof" class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-neutral-700 hover:border-[#c5a880] bg-[#121212] rounded-md cursor-pointer transition p-4 text-center group active:scale-95">
                            <svg class="w-6 h-6 text-gray-500 group-hover:text-[#c5a880] mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span id="file-label-text" class="text-[11px] font-bold text-gray-400 group-hover:text-[#c5a880] transition uppercase tracking-wider">Upload Bukti Bayar</span>
                            <span class="text-[9px] text-gray-600 mt-1">Format: JPG, PNG (Maks 5MB)</span>
                            <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required class="hidden" onchange="previewFileName(this)">
                        </label>

                        <button type="submit" class="w-full bg-[#c5a880] hover:bg-amber-600 active:bg-amber-700 text-black font-black text-xs tracking-widest uppercase py-4 rounded transition shadow-lg">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
                @endif

                <!-- JIKA SUDAH BAYAR / DI-VERIFIKASI -->
                @if($order->status != 'pending_payment')
                <div class="p-6 bg-[#121212] border border-neutral-800 rounded-md text-center space-y-4">
                    @if($order->payment_proof)
                    <div class="w-24 h-24 mx-auto rounded-lg overflow-hidden border border-neutral-700 shadow-md">
                        <img src="{{ asset('uploads/payment_proofs/' . $order->payment_proof) }}" alt="Bukti Transfer" class="w-full h-full object-cover grayscale opacity-70">
                    </div>
                    @endif
                    
                    @if($order->status == 'waiting_verification')
                        <p class="text-xs text-gray-400 font-semibold leading-relaxed">Kasir sedang memverifikasi pembayaran kamu.<br>Layar ini akan otomatis update.</p>
                    @elseif($order->status == 'processing')
                        <p class="text-xs text-amber-500 font-bold leading-relaxed">Dapur sedang meracik pesanan kamu.<br><span class="text-gray-400 font-normal">Tunggu sampai layar berubah jadi hijau.</span></p>
                    @endif
                </div>
                @endif
                
                <a href="{{ route('customer.menu') }}" class="mt-auto pt-8 text-center text-[10px] text-gray-500 hover:text-[#c5a880] uppercase tracking-widest transition font-bold block">
                    ← Kembali ke Menu
                </a>
            </div>

        </div>
    </div>

    <script>
        // Preview file upload
        function previewFileName(input) {
            const label = document.getElementById('file-label-text');
            if (input.files && input.files[0]) {
                label.innerText = '✓ ' + input.files[0].name;
                label.classList.add('text-[#c5a880]', 'font-bold');
            }
        }
        
        // Simpan active_invoice ke localStorage
        localStorage.setItem('active_invoice', '{{ $order->invoice_number }}');
        localStorage.setItem('status_{{ $order->invoice_number }}', '{{ $order->status }}');

        // AJAX INVISIBLE POLLING (Cek status setiap 3 detik tanpa refresh layar)
        const currentStatus = document.getElementById('order-status-data').getAttribute('data-status');
        
        if (['waiting_verification', 'processing'].includes(currentStatus)) {
            setInterval(() => {
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newStatus = doc.getElementById('order-status-data').getAttribute('data-status');
                    
                    // Kalau kasir udah klik Selesai / Terima di ERP
                    if (newStatus !== currentStatus) {
                        // Tembak layar baru
                        document.body.innerHTML = doc.body.innerHTML;
                        
                        // Kalau statusnya selesai, hajar HP pelanggan pakai Vibration API
                        if (newStatus === 'completed') {
                            if ("vibrate" in navigator) {
                                // Pola getar: Getar panjang, jeda, getar cepat 3x
                                navigator.vibrate([500, 200, 200, 100, 200, 100, 200]);
                            }
                        }
                        
                        // Eksekusi ulang script kalau status belum completed biar polling tetap jalan
                        if (newStatus !== 'completed') {
                            eval(doc.querySelector('script').innerHTML);
                        }
                    }
                })
                .catch(err => console.error("Polling error", err));
            }, 3000);
        }
    </script>
</body>
</html>