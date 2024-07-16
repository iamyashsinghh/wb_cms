<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\Venue;
use GuzzleHttp\Client;
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


    public function convert_all_the_localities_into_group(){
        Location::where('city_id', 1)->update(['is_group' => 1]);
    }


public function getLocationCoordinates($location = 'Rohini')
{
    $apiKey = 'AIzaSyBrWQqxRrVwgEDFYZdiC_nHlBE0pn5cjTw';
    $address = Venue::where('location_id', 30)->first()->venue_address;
    $client = new Client();
    $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
        'query' => [
            'address' => $address,
            'key' => $apiKey
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if ($data['status'] == 'OK') {
        $location = $data['results'][0]['geometry']['location'];
        return [
            'latitude' => $location['lat'],
            'longitude' => $location['lng']
        ];
    } else {
        return ['error' => 'Geocoding failed: ' . $data['status']];
    }
}
}
