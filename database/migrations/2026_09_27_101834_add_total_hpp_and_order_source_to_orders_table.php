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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'total_hpp')) {
                $table->decimal('total_hpp', 12, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'order_source')) {
                $table->string('order_source')->nullable()->after('total_hpp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'total_hpp')) {
                $table->dropColumn('total_hpp');
            }
            if (Schema::hasColumn('orders', 'order_source')) {
                $table->dropColumn('order_source');
            }
        });
    }
};
