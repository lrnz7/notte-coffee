<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'order_type',
        'payment_method',
        'status',
        'total_amount',
        'total_cogs',
        'gross_profit',
    ];

    // Relasi ke detail item pesanan
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}