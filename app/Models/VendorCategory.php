<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorCategory extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'vendor_categories';
    protected $guarded = [];
}
