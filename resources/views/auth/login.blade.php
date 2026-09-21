<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NOTTE Coffee ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center h-screen font-sans">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-amber-600 tracking-wider">NOTTE COFFEE</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem ERP & Manajemen Kasir</p>
        </div>

        @if($errors->has('email'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="admin@notte.com"
                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                    placeholder="••••••••"
                    class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-md transition duration-200">
                Masuk ke Panel ERP
            </button>
        </form>

        <div class="mt-6 border-t pt-4 text-xs text-gray-400 text-center">
            <p>Akun Demo Seeder:</p>
            <p class="font-mono mt-1 text-gray-600">Email: <span class="font-bold">admin@notte.com</span> | Pass: <span class="font-bold">password123</span></p>
        </div>
    </div>
</body>
</html>