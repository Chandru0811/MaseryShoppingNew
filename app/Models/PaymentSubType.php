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

    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
