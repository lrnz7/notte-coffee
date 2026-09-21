<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTTE Coffee - Dark Luxury</title>
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
    <style> 
        body { background-color: #0A0A0A; color: #F2EDE3; -webkit-font-smoothing: antialiased; } 
        
        /* Scroll Reveal Animation */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1), transform 1s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* --- EFEK HOVER HALUS & BERKELAS --- */
        .btn-luxury {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-luxury:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(201, 164, 104, 0.15);
        }

        .card-luxury {
            transition: border-color 0.4s ease, transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .card-luxury:hover {
            transform: translateY(-4px);
        }

        .nav-link-luxury {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link-luxury::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: #C9A468;
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link-luxury:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="font-inter">

    <!-- NAVBAR -->
    <nav class="fixed w-full z-50 px-8 py-6 flex justify-between items-center mix-blend-difference">
        <div class="flex flex-col items-center">
            <span class="font-fraunces text-2xl tracking-widest text-cream">NOTTE</span>
            <span class="text-[10px] tracking-wide text-gold-warm uppercase mt-1">C o f f e e</span>
        </div>
        <div class="hidden md:flex space-x-8 text-xs tracking-wide uppercase text-cream">
            <a href="{{ route('customer.menu') }}" class="nav-link-luxury hover:text-gold-warm">Menu</a>
            <a href="{{ route('pages.about') }}" class="nav-link-luxury hover:text-gold-warm">Philosophy</a>
            <a href="{{ route('pages.faq') }}" class="nav-link-luxury hover:text-gold-warm">FAQ</a>
        </div>
    </nav>

    <!-- SECTION 1: Hero -->
    <section class="relative min-h-screen flex items-center bg-black-deep overflow-hidden reveal-on-scroll">
        <div class="container mx-auto px-6 lg:px-16 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center z-10 pt-24 pb-12">
            <div class="max-w-lg">
                <h1 class="font-fraunces font-light text-6xl lg:text-8xl leading-none text-cream mb-6">THE<br>CONTRAST</h1>
                <p class="font-fraunces italic text-xl text-grey-text mb-12">Mont Blanc adalah pertemuan dua dunia yang berbeda.</p>
                <div class="flex flex-col border-t border-hairline">
                    <div class="flex items-start py-5 border-b border-hairline">
                        <div class="w-6 h-6 border border-hairline rounded-full flex items-center justify-center mr-6 mt-1 flex-shrink-0"><span class="font-fraunces italic text-xs text-cream">co</span></div>
                        <div><h4 class="font-inter text-xs tracking-wide uppercase text-cream mb-1">Cold Brew</h4><p class="text-sm text-grey-text">Bold. Clean. Full of character.</p></div>
                    </div>
                    <div class="flex items-start py-5 border-b border-hairline">
                        <div class="w-6 h-6 border border-hairline rounded-full flex items-center justify-center mr-6 mt-1 flex-shrink-0"><span class="font-fraunces italic text-xs text-cream">cr</span></div>
                        <div><h4 class="font-inter text-xs tracking-wide uppercase text-cream mb-1">Cream</h4><p class="text-sm text-grey-text">Smooth. Silky. Perfectly balanced.</p></div>
                    </div>
                </div>
                <a href="{{ route('customer.menu') }}" class="btn-luxury mt-10 inline-block border border-gold-warm text-gold-warm font-inter text-xs tracking-wide uppercase px-8 py-3 rounded hover:bg-gold-warm hover:text-black-deep transition">Pesan Sekarang</a>
            </div>
            <div class="h-full w-full relative">
                <div class="aspect-[4/5] lg:aspect-auto lg:h-[80vh] w-full bg-black-rich relative overflow-hidden rounded-md border border-hairline">
                    <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?q=80&w=1200&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-black-deep via-transparent to-transparent opacity-60"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: Signature Collection Preview -->
    <section class="bg-black-rich py-24 border-t border-hairline reveal-on-scroll">
        <div class="container mx-auto px-6 lg:px-16 text-center mb-16">
            <span class="text-[10px] uppercase tracking-widest text-gold-warm block mb-3">Highlight Menu</span>
            <h2 class="font-fraunces font-light text-5xl text-cream">SIGNATURE COLLECTION</h2>
        </div>
        
        <div class="container mx-auto px-6 lg:px-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(isset($menus) && $menus->count() > 0)
                @foreach($menus->take(3) as $menu)
                <a href="{{ route('customer.menu') }}" class="card-luxury group block border border-hairline hover:border-gold-warm overflow-hidden bg-black-deep">
                    <div class="aspect-square relative overflow-hidden">
                        <img src="{{ $menu->image ?? 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop' }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700 opacity-90">
                    </div>
                    <div class="p-6 text-center border-t border-hairline">
                        <h3 class="font-fraunces text-xl text-cream mb-1">{{ $menu->name }}</h3>
                        <span class="text-xs text-gold-warm tracking-wider uppercase">Lihat Detail →</span>
                    </div>
                </a>
                @endforeach
            @endif
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('customer.menu') }}" class="text-xs uppercase tracking-widest text-grey-text border-b border-grey-text pb-1 hover:text-cream hover:border-cream transition">Lihat Seluruh Menu</a>
        </div>
    </section>

    <!-- SECTION 3: The Philosophy & Craftsmanship -->
    <section class="bg-black-deep py-28 border-t border-hairline reveal-on-scroll">
        <div class="container mx-auto px-6 lg:px-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[10px] uppercase tracking-widest text-gold-warm block mb-3">The Craft</span>
                    <h2 class="font-fraunces font-light text-4xl lg:text-6xl text-cream mb-6 leading-tight">DIANGKAT DARI SENI & PRESISI</h2>
                    <p class="text-grey-text text-sm leading-relaxed mb-8 font-inter">
                        Di NOTTE, secangkir kopi bukan sekadar minuman penghilang kantuk. Ini adalah hasil eksperimen rasa yang mempertemukan karakter bold dan kehalusan tekstur dalam satu harmoni malam.
                    </p>
                    
                    <div class="space-y-6 border-t border-hairline pt-6">
                        <div class="flex items-start">
                            <span class="font-fraunces text-gold-warm text-lg mr-4">01</span>
                            <div>
                                <h4 class="font-inter text-xs tracking-wide uppercase text-cream mb-1">Precision Roasting</h4>
                                <p class="text-xs text-grey-text leading-relaxed">Biji kopi pilihan dipanggang dengan standar akurasi tinggi untuk menjaga kedalaman rasa aslinya.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="font-fraunces text-gold-warm text-lg mr-4">02</span>
                            <div>
                                <h4 class="font-inter text-xs tracking-wide uppercase text-cream mb-1">Artisan Contrast</h4>
                                <p class="text-xs text-grey-text leading-relaxed">Racikan khas yang menyeimbangkan elemen pekat dan lembut secara presisi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-[4/5] w-full bg-black-rich relative overflow-hidden rounded-md border border-hairline">
                        <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-80 hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black-deep via-transparent to-transparent opacity-40"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-black-deep border-t border-hairline py-12 px-8 reveal-on-scroll">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
            <div class="flex flex-col items-center md:items-start">
                <span class="font-fraunces text-xl tracking-widest text-cream">NOTTE</span>
                <span class="text-[9px] tracking-wide text-gold-warm uppercase mt-0.5">C o f f e e</span>
            </div>
            <div class="flex space-x-6 text-xs uppercase tracking-wider text-grey-text">
                <a href="{{ route('pages.about') }}" class="hover:text-cream">Tentang Kami</a>
                <a href="{{ route('pages.faq') }}" class="hover:text-cream">FAQ & Bantuan</a>
                <a href="{{ route('pages.terms') }}" class="hover:text-cream">Syarat & Privasi</a>
            </div>
            <p class="text-xs text-grey-text">© {{ date('Y') }} NOTTE Coffee. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Script Intersection Observer -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>