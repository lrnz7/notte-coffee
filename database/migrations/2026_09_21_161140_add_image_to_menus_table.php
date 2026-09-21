<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('menus', 'image')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};