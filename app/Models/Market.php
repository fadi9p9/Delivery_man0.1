<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'title',
        'location',
        'img',
        'rate',
        'rating'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'productId');
    }

    // 
    // علاقة المتجر بالتصنيفات (عبر Subcategories والمنتجات)
    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            Product::class,
            'market_id',       // المفتاح الأجنبي في Products الذي يشير إلى Market
            'id',              // المفتاح الأساسي في Category
            'id',              // المفتاح الأساسي في Market
            'subcategory_id'   // المفتاح الأجنبي في Products الذي يشير إلى Subcategory
        );
    }
}
