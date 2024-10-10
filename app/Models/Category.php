<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'category_sub_group_two_id',
        'name',
        'slug',
        'description',
        'active',
        'featured'
    ];

    protected $dates = ['deleted_at'];

    public function categorySubGroupTwo()
    {
        return $this->belongsTo(CategorySubGroupTwo::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class);
    }
}
