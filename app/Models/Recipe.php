<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = ['menu_id', 'material_id', 'amount_needed'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}