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

    public function approve()
    {
        $this->approved_header_logo_name = $this->header_logo_name;
        $this->approved_header_logo_path = $this->header_logo_path;
        $this->approved_header_logo_extension = $this->header_logo_extension;
        $this->approved_header_logo_size = $this->header_logo_size;
        $this->is_approved = true;
        $this->save();
    }
}
