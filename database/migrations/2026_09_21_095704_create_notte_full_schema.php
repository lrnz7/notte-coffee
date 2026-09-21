<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 0. Tabel Users untuk Multi-Role (Admin, Kasir, Customer)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'cashier', 'customer'])->default('customer');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // 1. Tabel Bahan Baku & Stok (Jantung ERP)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Beans Arabica, Susu UHT
            $table->string('unit'); // Contoh: gram, ml, pcs
            $table->decimal('unit_price', 12, 2); // Harga beli per unit
            $table->decimal('stock_quantity', 12, 2); // Sisa stok saat ini
            $table->decimal('min_stock_alert', 12, 2)->default(100); // Batas minimal alert
            $table->timestamps();
        });

        // 2. Tabel Katalog Menu (Kategori digabung jadi kolom teks)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Butterscotch Sea Salt
            $table->string('category'); // Contoh: Kopi, Non-Kopi, Snack (Langsung ketik teks)
            $table->text('description')->nullable();
            $table->decimal('selling_price', 12, 2); // Harga Jual
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Resep / Bill of Materials (Relasi Menu ke Bahan Baku)
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_needed', 12, 2); // Takaran per 1 porsi
            $table->timestamps();
        });

        // 4. Tabel Transaksi / Pesanan Utama
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->default('-'); // Beri default '-' biar gak error kalo kosong
            $table->text('shipping_address')->nullable();
            $table->enum('order_type', ['delivery', 'pickup', 'dine_in', 'takeaway'])->default('dine_in'); // <-- Tambah/sesuaiin enumnya
            $table->enum('payment_method', ['qris', 'transfer', 'cash']);
            $table->string('payment_proof')->nullable();
            $table->enum('status', ['pending', 'paid', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('total_cogs', 12, 2);
            $table->decimal('gross_profit', 12, 2);
            $table->timestamps();
        });

        // 5. Tabel Detail Item Pesanan
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 12, 2);
            $table->decimal('cogs_at_purchase', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // 6. Tabel Arus Kas & Pengeluaran Operasional
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']);
            $table->string('category'); // Contoh: Belanja Bahan, Listrik, Gaji
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tabel Sessions untuk Session Manager Laravel
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Tabel Cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('sessions');
    }
};