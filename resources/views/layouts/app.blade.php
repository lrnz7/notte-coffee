<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NOTTE Coffee') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-notte.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,400&display=swap" rel="stylesheet">
</head>
<body class="bg-[#080808] text-gray-200 antialiased">
    @yield('content')
</body>
</html>
