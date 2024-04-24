<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BusinessUser extends Model {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public function get_vendor_category() {
        return $this->hasOne(VendorCategory::class, 'id', 'business_category_id');
    }
    public function get_venue_category() {
        return $this->hasOne(VenueCategory::class, 'id', 'business_category_id');
    }

    public function getVendorContent() {
        return $this->hasOne(VendorUserContent::class, 'vendor_id', 'migrated_business_id');
    }

    public function getVenueContent() {
        return $this->hasOne(VenueUserContent::class, 'venue_id', 'migrated_business_id');
    }

    public function getCity() {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
}
