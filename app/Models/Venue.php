<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model {
    use HasFactory, SoftDeletes;
    public function reviews()
    {
        return $this->belongsToMany(Review::class, 'vendor_venue_review', 'venue_id', 'review_id');
    }
    public function get_city() {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    public function get_locality() {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }
    public function get_category()
{
    $firstCategoryId = $this->venue_category_ids[0] ?? null;
    return $this->hasOne(VenueCategory::class, 'id', 'venue_category_ids')->where('id', $firstCategoryId);
}
}
