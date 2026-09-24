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

    // Relasi ke Resep
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // ALIAS SUPAYA CONTROLLER ADMIN GAK ERROR (materials)
    public function materials()
    {
        return $this->hasMany(Recipe::class);
    }

    // OTOMATIS HITUNG HPP BERDASARKAN BAHAN BAKU & STOK
    public function getCalculatedHppAttribute()
    {
        $totalHpp = 0;
        foreach ($this->recipes as $recipe) {
            if ($recipe->ingredient) {
                $totalHpp += ($recipe->quantity * $recipe->ingredient->cost_per_unit);
            }
        }
        
        // Jika belum ada resep, gunakan estimasi aman 40% dari harga jual
        return $totalHpp > 0 ? $totalHpp : ($this->selling_price * 0.4);
    }
}