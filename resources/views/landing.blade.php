<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTTE Coffee — Good Coffee. Fair Price.</title>
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

    <!-- PROMO BANNER TOP -->
    @guest
    <div class="bg-notte-gold text-black text-center py-2 text-[10px] md:text-xs font-bold uppercase tracking-widest relative z-[60]">
        Daftar akun sekarang & dapatkan DISKON 50% untuk pesanan pertama! 
        <a href="{{ route('login') }}" class="underline ml-2 hover:text-white transition">Daftar Disini</a>
    </div>
    @endguest

    <!-- NAVBAR -->
    <nav class="sticky top-0 w-full z-50 bg-notte-black/90 backdrop-blur-md border-b border-neutral-800/60">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="flex items-center">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-8 md:h-10 w-auto object-contain">
            </a>
            <div class="hidden md:flex items-center gap-8 text-[11px] font-bold tracking-widest text-gray-400 uppercase">
                <a href="#philosophy" class="hover:text-notte-gold transition">Our Story</a>
                <a href="#crafted" class="hover:text-notte-gold transition">The Coffee</a>
                <a href="#literan" class="hover:text-notte-gold transition">NOTTE 1L</a>
                <a href="#visit" class="hover:text-notte-gold transition">Visit Us</a>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('customer.menu') }}" class="hidden md:block bg-notte-card border border-notte-gold hover:bg-notte-gold text-notte-gold hover:text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest">
                    Order Menu
                </a>
                @auth
                    @if(Auth::user()->role === 'customer')
                        <a href="{{ route('customer.account') }}" class="bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest">
                            Akun Saya
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="bg-rose-900/80 text-rose-200 border border-rose-700 font-bold text-[11px] px-4 py-2 rounded-full transition uppercase tracking-widest">
                            🛡 Panel ERP
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-[11px] px-6 py-2.5 rounded-full transition uppercase tracking-widest flex items-center gap-2">
                        <span>Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative min-h-[85vh] flex items-center justify-center pt-16 px-6 overflow-hidden reveal active">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#c5a880]/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10 flex flex-col items-center space-y-6">
            <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo Large" class="h-28 md:h-36 w-auto object-contain mb-2 drop-shadow-2xl">
            
            <span class="text-[11px] font-mono tracking-[0.3em] uppercase text-notte-gold font-bold">Good Coffee. Fair Price.</span>
            
            <h1 class="font-serif-title text-4xl md:text-6xl font-medium text-gray-100 leading-tight">
                For late nights, slow mornings,<br>and everything in between.
            </h1>
            
            <p class="text-xs md:text-sm text-gray-400 font-light max-w-xl leading-relaxed">
                Kopi rumahan yang dibuat dengan perhatian pada kualitas biji kopi, resep, dan rasa. Menjaganya tetap berada di harga yang masuk akal untuk dinikmati setiap hari.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 w-full">
                <a href="{{ route('customer.menu') }}" class="w-full sm:w-auto bg-notte-gold hover:bg-amber-600 text-black font-bold text-xs px-10 py-4 rounded-full transition uppercase tracking-widest">
                    Lihat Menu & Order →
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
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold border-b border-notte-gold pb-1 block w-max">Meet NOTTE</span>
                <h2 class="font-serif-title text-3xl md:text-4xl font-bold text-gray-100 border-l-2 border-notte-gold pl-6 leading-snug">
                    "Coffee made at home,<br>made with care."
                </h2>
                <div class="space-y-5 text-gray-400 text-sm leading-relaxed font-light">
                    <p>NOTTE lahir dari satu ide sederhana: <strong>kopi yang enak itu untuk dinikmati, bukan diperjualbelikan dengan harga berlebihan.</strong></p>
                    <p>Kami meracik kopi layaknya menyeduh di rumah sendiri — penuh perhatian pada biji kopi pilihan, racikan resep yang pas, dan konsistensi rasa. Baik untuk memulai pagi yang tenang, teman lembur larut malam, atau sekadar penutup hari.</p>
                    <p class="text-notte-gold font-mono text-xs italic">NOTTE — Good Coffee. Fair Price.</p>
                </div>
            </div>
            <div class="relative w-full h-[450px] rounded-2xl overflow-hidden border border-neutral-800 group bg-neutral-900">
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=800&q=80" 
                     alt="Notte Coffee Philosophy" 
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-1000 opacity-80">
                <div class="absolute inset-0 bg-gradient-to-t from-notte-black via-transparent to-transparent opacity-80"></div>
            </div>
        </div>
    </section>

    <!-- THE CRAFT / SIGNATURE HIGHLIGHT -->
    <section id="crafted" class="py-24 bg-notte-black border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            
            <div class="order-2 md:order-1 relative w-full h-[500px] rounded-2xl overflow-hidden border border-neutral-800 group">
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=800&auto=format&fit=crop" alt="Signature Coffee" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-notte-black via-transparent to-transparent opacity-90"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <span class="text-[10px] font-mono tracking-widest text-notte-gold uppercase block mb-1">Signature Favorite</span>
                    <h3 class="font-serif-title text-2xl text-white">Butterscotch Sea Salt</h3>
                    <p class="text-xs text-gray-400 font-light mt-1">Sweet, buttery, creamy, with a little touch of salt.</p>
                </div>
            </div>

            <div class="order-1 md:order-2 space-y-6">
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-notte-gold block">Everyday Selection</span>
                <h2 class="font-serif-title text-3xl md:text-4xl font-bold text-gray-100 leading-tight">
                    Rasa jujur, racikan sederhana.
                </h2>
                <p class="text-gray-400 text-sm leading-relaxed font-light">
                    Gak perlu istilah ribet untuk menikmati kopi yang enak. Kami meracik setiap cangkir dengan kombinasi rasa yang seimbang, lembut di tenggorokan, dan ramah di kantong.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="border-l border-neutral-800 pl-4">
                        <h4 class="text-sm font-bold text-gray-200">NOTTE SUBUH</h4>
                        <p class="text-xs text-gray-400 font-light">Smooth, sweet, and easy to enjoy. Dibuat khusus untuk pagi yang santai.</p>
                    </div>
                    <div class="border-l border-neutral-800 pl-4">
                        <h4 class="text-sm font-bold text-gray-200">ICED ROASTED CAPPUCCINO</h4>
                        <p class="text-xs text-gray-400 font-light">Roasted espresso, cold milk, and a creamy finish.</p>
                    </div>
                    <div class="border-l border-neutral-800 pl-4">
                        <h4 class="text-sm font-bold text-gray-200">AMERICANO</h4>
                        <p class="text-xs text-gray-400 font-light">Simple coffee. Nothing complicated.</p>
                    </div>
                </div>

                <a href="{{ route('customer.menu') }}" class="inline-block mt-4 text-xs font-bold text-notte-gold uppercase tracking-widest border-b border-notte-gold pb-1 hover:text-white transition">
                    Pesan Kopi Sekarang →
                </a>
            </div>
        </div>
    </section>

<!-- SECTION LITERAN -->
<section id="literan" class="py-24 bg-notte-card border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 text-center space-y-4">
            <span class="text-[11px] font-bold uppercase tracking-[0.25em] text-notte-gold block">Bring NOTTE Home</span>
            <h2 class="font-serif-title text-3xl md:text-5xl font-bold text-gray-100">Good Coffee, By The Liter.</h2>
            <p class="text-xs md:text-sm text-gray-400 font-light max-w-lg mx-auto leading-relaxed">
                Untuk kumpul bareng, nemenin lembur malam, atau sekadar stok kopi dingin siap minum di kulkas rumah.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-12 text-left">
                <!-- CARD 1 -->
                <div class="bg-notte-black border border-neutral-800/80 p-8 rounded-2xl transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] flex flex-col justify-between group cursor-default">
                    <div>
                        <div class="flex justify-end items-center mb-6">
                            <span class="text-[10px] font-mono font-bold tracking-widest text-notte-gold uppercase">1 Litre</span>
                        </div>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2 group-hover:text-notte-gold transition duration-300">NOTTE Subuh</h3>
                        <p class="text-xs text-gray-400 font-light leading-relaxed">Smooth, sweet, and easy to enjoy. Dibuat untuk pagi yang tenang dan malam yang panjang.</p>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="bg-notte-black border border-neutral-800/80 p-8 rounded-2xl transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] flex flex-col justify-between group cursor-default">
                    <div>
                        <div class="flex justify-end items-center mb-6">
                            <span class="text-[10px] font-mono font-bold tracking-widest text-notte-gold uppercase">1 Litre</span>
                        </div>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2 group-hover:text-notte-gold transition duration-300">Butterscotch</h3>
                        <p class="text-xs text-gray-400 font-light leading-relaxed">Sweet, buttery, creamy, with a little touch of sea salt.</p>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="bg-notte-black border border-neutral-800/80 p-8 rounded-2xl transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] flex flex-col justify-between group cursor-default">
                    <div>
                        <div class="flex justify-end items-center mb-6">
                            <span class="text-[10px] font-mono font-bold tracking-widest text-notte-gold uppercase">1 Litre</span>
                        </div>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2 group-hover:text-notte-gold transition duration-300">Caramel Macchiato</h3>
                        <p class="text-xs text-gray-400 font-light leading-relaxed">Espresso mantap berpadu harmonis dengan manisnya karamel melimpah.</p>
                    </div>
                </div>

                <!-- CARD 4 -->
                <div class="bg-notte-black border border-neutral-800/80 p-8 rounded-2xl transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40 hover:shadow-[0_8px_30px_rgba(197,168,128,0.08)] flex flex-col justify-between group cursor-default">
                    <div>
                        <div class="flex justify-end items-center mb-6">
                            <span class="text-[10px] font-mono font-bold tracking-widest text-notte-gold uppercase">1 Litre</span>
                        </div>
                        <h3 class="font-serif-title text-2xl text-gray-100 mb-2 group-hover:text-notte-gold transition duration-300">Americano</h3>
                        <p class="text-xs text-gray-400 font-light leading-relaxed">Simple coffee. Nothing complicated. Dingin, lugas, dan menyegarkan.</p>
                    </div>
                </div>
            </div>

            <div class="pt-10 text-center">
                <a href="{{ route('customer.menu') }}" class="inline-block bg-notte-gold hover:bg-amber-600 text-black font-extrabold text-xs px-9 py-4 rounded-full transition uppercase tracking-widest shadow-lg">
                    Order Menu 1L Sekarang →
                </a>
            </div>
        </div>
    </section>

    <!-- CORE VALUES -->
    <section class="py-20 bg-notte-black border-t border-neutral-800/80 reveal">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-card transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40">
                <span class="font-serif-title text-notte-gold text-4xl block mb-3 italic opacity-80">01.</span>
                <h3 class="font-serif-title text-lg text-gray-100 mb-2">Made at Home</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Dibuat dengan skala rumahan, cermat, dan perhatian penuh pada kebersihan serta kehangatan rasa.</p>
            </div>
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-card transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40">
                <span class="font-serif-title text-notte-gold text-4xl block mb-3 italic opacity-80">02.</span>
                <h3 class="font-serif-title text-lg text-gray-100 mb-2">Fair Price</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Good coffee doesn't have to cost a lot. Rasa kopi serius dengan harga yang masuk akal setiap hari.</p>
            </div>
            <div class="group p-8 border border-neutral-800/50 rounded-xl bg-notte-card transition-all duration-500 ease-out hover:-translate-y-2 hover:border-notte-gold/40">
                <span class="font-serif-title text-notte-gold text-4xl block mb-3 italic opacity-80">03.</span>
                <h3 class="font-serif-title text-lg text-gray-100 mb-2">Served with Care</h3>
                <p class="text-xs text-gray-400 leading-relaxed font-light">Setiap cangkir disiapkan khusus untuk menemani momen santai, obrolan malam, hingga produktivitas pagi lu.</p>
            </div>
        </div>
    </section>

    <!-- QUOTE BANNER -->
    <section class="py-20 bg-notte-card border-y border-neutral-800/80 reveal">
        <div class="max-w-4xl mx-auto px-6 text-center space-y-3">
            <span class="font-mono text-[10px] font-bold tracking-[0.3em] text-notte-gold uppercase block">NOTTE COFFEE</span>
            <h2 class="font-serif-title text-2xl md:text-4xl font-medium text-gray-100 italic leading-snug">
                "For late nights, slow mornings, and everything in between."
            </h2>
        </div>
    </section>

    <!-- VISIT US & FOOTER -->
    <section id="visit" class="pt-24 pb-12 bg-notte-black reveal">
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
                <p>&copy; {{ date('Y') }} NOTTE Coffee. Good Coffee. Fair Price.</p>
                <div class="flex gap-6">
                    <a href="{{ route('customer.menu') }}" class="hover:text-notte-gold transition">Order Online</a>
                    <a href="{{ route('admin.login') }}" class="hover:text-notte-gold transition">Staff Portal</a>
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