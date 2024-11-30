<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoryId',
        'name',
        
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategoryId');
    }
}