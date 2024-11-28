<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cartId',
        'orderLocation',
        'customerId',
        'deliveryId',
    ];

    // Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cartId');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customerId');
    }

    public function delivery()
    {
        return $this->belongsTo(User::class, 'deliveryId');
    }
}