<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ & Bantuan — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { black: { deep: '#0A0A0A', rich: '#121212' }, cream: '#F2EDE3', gold: { warm: '#C9A468' }, grey: { text: '#B5B0A8' }, hairline: '#2A2A2A' },
                    fontFamily: { fraunces: ['Fraunces', 'serif'], inter: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-black-deep text-cream font-inter min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <nav class="border-b border-hairline px-8 py-6 flex justify-between items-center">
        <a href="{{ route('home') }}" class="flex flex-col items-center">
            <span class="font-fraunces text-2xl tracking-widest text-cream">NOTTE</span>
            <span class="text-[10px] tracking-wide text-gold-warm uppercase mt-0.5">C o f f e e</span>
        </a>
        <a href="{{ route('home') }}" class="text-xs uppercase tracking-wide text-grey-text hover:text-cream">← Beranda</a>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16 max-w-3xl">
        <span class="text-[10px] uppercase tracking-widest text-gold-warm font-medium block mb-3">Pusat Bantuan</span>
        <h1 class="font-fraunces text-5xl font-light text-cream mb-8 leading-tight">Pertanyaan Umum (FAQ)</h1>

        <div class="space-y-6 border-t border-hairline pt-8">
            <div class="border-b border-hairline pb-6">
                <h3 class="font-fraunces text-lg text-cream mb-2">Bagaimana cara melakukan pemesanan online?</h3>
                <p class="text-xs text-grey-text leading-relaxed">Anda dapat memilih menu melalui katalog online kami, menentukan opsi pengiriman (Delivery/Pickup), mengisikan informasi pemesan, lalu melakukan konfirmasi checkout.</p>
            </div>

            <div class="border-b border-hairline pb-6">
                <h3 class="font-fraunces text-lg text-cream mb-2">Metode pembayaran apa saja yang tersedia?</h3>
                <p class="text-xs text-grey-text leading-relaxed">Saat ini kami mendukung pembayaran secara instan menggunakan QRIS dan Transfer Bank untuk transaksi online, serta Tunai (Cash) untuk transaksi offline di toko.</p>
            </div>

            <div class="border-b border-hairline pb-6">
                <h3 class="font-fraunces text-lg text-cream mb-2">Bagaimana cara melacak status pesanan saya?</h3>
                <p class="text-xs text-grey-text leading-relaxed">Setelah checkout berhasil, Anda akan mendapatkan Nomor Invoice unik (contoh: INV-ONLINE-xxx). Gunakan nomor tersebut pada halaman lacak pesanan untuk melihat pembaruan status terkini.</p>
            </div>
        </div>

        <!-- Section Kontak -->
        <div class="mt-12 p-8 bg-black-rich border border-hairline rounded">
            <h3 class="font-fraunces text-xl text-gold-warm mb-4">Hubungi Kami</h3>
            <p class="text-xs text-grey-text mb-2">Ada pertanyaan lebih lanjut atau butuh bantuan pesan antar?</p>
            <p class="text-xs text-cream font-semibold">WhatsApp: +62 812-3456-7890 | Email: support@nottecoffee.com</p>
        </div>
    </main>

    <!-- Footer Minimalis -->
    <footer class="border-t border-hairline py-6 text-center text-xs text-grey-text">
        © {{ date('Y') }} NOTTE Coffee. All Rights Reserved.
    </footer>

</body>
</html>