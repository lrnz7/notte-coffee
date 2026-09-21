<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class OfficialMenuSeeder extends Seeder
{
    public function run()
    {
        // Matikan pengecekan Foreign Key sementara biar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel menu
        Menu::truncate();

        // Hidupkan kembali pengecekan Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = [
            [
                'name' => 'NOTTE SUBUH',
                'category' => 'Coffee',
                'description' => 'Palm Sugar Latte. Creamy. Smooth. Everyday.',
                'selling_price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'MONT BLANC',
                'category' => 'Signature',
                'description' => 'Cold Brew Cream. Bold. Creamy. Refreshing.',
                'selling_price' => 28000,
                'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'NOTTE LATINA',
                'category' => 'Black Coffee',
                'description' => 'Simple. Bold. Timeless. Pure coffee. Pure you.',
                'selling_price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'NOTTE SEÑORITA',
                'category' => 'Coffee',
                'description' => 'Bold espresso. Sweetened with condensed milk. Made for the night.',
                'selling_price' => 25000,
                'image' => 'https://images.unsplash.com/photo-1557142046-c704a3adf364?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'NOTTE AMORE MIO',
                'category' => 'Signature',
                'description' => 'Caramel Macchiato. Caramel meets espresso. Sweet, smooth, and made to be your daily love.',
                'selling_price' => 28000,
                'image' => 'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'BUTTERSCOTCH SEA SALT',
                'category' => 'Signature',
                'description' => 'Buttery sweet. Perfectly balanced with a touch of sea salt.',
                'selling_price' => 30000,
                'image' => 'https://images.unsplash.com/photo-1644558509491-b65fb5a383ce?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}