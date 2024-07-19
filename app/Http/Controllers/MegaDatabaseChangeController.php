<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\Venue;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MegaDatabaseChangeController extends Controller
{
    public function rename_all_venue_remove_locality_and_city_from_venue_name()
    {
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


    public function convert_all_the_localities_into_group()
    {
        Location::where('city_id', 1)->update(['is_group' => 1]);
    }




    public function getLocationCoordinates()
    {
        $apiKey = 'AIzaSyBrWQqxRrVwgEDFYZdiC_nHlBE0pn5cjTw';

        $locations = Location::where('latitude', '')->get();
        $client = new Client();

        foreach ($locations as $location) {
            $venue = Venue::where('location_id', $location->id)->first();

            if ($venue) {
                $address = $venue->venue_address;
                $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'query' => [
                        'address' => $address,
                        'key' => $apiKey
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                if ($data['status'] == 'OK') {
                    $geoLocation = $data['results'][0]['geometry']['location'];
                    $location->latitude = $geoLocation['lat'];
                    $location->longitute = $geoLocation['lng'];
                    $location->save();

                    Log::info('Updated location coordinates', [
                        'location_id' => $location->id,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude
                    ]);
                } else {
                    Log::error('Geocoding failed', [
                        'location_id' => $location->id,
                        'status' => $data['status']
                    ]);
                }
            } else {
                Log::warning('No venue found for location', ['location_id' => $location->id]);
            }
        }
    }

    public function get_address()
    {
        return Location::where('id', '>', 4)->where('city_id', 1)->count();
    }


    public function updateNearbyLocations()
    {
        set_time_limit(300);
        $locations = Location::where('id', '>', 4)->where('city_id', 1)->get();
        $otherLoc = Location::where('id', '>', 4)->where('city_id', 1)->get();
        foreach ($locations as $location) {
            $nearbyLocationIds = [];
            $nearbyLocationNames = [];

            foreach ($otherLoc as $otherLocation) {
                if ($location->id !== $otherLocation->id) {
                    $distance = $this->getDistanceByRoad(
                        $location->latitude,
                        $location->longitute,
                        $otherLocation->latitude,
                        $otherLocation->longitute
                    );

                    // Define the radius within which you consider locations to be nearby (in kilometers)
                    $radius = 6;

                    if ($distance >= 0.01 && $distance <= $radius) {
                        $nearbyLocationIds[] = $otherLocation->id;
                        $nearbyLocationNames[] = $otherLocation->name;
                    }

                    // Log the distance for debugging
                    Log::info('Distance calculated by road', [
                        'location_id' => $location->id,
                        'other_location_id' => $otherLocation->id,
                        'other_location_name' => $otherLocation->name,
                        'distance' => $distance
                    ]);
                }
            }

            $location->locality_ids = implode(',', $nearbyLocationIds);
            $location->save();

            Log::info('Updated nearby locations', [
                'location_id' => $location->id,
                'nearby_location_ids' => $nearbyLocationIds,
                'nearby_location_names' => $nearbyLocationNames
            ]);
                break;
        }
    }

    private function getDistanceByRoad($lat1, $lon1, $lat2, $lon2)
    {
        $client = new Client();
        $apiKey = 'AIzaSyBrWQqxRrVwgEDFYZdiC_nHlBE0pn5cjTw';
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$lat1},{$lon1}&destinations={$lat2},{$lon2}&key={$apiKey}";
        Log::info($url);
        try {
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);
            Log::info($data);
            if (isset($data['rows'][0]['elements'][0]['status']) && $data['rows'][0]['elements'][0]['status'] == 'OK') {
                if (isset($data['rows'][0]['elements'][0]['distance']['value'])) {
                    $distance = $data['rows'][0]['elements'][0]['distance']['value'] / 1000; // Convert meters to kilometers
                    return $distance;
                } else {
                    Log::error('Distance key is missing in the response', [
                        'response' => $data
                    ]);
                }
            } else {
                Log::error('Error in the API response status', [
                    'response' => $data
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception when calling Google Maps API', [
                'exception' => $e->getMessage()
            ]);
        }

        return null;
    }



    // public function updateNearbyLocations()
    // {
    //     $locations = Location::where('id', '>', 4)->get();

    //     foreach ($locations as $location) {
    //         $nearbyLocationIds = [];
    //         $nearbyLocationNames = [];

    //         foreach ($locations as $otherLocation) {
    //             if ($location->id !== $otherLocation->id) {
    //                 $distance = $this->calculateDistance(
    //                     (float) $location->latitude,
    //                     (float) $location->longitude,
    //                     (float) $otherLocation->latitude,
    //                     (float) $otherLocation->longitude
    //                 );

    //                 // Define the radius within which you consider locations to be nearby (in kilometers)
    //                 $radius = 5;

    //                 if ($distance <= $radius) {
    //                     $nearbyLocationIds[] = $otherLocation->id;
    //                     $nearbyLocationNames[] = $otherLocation->name;
    //                 }

    //                 // Log the distance for debugging
    //                 Log::info('Distance calculated', [
    //                     'location_id' => $location->id,
    //                     'other_location_id' => $otherLocation->id,
    //                     'other_location_name' => $otherLocation->name,
    //                     'distance' => $distance
    //                 ]);
    //             }
    //         }

    //         $location->locality_ids = implode(',', $nearbyLocationIds);
    //         $location->save();

    //         Log::info('Updated nearby locations', [
    //             'location_id' => $location->id,
    //             'nearby_location_ids' => $nearbyLocationIds,
    //             'nearby_location_names' => $nearbyLocationNames
    //         ]);
    //     }
    // }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in kilometers

        $latDistance = deg2rad($lat2 - $lat1);
        $lonDistance = deg2rad($lon2 - $lon1);

        $a = sin($latDistance / 2) * sin($latDistance / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDistance / 2) * sin($lonDistance / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}
