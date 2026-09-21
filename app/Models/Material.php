<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'unit_price', 'stock_quantity', 'min_stock_alert'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}