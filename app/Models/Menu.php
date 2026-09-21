<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Accessor untuk hitung HPP dinamis disesuaikan dengan kolom unit_price database
    public function getCalculatedHppAttribute()
    {
        return $this->materials->sum(function ($material) {
            $qty = $material->pivot->quantity_required ?? 0;
            $price = $material->unit_price ?? 0; // Menyesuaikan dengan kolom unit_price di tabel materials
            return $qty * $price;
        });
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'menu_materials')
                    ->withPivot('quantity_required');
    }
}