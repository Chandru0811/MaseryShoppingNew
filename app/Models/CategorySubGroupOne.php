<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorySubGroupOne extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_sub_groups_one';

    protected $fillable = [
        'category_sub_group_id',
        'name', 
        'slug', 
        'description',
        'active',
    ];

    protected $dates = ['deleted_at'];

    public function categorySubGroup()
    {
        return $this->belongsTo(CategorySubGroup::class);
    }

    public function categorySubGroupTwo()
    {
        return $this->hasMany(CategorySubGroupTwo::class);
    }
}
