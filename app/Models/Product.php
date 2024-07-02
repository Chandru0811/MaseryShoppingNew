<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'brand',
        'category_id',
        'name',
        'slug',
        'model_number',
        'mpn',
        'gtin',
        'gtin_type',
        'min_price',
        'max_price',
        'requires_shipping',
        'description',
    ];
    
    protected $dates = ['deleted_at'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
    
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    
}
