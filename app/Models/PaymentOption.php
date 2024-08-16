<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'description' => 'json',  // Cast description as JSON
        'is_active' => 'boolean',
    ];

    public function paymentSubTypes()
    {
        return $this->hasMany(PaymentSubType::class);
    }
}
