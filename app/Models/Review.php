<?php
// App\Models\Review.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'product_for',
        'users_name',
        'comment',
        'rating',
        'c_number',
        'status',
        'is_read',
    ];
}
