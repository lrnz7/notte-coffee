<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080808; color: #e5e5e5; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-notte-card { background-color: #121212; }
        .text-notte-gold { color: #c5a880; }
        .bg-notte-gold { background-color: #c5a880; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-6 relative overflow-hidden">

    <!-- Background Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#c5a880]/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md bg-notte-card border border-neutral-800 p-8 rounded-2xl shadow-2xl relative z-10">
        
        <div class="text-center mb-8">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-notte.png') }}" alt="NOTTE Logo" class="h-10 mx-auto mb-4 object-contain">
            </a>
            <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-notte-gold block">Customer Portal</span>
            <p class="text-xs text-gray-400 mt-1">Dapatkan Diskon 50% untuk pesanan pertama lu!</p>
        </div>

        <!-- ALERT ERROR & SUCCESS -->
        @if(session('error_msg'))
            <div class="mb-4 p-3 bg-rose-950/80 border border-rose-800 text-rose-300 text-xs rounded-lg text-center">
                {{ session('error_msg') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-950/80 border border-rose-800 text-rose-300 text-xs rounded-lg">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- TAB BUTTONS -->
        <div class="flex border-b border-neutral-800 mb-6 text-xs font-bold uppercase tracking-wider">
            <button id="tab-login-btn" onclick="switchTab('login')" class="flex-1 py-3 border-b-2 border-notte-gold text-notte-gold">Masuk</button>
            <button id="tab-register-btn" onclick="switchTab('register')" class="flex-1 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-300">Daftar Akun</button>
        </div>

        <!-- FORM LOGIN -->
        <form id="form-login" action="{{ route('customer.login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <button type="submit" class="w-full bg-notte-gold text-black font-extrabold text-xs uppercase tracking-widest py-3.5 rounded-full hover:bg-amber-600 transition shadow-lg mt-4">
                Masuk Sekarang
            </button>
        </form>

        <!-- FORM REGISTER -->
        <form id="form-register" action="{{ route('customer.register.post') }}" method="POST" class="space-y-4 hidden">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Nomor WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full bg-[#080808] border border-neutral-800 p-3 rounded-lg text-xs text-gray-200 focus:border-notte-gold outline-none">
            </div>
            <button type="submit" class="w-full bg-notte-gold text-black font-extrabold text-xs uppercase tracking-widest py-3.5 rounded-full hover:bg-amber-600 transition shadow-lg mt-4">
                Daftar & Klaim Diskon 50%
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-notte-gold transition">← Kembali ke Landing Page</a>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const loginBtn = document.getElementById('tab-login-btn');
            const registerBtn = document.getElementById('tab-register-btn');

            if (tab === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginBtn.className = 'flex-1 py-3 border-b-2 border-notte-gold text-notte-gold';
                registerBtn.className = 'flex-1 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-300';
            } else {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
                registerBtn.className = 'flex-1 py-3 border-b-2 border-notte-gold text-notte-gold';
                loginBtn.className = 'flex-1 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-300';
            }
        }

        // Otomatis pindah tab ke register kalau ada error saat pendaftaran
        @if(session('active_tab') === 'register' || $errors->has('name') || $errors->has('phone') || $errors->has('password_confirmation'))
            switchTab('register');
        @endif
    </script>
</body>
</html>