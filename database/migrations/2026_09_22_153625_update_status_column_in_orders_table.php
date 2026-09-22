<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah tipe kolom status jadi string/varchar biar muat status apa aja
            $table->string('status', 50)->default('pending_payment')->change();
            
            // Sekalian tambahkan kolom payment_proof kalau belum ada
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->change();
        });
    }
};