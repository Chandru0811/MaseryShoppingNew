<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Common\Imageable;
use Carbon\Carbon;


class Inventory extends Model
{
    use HasFactory, Imageable;

    protected $fillable = [
        'title',
        'product_id',
        'brand_id',
        'category_id',
        'sku',
        'condition',
        'condition_note',
        'description',
        'key_features',
        'stock_quantity',
        'damaged_quantity',
        'user_id',
        'purchase_price',
        'sale_price',
        'offer_price',
        'offer_start',
        'offer_end',
        'shipping_weight',
        'free_shipping',
        'available_from',
        'min_order_quantity',
        'slug',
        'linked_items',
        'stuff_pick',
    ];

    public function mine()
    {
        // Your implementation here
        // For example, return inventories that belong to the authenticated user
        return $this->where('user_id', auth()->id())->get();
    }

    public function scopeAvailable($query)
    {
        return $query->where('active', 1);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function currnt_sale_price()
    {
        if ($this->hasOffer())
            return $this->offer_price;

        return $this->sale_price;
    }

    public function hasOffer()
    {
        if (
            ($this->offer_price > 0) &&
            ($this->offer_price < $this->sale_price) &&
            ($this->offer_start < Carbon::now()) &&
            ($this->offer_end > Carbon::now())
        )
            return TRUE;

        return FALSE;
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'LIKE', '%' . $term . '%')
            ->orWhere('description', 'LIKE', '%' . $term . '%')
            ->orWhere('slug', 'LIKE', '%' . $term . '%');
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
            ->withPivot('item_description', 'quantity', 'unit_price')->withTimestamps();
    }
}
