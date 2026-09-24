<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'cost_per_unit',
        'stock',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}