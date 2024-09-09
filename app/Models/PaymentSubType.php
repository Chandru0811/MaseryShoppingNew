<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSubType extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_option_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
