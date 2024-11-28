<?php

namespace App\Models;

use Database\Seeders\productImageSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategoryId',
        'title',
        'description',
        'price',
        'size',
        'discount',
        'totalQuantity',
        'rate',
        'rating',
        'marketId',
    ];

    // Relationships
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategoryId');
    }

    public function images()
    {
        return $this->hasMany(productImage::class, 'productId');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'productId');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'productId');
    }
    public function market()
    {
        return $this->belongsTo(Market::class, 'marketId');
    }
}
