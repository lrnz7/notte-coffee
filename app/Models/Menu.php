<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'selling_price',
        'image',
        'is_active',
    ];

    // Relasi ke tabel recipes
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // Pakai belongsToMany biar sinkron sama MenuController, HANYA panggil kolom 'quantity'
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'recipes', 'menu_id', 'ingredient_id')
                    ->withPivot('quantity');
    }

    // OTOMATIS HITUNG HPP BERDASARKAN BAHAN BAKU (MATERIAL)
    public function getCalculatedHppAttribute()
    {
        $totalHpp = 0;
        foreach ($this->recipes as $recipe) {
            if ($recipe->material) {
                // Pakai unit_price dari tabel materials
                $qty = $recipe->quantity ?? 0;
                $totalHpp += ($qty * $recipe->material->unit_price);
            }
        }
        
        return $totalHpp > 0 ? $totalHpp : ($this->selling_price * 0.4);
    }
}