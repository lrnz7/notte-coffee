<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'closing_time',
        'opening_cash',
        'system_cash_sales',
        'system_qris_sales',
        'physical_cash_count',
        'cash_difference',
        'notes',
    ];

    protected $casts = [
        'closing_time' => 'datetime',
        'opening_cash' => 'decimal:2',
        'system_cash_sales' => 'decimal:2',
        'system_qris_sales' => 'decimal:2',
        'physical_cash_count' => 'decimal:2',
        'cash_difference' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
