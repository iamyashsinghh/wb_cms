<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class C_Number extends Model
{
    use HasFactory;

    protected $table = 'company_numbers';

    protected $fillable = [
        'tata_numbers',
        'is_next',
        'id'
    ];
}
