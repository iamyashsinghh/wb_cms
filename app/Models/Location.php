<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model {
    use HasFactory, SoftDeletes;

    // public function get_group_localities(){ //not in use
    //     $locality_id_arr = explode(",", $this->locality_ids);
    //     return Location::select('id', 'name')->whereIn('id', $locality_id_arr)->get();
    // }
}
