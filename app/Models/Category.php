<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'img',
    ];

    // Relationships
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'categoryId');
    }

    // new modified relationships 
    public function markets()
    {
        return $this->hasManyThrough(
            Market::class,    // النموذج النهائي
            Product::class,   // النموذج الوسيط
            'category_id',    // المفتاح الأجنبي في Subcategories الذي يشير إلى Category
            'id',             // المفتاح الأساسي في Market
            'id',             // المفتاح الأساسي في Category
            'market_id'       // المفتاح الأجنبي في Products الذي يشير إلى Market
        );
    }
}