<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Simpan alamat utama + koordinat di tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->string('latitude')->nullable()->after('address');
                $table->string('longitude')->nullable()->after('latitude');
            }
        });

        // 2. Tambah order_source di tabel orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_source')) {
                $table->string('order_source')->default('online_web')->after('status');
                // Nilai: 'offline_pos', 'online_web', 'merchant'
            }
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('order_source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_source', 'delivery_address']);
        });
    }
};