<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'contact_us';

    protected $fillable = [
        'phone',
        'email',
        'address',
        'timing',
        'maplink',
        'heading',
        'content'
    ];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)
                     ->select([
                        'approved_phone as phone',
                        'approved_email as email',
                        'approved_address as address',
                        'approved_timing as timing',
                        'approved_maplink as maplink',
                        'approved_heading as heading',
                        'approved_content as content',
                     ]);
    }
}
