<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami — NOTTE Coffee</title>
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
        <span class="text-[10px] uppercase tracking-widest text-gold-warm font-medium block mb-3">Filosofi & Visi Misi</span>
        <h1 class="font-fraunces text-5xl font-light text-cream mb-8 leading-tight">Mengenal NOTTE Coffee</h1>
        
        <div class="space-y-6 text-grey-text text-sm leading-relaxed border-t border-hairline pt-8">
            <p>
                <strong class="text-cream font-normal">NOTTE Coffee</strong> lahir dari hasrat menghadirkan pengalaman menikmati kopi yang tenang, personal, dan presisi. Kami percaya bahwa kopi bukan sekadar pendorong energi cepat saji, melainkan ritual yang merayakan ketenangan dan kualitas rasa.
            </p>
            <p>
                Setiap biji kopi disortir dengan standar ketat, dipadukan bersama bahan-bahan segar berkualitas tinggi, dan diseduh menggunakan rasio yang telah diperhitungkan secara cermat.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 my-12 pt-8 border-t border-hairline">
                <div>
                    <h3 class="font-fraunces text-xl text-gold-warm mb-2">Visi Utama</h3>
                    <p class="text-xs leading-relaxed">Menjadi standar baru brand kopi Indonesia yang mengedepankan kualitas sensorik, estetika editorial, dan transparansi operasional.</p>
                </div>
                <div>
                    <h3 class="font-fraunces text-xl text-gold-warm mb-2">Misi Kami</h3>
                    <p class="text-xs leading-relaxed">Menyajikan setiap porsi sajian dengan konsistensi rasa yang sempurna serta menjaga harmoni antara teknologi ERP modern dan seni racik kopi.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Minimalis -->
    <footer class="border-t border-hairline py-6 text-center text-xs text-grey-text">
        © {{ date('Y') }} NOTTE Coffee. All Rights Reserved.
    </footer>

</body>
</html>