<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];
    protected $withCount = ['inventories'];

    protected $fillable = [
        'customer_id',
        'ip_address',
        'ship_to',
        'shipping_zone_id',
        'shipping_rate_id',
        'packaging_id',
        'taxrate',
        'item_count',
        'quantity',
        'total',
        'discount',
        'shipping',
        'packaging',
        'handling',
        'taxes',
        'grand_total',
        'shipping_weight',
        'shipping_address',
        'billing_address',
        'coupon_id',
        'payment_method_id',
        'payment_status',
        'message_to_customer',
        'admin_note',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Customer',
        ]);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'cart_items')
        ->withPivot('item_description', 'quantity', 'unit_price')->withTimestamps();
    }

    public function get_shipping_cost()
    {
        return $this->is_free_shipping() ? 0 : $this->shipping + $this->handling;
    }

    public function grand_total()
    {
        if($this->is_free_shipping())
            return ($this->total + $this->taxes + $this->packaging) - $this->discount;

        return ($this->total + $this->handling + $this->taxes + $this->shipping + $this->packaging) - $this->discount;
    }

    public function is_free_shipping()
    {
        if( ! $this->shipping_rate_id )
            return TRUE;

        return FALSE;
    }

}
