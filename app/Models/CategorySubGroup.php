<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorySubGroup extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'category_sub_groups';

    protected $fillable = [
        'category_group_id',
        'name', 
        'slug', 
        'description',
        'active',
    ];

    protected $dates = ['deleted_at'];

    public function categoryGroup()
    {
        return $this->belongsTo(CategoryGroup::class);
    }

    public function categorySubGroupOne()
    {
        return $this->hasMany(CategorySubGroupOne::class);
    }
    
    public function scopeAvailable($query)
    {
        return $query->where('active', '1'); 
    }
}
