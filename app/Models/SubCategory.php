<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = "sub_categories";

    protected $primaryKey  = "id";
    
    protected $fillable = ['name', 'slug', 'description', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}