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
}
