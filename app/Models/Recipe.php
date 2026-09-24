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
    ];

    // ALIAS SUPAYA VIEW ADMIN TIDAK ERROR KETIKA MEMANGGIL quantity_required
    public function getQuantityRequiredAttribute()
    {
        return $this->quantity;
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}