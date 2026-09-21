<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan — NOTTE Coffee</title>
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
        <span class="text-[10px] uppercase tracking-widest text-gold-warm font-medium block mb-3">Legal & Privasi</span>
        <h1 class="font-fraunces text-5xl font-light text-cream mb-8 leading-tight">Syarat & Kebijakan</h1>

        <div class="space-y-6 text-grey-text text-sm leading-relaxed border-t border-hairline pt-8">
            <section>
                <h3 class="font-fraunces text-lg text-cream mb-2">1. Ketentuan Pemesanan</h3>
                <p class="text-xs">Setiap pesanan online yang telah dikonfirmasi akan langsung diteruskan ke sistem dapur/barista kami. Pembatalan hanya dapat dilakukan sebelum status pesanan diubah menjadi "Diproses".</p>
            </section>

            <section>
                <h3 class="font-fraunces text-lg text-cream mb-2">2. Kebijakan Privasi Data</h3>
                <p class="text-xs">Data pribadi seperti Nama, Nomor WhatsApp, dan Alamat Pengiriman hanya digunakan untuk kepentingan pengiriman pesanan dan verifikasi transaksi NOTTE Coffee. Kami menjamin data Anda tidak akan diperjualbelikan kepada pihak ketiga.</p>
            </section>

            <section>
                <h3 class="font-fraunces text-lg text-cream mb-2">3. Kualitas Produk & Pengembalian</h3>
                <p class="text-xs">Kami menjamin seluruh sajian dibuat menggunakan bahan baku segar sesuai resep terstandarisasi. Apabila terdapat keliru pesanan, harap menghubungi customer support kami melalui WhatsApp dengan menyertakan bukti foto dan invoice.</p>
            </section>
        </div>
    </main>

    <!-- Footer Minimalis -->
    <footer class="border-t border-hairline py-6 text-center text-xs text-grey-text">
        © {{ date('Y') }} NOTTE Coffee. All Rights Reserved.
    </footer>

</body>
</html>