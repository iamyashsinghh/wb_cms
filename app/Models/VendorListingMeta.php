<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorListingMeta extends Model
{
    protected $table = 'vendor_listing_metas';

    protected $guarded = [];

    public $timestamps = true; // to allow created_at and updated_at to auto-fill
}
