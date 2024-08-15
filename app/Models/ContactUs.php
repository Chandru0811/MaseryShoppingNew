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

    public function approve()
    {
        $this->approved_phone = $this->phone;
        $this->approved_email = $this->email;
        $this->approved_address = $this->address;
        $this->approved_timing = $this->timing;
        $this->approved_maplink = $this->maplink;
        $this->approved_heading = $this->heading;
        $this->approved_content = $this->content;
        $this->is_approved = true;
        $this->save();
    }
}
