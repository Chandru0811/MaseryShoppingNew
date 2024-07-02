<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    const STATUS_WAITING_FOR_PAYMENT    = 1;    // Default
    const STATUS_PAYMENT_ERROR          = 2;
    const STATUS_CONFIRMED              = 3;
    const STATUS_FULFILLED              = 4;   // All status before paid value consider as unfulfilled and bottoms are fulfilled
    const STATUS_AWAITING_DELIVERY      = 5;
    const STATUS_DELIVERED              = 6;
    const STATUS_RETURNED               = 7;

    const PAYMENT_STATUS_UNPAID             = 1;       // Default
    const PAYMENT_STATUS_PENDING            = 2;
    const PAYMENT_STATUS_PAID               = 3;      // All status before paid value consider as unpaid
    const PAYMENT_STATUS_INITIATED_REFUND   = 4;
    const PAYMENT_STATUS_PARTIALLY_REFUNDED = 5;
    const PAYMENT_STATUS_REFUNDED           = 6;

    protected $dates = ['created_at', 'deleted_at', 'shipping_date', 'delivery_date', 'payment_date'];

    protected $fillable = [
        'order_number',
        'customer_id',
        'ship_to',
        'shipping_zone_id',
        'shipping_rate_id',
        'packaging_id',
        'item_count',
        'quantity',
        'shipping_weight',
        'taxrate',
        'total',
        'discount',
        'shipping',
        'packaging',
        'handling',
        'taxes',
        'grand_total',
        'billing_address',
        'shipping_address',
        'shipping_date',
        'delivery_date',
        'tracking_id',
        'coupon_id',
        'carrier_id',
        'message_to_customer',
        'send_invoice_to_customer',
        'admin_note',
        'buyer_note',
        'payment_method_id',
        'payment_date',
        'payment_status',
        'order_status_id',
        'goods_received',
        'approved',
        'feedback_id',
        'disputed',
        'email'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Customer',
        ]);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'order_items')
        ->withPivot('item_description', 'quantity', 'unit_price','feedback_id')->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
        ->withPivot('item_description', 'quantity', 'unit_price', 'feedback_id')->withTimestamps();
    }

    public function calculate_grand_total()
    {
        return ($this->total + $this->handling + $this->taxes + $this->shipping + $this->packaging) - $this->discount;
    }
    
    public function isPaid()
    {
        return $this->payment_status >= static::PAYMENT_STATUS_PAID;
    }

    public function refundedSum()
    {
        return $this->refunds->where('status', Refund::STATUS_APPROVED)->sum('amount');
    }

    public function goods_received()
    {
        return $this->update(['order_status_id' => 6, 'goods_received' => 1]); // Delivered Status. This id is freezed by system config
    }

    public function orderStatus($plain = False)
    {
        $order_status = strtoupper(get_order_status_name($this->order_status_id));

        if( $plain )
            return $order_status;

        switch ($this->order_status_id) {
            case static::STATUS_WAITING_FOR_PAYMENT:
            case static::STATUS_PAYMENT_ERROR:
            case static::STATUS_RETURNED:
                return '<span class="label label-danger">' . $order_status . '</span>';

            case static::STATUS_CONFIRMED:
            case static::STATUS_AWAITING_DELIVERY:
                return '<span class="label label-outline">' . $order_status . '</span>';

            case static::STATUS_FULFILLED:
                return '<span class="label label-info">' . $order_status . '</span>';

            case static::STATUS_DELIVERED:
                return '<span class="label label-primary">' . $order_status . '</span>';
        }
    }

    public function paymentStatusName($plain = False)
    {
        $payment_status = strtoupper(get_payment_status_name($this->payment_status));

        if( $plain )
            return $payment_status;

        switch ($this->payment_status) {
            case static::PAYMENT_STATUS_UNPAID:
            case static::PAYMENT_STATUS_REFUNDED:
            case static::PAYMENT_STATUS_PARTIALLY_REFUNDED:
                return '<span class="label label-danger">' . $payment_status . '</span>';

            case static::PAYMENT_STATUS_PENDING:
            case static::PAYMENT_STATUS_INITIATED_REFUND:
                return '<span class="label label-info">' . $payment_status . '</span>';

            case static::PAYMENT_STATUS_PAID:
                return '<span class="label label-outline">' . $payment_status . '</span>';
        }
    }
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
