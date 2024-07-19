<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'vendor_venue_review', 'vendor_id', 'review_id');
    }

    public function get_city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    public function get_locality()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }
    public function get_category()
    {
        return $this->hasOne(VendorCategory::class, 'id', 'vendor_category_id');
    }
}
