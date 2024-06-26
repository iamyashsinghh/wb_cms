<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\Venue;
use Illuminate\Http\Request;

class MegaDatabaseChangeController extends Controller
{
    public function rename_all_venue_remove_locality_and_city_from_venue_name() {
        // Retrieve all venues, localities, and cities
        $venues = Venue::select('id', 'name', 'location_id', 'city_id')->get();
        $localities = Location::select('id', 'name', 'slug')->get();
        $cities = City::select('id', 'name', 'slug')->get();

        // Create lookup arrays for localities and cities
        $localityLookup = $localities->pluck('name', 'id');
        $cityLookup = $cities->pluck('name', 'id');

        // Iterate over each venue to modify the name
        foreach ($venues as $venue) {
            $originalName = $venue->name;
            $localityName = $localityLookup->get($venue->location_id, '');
            $cityName = $cityLookup->get($venue->city_id, '');

            // Remove locality, city names, and commas from the venue name
            $newName = str_replace([$localityName, $cityName, ','], '', $originalName);
            $newName = trim($newName); // Remove any leading or trailing whitespace

            // Update the venue name if it has changed
            if ($originalName !== $newName) {
                $venue->name = $newName;
                $venue->save();
            }
        }
    }


}
