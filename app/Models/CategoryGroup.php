<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryGroup extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_groups';

    protected $fillable = [
        'name', 
        'slug', 
        'description',
        'icon',
        'active',
        'order'
    ];

    protected $dates = ['deleted_at'];

    public function categorySubGroup()
    {
        return $this->hasMany(CategorySubGroup::class);
    }
}
