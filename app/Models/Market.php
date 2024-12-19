<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;
    protected $table = 'markets';
    protected $fillable = [
        'userId',
        'title',
        'description',
        'location',
        'img',
        'rating',
        'rating_count'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'marketId');
    }

    // 
    // علاقة المتجر بالتصنيفات (عبر Subcategories والمنتجات)
    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            Product::class,
            'marketId',       // المفتاح الأجنبي في Products الذي يشير إلى Market
            'id',              // المفتاح الأساسي في Category
            'id',              // المفتاح الأساسي في Market
            'subcategoryId'   // المفتاح الأجنبي في Products الذي يشير إلى Subcategory
        );
    }
}
