<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    use HasFactory;

    protected $table = 'headers';

    protected $fillable = [
        'header_logo_name',
        'header_logo_path',
        'header_logo_extension',
        'header_logo_size'
    ];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)
                     ->select([
                        'approved_header_logo_name as header_logo_name',
                        'approved_header_logo_path as header_logo_path',
                        'approved_header_logo_extension as header_logo_extension',
                        'approved_header_logo_size as header_logo_size',
                     ]);
    }
}
