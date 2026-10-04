<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Material;
use App\Models\Menu;
use App\Models\Recipe;

class NotteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan penjaga Foreign Key MySQL sementara
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel sebelum seeding ulang (optional, tapi aman)
        Menu::truncate();
        Material::truncate();
        Recipe::truncate();

        // 2. Data Dummy Bahan Baku (Kembali pakai Material)
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

        // 3. Data Lengkap Menu (Digabung dari OfficialMenuSeeder)
        $menus = [
            // ==========================================
            // COFFEE CUPS
            // ==========================================
            [
                'name' => 'NOTTE Signature Subuh',
                'category' => 'Coffee',
                'description' => 'Smooth Palm Sugar Latte. (Secret Prep: 22g Arabica Blend, 160ml Premium Milk, 25ml Organic Aren).',
                'selling_price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Butterscotch Sea Salt',
                'category' => 'Coffee',
                'description' => 'Sweet, buttery, with a touch of sea salt. (Secret Prep: 22g Espresso, 120ml Milk Base, 20ml Butterscotch, 30ml Heavy Cream Foam).',
                'selling_price' => 23000,
                'image' => 'https://images.unsplash.com/photo-1644558509491-b65fb5a383ce?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Caramel Macchiato',
                'category' => 'Coffee',
                'description' => 'Espresso meets sweet caramel. (Secret Prep: 22g Espresso, 150ml Milk Base, 15ml Vanilla, 15ml Thick Caramel).',
                'selling_price' => 23000,
                'image' => 'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Mont Blanc',
                'category' => 'Coffee',
                'description' => 'Bold & Refreshing Orange Coffee. (Secret Prep: 30ml Espresso, 40ml Valencia Orange, 20ml French Vanilla, Cream Foam).',
                'selling_price' => 23000,
                'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Iced Roasted Cappuccino',
                'category' => 'Coffee',
                'description' => 'Roasted espresso and cold milk. (Secret Prep: 22g Espresso, 110ml Milk Base, 25ml Heavy Creamer).',
                'selling_price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1557142046-c704a3adf364?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Americano',
                'category' => 'Coffee',
                'description' => 'Pure, bold, and refreshing. (Secret Prep: 22g Espresso Yield 40ml, 120ml Filtered Water).',
                'selling_price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],

            // ==========================================
            // BOTTLED 1 LITER
            // ==========================================
            [
                'name' => 'NOTTE Subuh 1L',
                'category' => '1 Liter',
                'description' => 'Good Coffee, By The Liter. Smooth & sweet Palm Sugar Latte for sharing.',
                'selling_price' => 75000,
                'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Butterscotch Sea Salt 1L',
                'category' => '1 Liter',
                'description' => 'Sweet, buttery, creamy, with a touch of sea salt in a 1-liter bottle.',
                'selling_price' => 85000,
                'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Caramel Macchiato 1L',
                'category' => '1 Liter',
                'description' => 'Espresso mantap berpadu harmonis dengan manisnya karamel 1000ml.',
                'selling_price' => 85000,
                'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Americano 1L',
                'category' => '1 Liter',
                'description' => 'Simple black coffee. Cold, bold, and ready in your fridge.',
                'selling_price' => 65000,
                'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],

            // ==========================================
            // NON-COFFEE
            // ==========================================
            [
                'name' => 'Thai Tea',
                'category' => 'Non-Coffee',
                'description' => 'Authentic Thai Tea blend with creamy condensed milk.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Thai Green Tea',
                'category' => 'Non-Coffee',
                'description' => 'Premium Thai Green Tea, sweet and refreshing.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1625797380121-72f87ee877ce?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Pure Cocoa',
                'category' => 'Non-Coffee',
                'description' => 'Rich and thick iced dark chocolate.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Mineral Water',
                'category' => 'Non-Coffee',
                'description' => 'Air mineral tidak dingin yaa!!!',
                'selling_price' => 5000,
                'image' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],

            // ==========================================
            // FOOD
            // ==========================================
            [
                'name' => 'Kentang Goreng',
                'category' => 'Food',
                'description' => 'Crispy straight-cut french fries with savory seasoning.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Indomie Polos',
                'category' => 'Food',
                'description' => 'Comfort food andalan. Indomie goreng original.',
                'selling_price' => 8000,
                'image' => 'https://images.unsplash.com/photo-1612929633738-8fe01f7c845f?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Indomie Telur',
                'category' => 'Food',
                'description' => 'Indomie goreng dengan tambahan telur mata sapi.',
                'selling_price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Indomie Double Polos',
                'category' => 'Food',
                'description' => 'Porsi kuli. Dua bungkus Indomie goreng original.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Mie Nyemek',
                'category' => 'Food',
                'description' => 'Indomie rebus dengan kuah kental, telur, dan bumbu rahasia NOTTE.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Nasi Telur',
                'category' => 'Food',
                'description' => 'Nasi hangat, telur ceplok, kecap, dan sambal bawang.',
                'selling_price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1525648199074-cee30ba79a4a?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],

            // ==========================================
            // DESSERT
            // ==========================================
            [
                'name' => 'Salad Buah',
                'category' => 'Dessert',
                'description' => 'Fresh cuts of seasonal fruits with creamy cheese dressing.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1490474418585-ba9bad8fd0ea?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Tiramisu',
                'category' => 'Dessert',
                'description' => 'Classic Italian dessert with espresso-soaked ladyfingers.',
                'selling_price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1571115177098-24edf4c66ff9?q=80&w=600&auto=format&fit=crop',
                'is_active' => true,
            ],
        ];

        // Looping untuk nyimpen semua menu ke database
        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }

        // 4. Data Dummy Resep / HPP (Tautkan bahan baku ke menu Butterscotch)
        $butterscotchMenu = Menu::where('name', 'Butterscotch Sea Salt')->first();

        if ($butterscotchMenu) {
            Recipe::create([
                'menu_id' => $butterscotchMenu->id,
                'ingredient_id' => $beans->id,
                'quantity' => 18.00,
            ]);

            Recipe::create([
                'menu_id' => $butterscotchMenu->id,
                'ingredient_id' => $milk->id,
                'quantity' => 120.00,
            ]);

            Recipe::create([
                'menu_id' => $butterscotchMenu->id,
                'ingredient_id' => $syrup->id,
                'quantity' => 30.00,
            ]);
        }

        // 5. Nyalakan lagi penjaga Foreign Key MySQL
        Schema::enableForeignKeyConstraints();
    }
}