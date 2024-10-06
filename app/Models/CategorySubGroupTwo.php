<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorySubGroupTwo extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_sub_groups_two';

    protected $fillable = [
        'category_sub_group_one_id',
        'name', 
        'slug', 
        'description',
        'active',
    ];

    protected $dates = ['deleted_at'];

    public function categorySubGroupOne()
    {
        return $this->belongsTo(CategorySubGroupOne::class);
    }

    public function category()
    {
        return $this->hasMany(Category::class);
    }
}
