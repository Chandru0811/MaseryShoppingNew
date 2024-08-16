<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

    protected $primaryKey = 'id';

    protected $fillable = [
        'inventory_id',
        'product_id',
        'customer_id',
        'ip_address'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMine($query)
    {
        $customer_id = \Auth::guard('api')->check() ? \Auth::guard('api')->user()->id : null;
        $ip_address = request()->ip(); 
        
        return $query->where('customer_id', $customer_id)->orwhere('ip_address',$ip_address);
        
    }
}
