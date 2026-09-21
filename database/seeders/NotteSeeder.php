<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Menu;
use App\Models\Recipe;

class NotteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Dummy Bahan Baku (Materials)
        $beans = Material::create([
            'name' => 'Beans Arabica Blend',
            'unit' => 'gram',
            'unit_price' => 350.00, // Rp350 per gram
            'stock_quantity' => 1000.00, // 1 Kg
            'min_stock_alert' => 200.00,
        ]);

        $milk = Material::create([
            'name' => 'Susu UHT Full Cream',
            'unit' => 'ml',
            'unit_price' => 25.00, // Rp25 per ml
            'stock_quantity' => 5000.00, // 5 Liter
            'min_stock_alert' => 1000.00,
        ]);

        $syrup = Material::create([
            'name' => 'Butterscotch Syrup',
            'unit' => 'ml',
            'unit_price' => 150.00, // Rp150 per ml
            'stock_quantity' => 1000.00, // 1 Liter
            'min_stock_alert' => 200.00,
        ]);

        // 2. Data Dummy Menu (Menus)
        $menuKopi = Menu::create([
            'name' => 'Butterscotch Sea Salt',
            'category' => 'Kopi',
            'description' => 'Es Kopi susu signature dengan saus butterscotch dan sentuhan sea salt.',
            'selling_price' => 23000.00,
            'is_active' => true,
        ]);

        // 3. Data Dummy Resep / HPP (Bill of Materials)
        // Menu Butterscotch Sea Salt butuh: 18g beans, 120ml susu, 30ml syrup
        Recipe::create([
            'menu_id' => $menuKopi->id,
            'material_id' => $beans->id,
            'amount_needed' => 18.00,
        ]);

        Recipe::create([
            'menu_id' => $menuKopi->id,
            'material_id' => $milk->id,
            'amount_needed' => 120.00,
        ]);

        Recipe::create([
            'menu_id' => $menuKopi->id,
            'material_id' => $syrup->id,
            'amount_needed' => 30.00,
        ]);
    }
}