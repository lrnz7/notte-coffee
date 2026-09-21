<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan {{ $order->invoice_number }} — NOTTE Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;1,300&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        black: { deep: '#0A0A0A', rich: '#121212' },
                        cream: '#F2EDE3',
                        gold: { warm: '#C9A468' },
                        grey: { text: '#B5B0A8' },
                        hairline: '#2A2A2A'
                    },
                    fontFamily: {
                        fraunces: ['Fraunces', 'serif'],
                        inter: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-black-deep text-cream font-inter min-h-screen flex flex-col justify-between p-6">

    <div class="max-w-lg mx-auto w-full my-auto bg-black-rich border border-hairline p-8 rounded">
        <div class="text-center pb-6 border-b border-hairline mb-6">
            <span class="text-[10px] uppercase tracking-widest text-gold-warm">Order Confirmed</span>
            <h1 class="font-fraunces text-3xl text-cream mt-2">{{ $order->invoice_number }}</h1>
            <p class="text-xs text-grey-text mt-1">Terima kasih, {{ $order->customer_name }}. Pesanan Anda telah diterima.</p>
        </div>

        <div class="space-y-4 mb-8">
            <div class="flex justify-between text-xs">
                <span class="text-grey-text">Status Pesanan</span>
                <span class="font-bold text-gold-warm uppercase tracking-wide">{{ $order->status }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-grey-text">Tipe Pesanan</span>
                <span class="uppercase text-cream">{{ $order->order_type }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-grey-text">Nomor HP</span>
                <span class="text-cream">{{ $order->customer_phone }}</span>
            </div>
        </div>

        <div class="border-t border-hairline pt-6 mb-8">
            <h3 class="text-xs uppercase tracking-wider text-gold-warm mb-4">Detail Item</h3>
            <div class="space-y-3">
                @foreach($order->orderItems as $item)
                <div class="flex justify-between text-xs">
                    <span class="text-cream">{{ $item->menu->name }} x {{ $item->quantity }}</span>
                    <span class="text-grey-text">Rp{{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between text-sm font-bold pt-4 mt-4 border-t border-hairline">
                <span class="text-cream">Total Tagihan</span>
                <span class="text-gold-warm">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <a href="{{ route('customer.menu') }}" class="block text-center w-full border border-gold-warm text-gold-warm text-xs uppercase tracking-widest py-3 hover:bg-gold-warm hover:text-black-deep transition">
            Kembali ke Katalog
        </a>
    </div>

</body>
</html>