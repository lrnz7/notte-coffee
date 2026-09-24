<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MasterDataERPSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Recipe::truncate();
        Ingredient::truncate();
        Menu::truncate();
        if (Schema::hasTable('materials')) {
            DB::table('materials')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. MASTER BAHAN BAKU (HARGA MODAL LOGIS & MARGIN BAGUS)
        $ingredientsData = [
            'beans'          => ['name' => 'Biji Kopi Houseblend', 'unit' => 'gram', 'cost' => 150, 'stock' => 100000],  
            'milk_base'      => ['name' => 'Milk Base House', 'unit' => 'ml', 'cost' => 12, 'stock' => 200000],      
            'full_cream'     => ['name' => 'Susu Full Cream', 'unit' => 'ml', 'cost' => 12, 'stock' => 200000],      
            'gula_aren'      => ['name' => 'Sirup Gula Aren', 'unit' => 'ml', 'cost' => 20, 'stock' => 50000],       
            'evaporasi'      => ['name' => 'Susu Evaporasi', 'unit' => 'ml', 'cost' => 18, 'stock' => 50000],        
            'ellenka'        => ['name' => 'Krimer Ellenka', 'unit' => 'ml', 'cost' => 10, 'stock' => 50000],        
            'skm'            => ['name' => 'SKM Carnation', 'unit' => 'gram', 'cost' => 15, 'stock' => 50000],       
            'vanilla'        => ['name' => 'Vanilla Syrup', 'unit' => 'ml', 'cost' => 40, 'stock' => 20000],        
            'caramel'        => ['name' => 'Caramel Sauce', 'unit' => 'ml', 'cost' => 50, 'stock' => 20000],        
            'butterscotch'   => ['name' => 'Butterscotch Syrup', 'unit' => 'ml', 'cost' => 45, 'stock' => 20000],   
            'sea_salt'       => ['name' => 'Sea Salt Cream', 'unit' => 'ml', 'cost' => 10, 'stock' => 20000],      
            'whip_cream'     => ['name' => 'Whipping Cream', 'unit' => 'ml', 'cost' => 30, 'stock' => 30000],      
            'orange_juice'   => ['name' => 'Jus Jeruk Fresh', 'unit' => 'ml', 'cost' => 20, 'stock' => 30000],      
            'cup_pet'        => ['name' => 'Cup PET + Lid', 'unit' => 'pcs', 'cost' => 600, 'stock' => 10000],     
            'paper_cup'      => ['name' => 'Paper Cup 8oz', 'unit' => 'pcs', 'cost' => 500, 'stock' => 10000],     
            'botol_1l'       => ['name' => 'Botol Kale 1L', 'unit' => 'pcs', 'cost' => 3500, 'stock' => 5000],      
            'es_batu'        => ['name' => 'Es Batu Kristal', 'unit' => 'gram', 'cost' => 1, 'stock' => 1000000],    
            'air'            => ['name' => 'Air RO Brewing', 'unit' => 'ml', 'cost' => 1, 'stock' => 2000000],      
            
            // NON-COFFEE, FOOD & DESSERT
            'thai_tea'       => ['name' => 'Daun Thai Tea', 'unit' => 'gram', 'cost' => 60, 'stock' => 50000],       
            'green_tea'      => ['name' => 'Daun Green Tea', 'unit' => 'gram', 'cost' => 70, 'stock' => 50000],      
            'cocoa'          => ['name' => 'Bubuk Pure Cocoa', 'unit' => 'gram', 'cost' => 80, 'stock' => 50000],     
            'mineral_botol'  => ['name' => 'Air Mineral 600ml', 'unit' => 'pcs', 'cost' => 1500, 'stock' => 5000],    
            'kentang'        => ['name' => 'Kentang Frozen', 'unit' => 'gram', 'cost' => 20, 'stock' => 100000],     
            'indomie'        => ['name' => 'Indomie Goreng', 'unit' => 'pcs', 'cost' => 2800, 'stock' => 5000],     
            'telur'          => ['name' => 'Telur Ayam', 'unit' => 'pcs', 'cost' => 1500, 'stock' => 5000],         
            'nasi'           => ['name' => 'Nasi Putih', 'unit' => 'pcs', 'cost' => 1500, 'stock' => 5000],         
            'salad_buah'     => ['name' => 'Salad Buah Set', 'unit' => 'pcs', 'cost' => 4500, 'stock' => 2000],     
            'tiramisu'       => ['name' => 'Tiramisu Box', 'unit' => 'pcs', 'cost' => 5000, 'stock' => 2000],       
        ];

        $ingredients = [];
        foreach ($ingredientsData as $key => $data) {
            $ing = Ingredient::create([
                'name'          => $data['name'],
                'unit'          => $data['unit'],
                'cost_per_unit' => $data['cost'],
                'stock'         => $data['stock'],
            ]);
            $ingredients[$key] = $ing;

            // Disesuaikan persis dengan struktur tabel materials milik lu (tanpa kolom cost_per_unit & stock)
            if (Schema::hasTable('materials')) {
                DB::table('materials')->insert([
                    'id'             => $ing->id,
                    'name'           => $data['name'],
                    'unit'           => $data['unit'],
                    'unit_price'     => $data['cost'],
                    'stock_quantity' => $data['stock'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // 2. MASTER MENU & RESEP FIKTIF REALISTIS
        $m1 = Menu::create(['name' => 'NOTTE Signature Subuh', 'category' => 'Coffee', 'selling_price' => 20000, 'description' => 'Smooth Palm Sugar Latte. Signature Blend.', 'image' => 'https://images.unsplash.com/photo-1559525839-b184a4d698c7?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m1->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],       
            ['ing' => $ingredients['milk_base'], 'qty' => 120],   
            ['ing' => $ingredients['gula_aren'], 'qty' => 15],    
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],       
            ['ing' => $ingredients['es_batu'], 'qty' => 100],     
        ]);

        $m2 = Menu::create(['name' => 'Butterscotch Sea Salt', 'category' => 'Coffee', 'selling_price' => 23000, 'description' => 'Sweet, buttery, creamy, with a touch of sea salt.', 'image' => 'https://images.unsplash.com/photo-1644558509491-b65fb5a383ce?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m2->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],
            ['ing' => $ingredients['milk_base'], 'qty' => 100],
            ['ing' => $ingredients['butterscotch'], 'qty' => 15],
            ['ing' => $ingredients['whip_cream'], 'qty' => 20],
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],
            ['ing' => $ingredients['es_batu'], 'qty' => 100],
        ]);

        $m3 = Menu::create(['name' => 'Caramel Macchiato', 'category' => 'Coffee', 'selling_price' => 23000, 'description' => 'Rich espresso meets sweet caramel and velvety milk.', 'image' => 'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m3->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],
            ['ing' => $ingredients['milk_base'], 'qty' => 120],
            ['ing' => $ingredients['caramel'], 'qty' => 10],
            ['ing' => $ingredients['vanilla'], 'qty' => 10],
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],
            ['ing' => $ingredients['es_batu'], 'qty' => 100],
        ]);

        $m4 = Menu::create(['name' => 'Mont Blanc', 'category' => 'Coffee', 'selling_price' => 23000, 'description' => 'Bold & Refreshing Orange Coffee with vanilla cream foam.', 'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m4->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],
            ['ing' => $ingredients['orange_juice'], 'qty' => 30],
            ['ing' => $ingredients['vanilla'], 'qty' => 10],
            ['ing' => $ingredients['whip_cream'], 'qty' => 15],
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],
            ['ing' => $ingredients['es_batu'], 'qty' => 100],
        ]);

        $m5 = Menu::create(['name' => 'Iced Roasted Cappuccino', 'category' => 'Coffee', 'selling_price' => 20000, 'description' => 'Roasted espresso with cold silky milk.', 'image' => 'https://images.unsplash.com/photo-1557142046-c704a3adf364?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m5->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],
            ['ing' => $ingredients['milk_base'], 'qty' => 130],
            ['ing' => $ingredients['ellenka'], 'qty' => 10],
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],
            ['ing' => $ingredients['es_batu'], 'qty' => 100],
        ]);

        $m6 = Menu::create(['name' => 'Americano', 'category' => 'Coffee', 'selling_price' => 20000, 'description' => 'Pure, bold espresso with chilled filtered water.', 'image' => 'https://images.unsplash.com/photo-1550461716-bf9173208941?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($m6->id, [
            ['ing' => $ingredients['beans'], 'qty' => 15],
            ['ing' => $ingredients['air'], 'qty' => 120],
            ['ing' => $ingredients['cup_pet'], 'qty' => 1],
            ['ing' => $ingredients['es_batu'], 'qty' => 100],
        ]);

        // B. BOTTLED 1 LITER
        $l1 = Menu::create(['name' => 'NOTTE Subuh 1L', 'category' => '1 Liter', 'selling_price' => 75000, 'description' => 'Bottled 1000ml Signature Palm Sugar Latte.', 'image' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($l1->id, [
            ['ing' => $ingredients['full_cream'], 'qty' => 600],
            ['ing' => $ingredients['beans'], 'qty' => 60],
            ['ing' => $ingredients['gula_aren'], 'qty' => 80],
            ['ing' => $ingredients['botol_1l'], 'qty' => 1]
        ]);

        $l2 = Menu::create(['name' => 'Butterscotch Sea Salt 1L', 'category' => '1 Liter', 'selling_price' => 85000, 'description' => 'Bottled 1000ml Butterscotch Sea Salt Latte.', 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($l2->id, [
            ['ing' => $ingredients['full_cream'], 'qty' => 600],
            ['ing' => $ingredients['beans'], 'qty' => 60],
            ['ing' => $ingredients['butterscotch'], 'qty' => 60],
            ['ing' => $ingredients['botol_1l'], 'qty' => 1]
        ]);

        $l3 = Menu::create(['name' => 'Caramel Macchiato 1L', 'category' => '1 Liter', 'selling_price' => 85000, 'description' => 'Bottled 1000ml Rich Caramel Macchiato.', 'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($l3->id, [
            ['ing' => $ingredients['full_cream'], 'qty' => 600],
            ['ing' => $ingredients['beans'], 'qty' => 60],
            ['ing' => $ingredients['caramel'], 'qty' => 60],
            ['ing' => $ingredients['botol_1l'], 'qty' => 1]
        ]);

        $l4 = Menu::create(['name' => 'Americano 1L', 'category' => '1 Liter', 'selling_price' => 65000, 'description' => 'Bottled 1000ml Cold Bold Black Coffee.', 'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($l4->id, [
            ['ing' => $ingredients['beans'], 'qty' => 70],
            ['ing' => $ingredients['air'], 'qty' => 800],
            ['ing' => $ingredients['botol_1l'], 'qty' => 1]
        ]);

        // C. NON-COFFEE
        $n1 = Menu::create(['name' => 'Thai Tea', 'category' => 'Non-Coffee', 'selling_price' => 15000, 'description' => 'Authentic Thai Tea with condensed milk.', 'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($n1->id, [['ing' => $ingredients['thai_tea'], 'qty' => 15], ['ing' => $ingredients['skm'], 'qty' => 30], ['ing' => $ingredients['cup_pet'], 'qty' => 1]]);

        $n2 = Menu::create(['name' => 'Thai Green Tea', 'category' => 'Non-Coffee', 'selling_price' => 15000, 'description' => 'Sweet & creamy Thai Green Tea.', 'image' => 'https://images.unsplash.com/photo-1625797380121-72f87ee877ce?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($n2->id, [['ing' => $ingredients['green_tea'], 'qty' => 15], ['ing' => $ingredients['skm'], 'qty' => 30], ['ing' => $ingredients['cup_pet'], 'qty' => 1]]);

        $n3 = Menu::create(['name' => 'Pure Cocoa', 'category' => 'Non-Coffee', 'selling_price' => 15000, 'description' => 'Rich dark iced chocolate.', 'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($n3->id, [['ing' => $ingredients['cocoa'], 'qty' => 20], ['ing' => $ingredients['full_cream'], 'qty' => 100], ['ing' => $ingredients['cup_pet'], 'qty' => 1]]);

        $n4 = Menu::create(['name' => 'Mineral Water', 'category' => 'Non-Coffee', 'selling_price' => 5000, 'description' => 'Air mineral segar 600ml.', 'image' => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($n4->id, [['ing' => $ingredients['mineral_botol'], 'qty' => 1]]);

        // D. FOOD & DESSERT
        $f1 = Menu::create(['name' => 'Kentang Goreng', 'category' => 'Food', 'selling_price' => 15000, 'description' => 'Crispy french fries.', 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f1->id, [['ing' => $ingredients['kentang'], 'qty' => 120]]);

        $f2 = Menu::create(['name' => 'Indomie Polos', 'category' => 'Food', 'selling_price' => 8000, 'description' => 'Indomie goreng original.', 'image' => 'https://images.unsplash.com/photo-1612929633738-8fe01f7c845f?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f2->id, [['ing' => $ingredients['indomie'], 'qty' => 1]]);

        $f3 = Menu::create(['name' => 'Indomie Telur', 'category' => 'Food', 'selling_price' => 12000, 'description' => 'Indomie goreng + telur ceplok.', 'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f3->id, [['ing' => $ingredients['indomie'], 'qty' => 1], ['ing' => $ingredients['telur'], 'qty' => 1]]);

        $f4 = Menu::create(['name' => 'Indomie Double Polos', 'category' => 'Food', 'selling_price' => 15000, 'description' => 'Dua bungkus Indomie goreng.', 'image' => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f4->id, [['ing' => $ingredients['indomie'], 'qty' => 2]]);

        $f5 = Menu::create(['name' => 'Mie Nyemek', 'category' => 'Food', 'selling_price' => 15000, 'description' => 'Indomie kuah kental + telur.', 'image' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f5->id, [['ing' => $ingredients['indomie'], 'qty' => 1], ['ing' => $ingredients['telur'], 'qty' => 1]]);

        $f6 = Menu::create(['name' => 'Nasi Telur', 'category' => 'Food', 'selling_price' => 10000, 'description' => 'Nasi hangat + telur ceplok.', 'image' => 'https://images.unsplash.com/photo-1525648199074-cee30ba79a4a?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($f6->id, [['ing' => $ingredients['nasi'], 'qty' => 1], ['ing' => $ingredients['telur'], 'qty' => 1]]);

        $d1 = Menu::create(['name' => 'Salad Buah', 'category' => 'Dessert', 'selling_price' => 15000, 'description' => 'Fresh fruit salad.', 'image' => 'https://images.unsplash.com/photo-1490474418585-ba9bad8fd0ea?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($d1->id, [['ing' => $ingredients['salad_buah'], 'qty' => 1]]);

        $d2 = Menu::create(['name' => 'Tiramisu', 'category' => 'Dessert', 'selling_price' => 15000, 'description' => 'Classic tiramisu box.', 'image' => 'https://images.unsplash.com/photo-1571115177098-24edf4c66ff9?q=80&w=600&auto=format&fit=crop', 'is_active' => true]);
        $this->addRecipe($d2->id, [['ing' => $ingredients['tiramisu'], 'qty' => 1]]);
    }

    private function addRecipe($menuId, $items)
    {
        foreach ($items as $item) {
            Recipe::create([
                'menu_id'       => $menuId,
                'ingredient_id' => $item['ing']->id,
                'quantity'      => $item['qty'],
            ]);
        }
    }
}