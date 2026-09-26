<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'ingredient_id',
        'quantity',
        'quantity_required'
    ];

    // ALIAS SUPAYA VIEW ADMIN TIDAK ERROR
    public function getQuantityRequiredAttribute()
    {
        return $this->quantity;
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Dipakai oleh Controller & Hitung HPP
    public function material()
    {
        return $this->belongsTo(Material::class, 'ingredient_id');
    }

    // ALIAS WAJIB BIAR VIEW BLADE LU GAK BLANK "Bahan Tidak Ditemukan"
    public function ingredient()
    {
        return $this->belongsTo(Material::class, 'ingredient_id');
    }
}