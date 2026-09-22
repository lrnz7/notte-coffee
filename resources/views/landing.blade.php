<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTTE Coffee — Midnight romance. Everyday coffee.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-notte-black { background-color: #080808; }
        .bg-notte-card { background-color: #121212; }
        .border-notte-gold { border-color: #c5a880; }
        .text-notte-gold { color: #c5a880; }
        .bg-notte-gold { background-color: #c5a880; }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-notte-black text-gray-200 antialiased selection:bg-amber-900 selection:text-amber-100">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-50 bg-notte-black/90 backdrop-blur-md border-b border-neutral-800/60">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="flex items-center">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-8 md:h-10 w-auto object-contain">
            </a>
            <div class="hidden md:flex items-center gap-8 text-[11px] font-bold tracking-widest text-gray-400 uppercase">
                <a href="#philosophy" class="hover:text-notte-gold transition">Our Philosophy</a>
                <a href="#crafted" class="hover:text-notte-gold transition">The Craft</a>
                <a href="#visit" class="hover:text-notte-gold transition">Visit Us</a>
            </div>
            <a href="{{ route('customer.menu') }}" class="bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest">
                Jelajahi Menu
            </a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative min-h-screen flex items-center justify-center pt-20 px-6 overflow-hidden reveal active">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#c5a880]/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10 flex flex-col items-center space-y-8">
            <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo Large" class="h-32 md:h-40 w-auto object-contain mb-4 drop-shadow-2xl">
            <h1 class="font-serif-title text-4xl md:text-6xl font-medium text-gray-100 leading-tight">
                The Art of Fine Coffee,<br>Right in Your Neighborhood.
            </h1>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-6 w-full">
                <a href="{{ route('customer.menu') }}" class="w-full sm:w-auto bg-notte-gold hover:bg-amber-600 text-black font-bold text-xs px-10 py-4 rounded-full transition uppercase tracking-widest">
                    Jelajahi Menu →
                </a>
                <a href="https://wa.me/6285122121322" target="_blank" class="w-full sm:w-auto border border-neutral-700 hover:border-notte-gold text-gray-300 hover:text-notte-gold font-bold text-xs px-10 py-4 rounded-full transition uppercase tracking-widest bg-notte-card">
                    Pesan via WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- OUR PHILOSOPHY -->
    <section id="philosophy" class="py-24 bg-notte-card border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold border-b border-notte-gold pb-1 block w-max">Our Philosophy</span>
                <h2 class="font-serif-title text-3xl md:text-4xl font-bold text-gray-100 italic border-l-2 border-notte-gold pl-6">
                    "Notte means night. Not because coffee belongs to the night, but because some of life's best conversations do."
                </h2>
                <div class="space-y-5 text-gray-400 text-sm leading-relaxed font-light">
                    <p>NOTTE was born from the idea that every cup should bring more than caffeine. It should bring comfort, presence, and a moment to pause.</p>
                    <p>Whether you're starting your morning, taking a break in the afternoon, or ending a long day, we hope every sip feels intentional.</p>
                </div>
            </div>
            <div class="relative w-full h-[500px] rounded-2xl overflow-hidden border border-neutral-800 group bg-neutral-900">
                <!-- LINK GAMBAR BARU YG DIJAMIN AKTIF -->
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=800&q=80" 
                     alt="Notte Coffee Philosophy" 
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-1000 opacity-90">
                <div class="absolute inset-0 bg-gradient-to-t from-notte-black/80 via-transparent to-transparent"></div>
            </div>
        </div>
    </section>

    <!-- THE CRAFT -->
    <section id="crafted" class="py-24 bg-notte-black border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            
            <div class="order-2 md:order-1 relative w-full h-[550px] rounded-2xl overflow-hidden border border-neutral-800 group">
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=800&auto=format&fit=crop" alt="Signature Coffee" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-notte-black via-transparent to-transparent opacity-90"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <span class="text-[10px] font-mono tracking-widest text-notte-gold uppercase">Signature</span>
                    <h3 class="font-serif-title text-2xl text-white mt-1">Butterscotch Sea Salt</h3>
                </div>
            </div>

            <div class="order-1 md:order-2 space-y-6">
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold block">The Craft</span>
                <h2 class="font-serif-title text-4xl font-bold text-gray-100 leading-tight">
                    Crafted for the moment that matters.
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed font-light">
                    Setiap menu diracik dengan presisi. Varian signature kami merepresentasikan harmoni antara waktu, kontrol, dan karakter rasa yang kuat. Smooth. Bold. Honest.
                </p>
                <a href="{{ route('customer.menu') }}" class="inline-block mt-4 text-xs font-bold text-notte-gold uppercase tracking-widest border-b border-notte-gold pb-1 hover:text-white transition">
                    Jelajahi Menu Pilihan →
                </a>
            </div>
        </div>
    </section>

    <!-- FEATURES (Premium UI/UX Hover) -->
    <section class="py-20 bg-notte-card border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-black transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] cursor-default">
                <span class="font-serif-title text-notte-gold text-5xl block mb-4 italic opacity-60 group-hover:opacity-100 transition duration-500">01.</span>
                <h3 class="font-serif-title text-xl text-gray-100 mb-3">Vesper Orbit Extraction</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Ekstraksi kopi dengan tingkat presisi tinggi. Menghasilkan rasa yang bold, namun tetap smooth di tenggorokan.</p>
            </div>
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-black transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] cursor-default">
                <span class="font-serif-title text-notte-gold text-5xl block mb-4 italic opacity-60 group-hover:opacity-100 transition duration-500">02.</span>
                <h3 class="font-serif-title text-xl text-gray-100 mb-3">Honest Price</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Kualitas secangkir fine coffee sejati, disajikan tanpa harga yang bikin kantong lu jebol. #onestpricecoffeeh</p>
            </div>
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-black transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] cursor-default">
                <span class="font-serif-title text-notte-gold text-5xl block mb-4 italic opacity-60 group-hover:opacity-100 transition duration-500">03.</span>
                <h3 class="font-serif-title text-xl text-gray-100 mb-3">Midnight Atmosphere</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Suasana dan estetika tempat yang dirancang khusus untuk memberi kenyamanan buat obrolan panjang lu.</p>
            </div>
        </div>
    </section>

    <!-- QUOTE BANNER -->
    <section class="py-24 bg-notte-black border-y border-neutral-800/80 reveal">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <span class="font-mono text-[10px] font-bold tracking-[0.3em] text-gray-500 uppercase block mb-6">#onestpricecoffeeh</span>
            <h2 class="font-serif-title text-3xl md:text-5xl font-medium text-notte-gold italic leading-snug">
                "Midnight romance. Everyday coffee."
            </h2>
        </div>
    </section>

    <!-- VISIT US & FOOTER -->
    <section id="visit" class="pt-24 pb-12 bg-notte-card reveal">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-16">
                <div class="space-y-8">
                    <div>
                        <h3 class="font-serif-title text-3xl font-bold text-gray-100 mb-4">Visit Us</h3>
                        <p class="text-gray-400 text-sm leading-relaxed font-light">
                            <strong>NOTTE Coffee Jatimurni</strong><br>
                            Jl. Bhinneka No.33a, RT.005/RW.003,<br>
                            Jatimurni, Kec. Pd. Melati, Kota Bekasi,<br>
                            Jawa Barat 17431
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <a href="https://www.instagram.com/nottehouse.id?stkn=MXU0bGNiZW1pbW95MQ==" target="_blank" class="px-6 py-3 border border-neutral-700 rounded-lg text-xs font-bold text-gray-300 hover:border-notte-gold hover:text-notte-gold transition">
                            📷 @nottehouse.id
                        </a>
                        <a href="https://wa.me/6285122121322" target="_blank" class="px-6 py-3 border border-neutral-700 rounded-lg text-xs font-bold text-gray-300 hover:border-notte-gold hover:text-notte-gold transition">
                            💬 WhatsApp
                        </a>
                    </div>
                </div>
                <div class="w-full h-[300px] rounded-2xl overflow-hidden border border-neutral-800">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.7483756402287!2d106.9272304!3d-6.3262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTknMzQuMyJTIDEwNsKwNTUnMzguMCJF!5e0!3m2!1sen!2sid!4v1690000000000!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="border-t border-neutral-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] text-gray-500 font-mono">
                <p>&copy; {{ date('Y') }} NOTTE Coffee. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="{{ route('customer.menu') }}" class="hover:text-notte-gold transition">Order Online</a>
                    <a href="{{ route('login') }}" class="hover:text-notte-gold transition">Staff Portal</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 100;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", reveal);
        reveal();
    </script>
</body>
</html>