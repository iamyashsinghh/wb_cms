<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class whatsappMessages extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'whatsapp_messages';
    protected $guarded = [];
}
