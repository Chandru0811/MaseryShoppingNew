<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address','user_id','title', 'message', 'type', 'status','is_sent','is_read', 'read_at','priority'];
}
