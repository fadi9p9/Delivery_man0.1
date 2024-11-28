<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cartId',
        'productId',
        'quantity',
    ];

    // Relationships
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cartId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }
}