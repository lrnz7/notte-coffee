<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop paksa tabel lama kalau strukturnya beda/konflik
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('ingredients');
        Schema::enableForeignKeyConstraints();

        // Buat ulang tabel ingredients dengan bersih
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');
            $table->decimal('cost_per_unit', 15, 2)->default(0);
            $table->decimal('stock', 15, 2)->default(0);
            $table->timestamps();
        });

        // Buat ulang tabel recipes dengan kolom ingredient_id yang valid
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('ingredients');
    }
};