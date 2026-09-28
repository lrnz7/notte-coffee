<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashier_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('closing_time');
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('system_cash_sales', 12, 2)->default(0);
            $table->decimal('system_qris_sales', 12, 2)->default(0);
            $table->decimal('physical_cash_count', 12, 2)->default(0);
            $table->decimal('cash_difference', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_closings');
    }
};
