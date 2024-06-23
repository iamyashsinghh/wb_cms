<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp_code',
        'request_otp_at',
        'login_at',
        'logout_at',
        'device_id',
        'ip_address',
        'status',
    ];
}
