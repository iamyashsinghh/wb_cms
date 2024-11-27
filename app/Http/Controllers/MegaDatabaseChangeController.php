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


    public function updateNearbyLocations($location_id)
    {
        set_time_limit(300);
        $location = Location::where('id', '>', $location_id)->where('city_id', 1)->first();
        $otherLoc = Location::where('id', '>', 4)->where('city_id', 1)->get();
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
                $radius = 5;

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
        return "<a href=\"https://cms.wbcrm.in/hi_done/$location->id\">Open Link</a>";
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


    public function remove_five_star()
    {
        $venues = Venue::whereRaw("FIND_IN_SET(?, venue_category_ids)", [7])->get();

        foreach ($venues as $venue) {
            // Split the string into an array
            $categories = explode(',', $venue->venue_category_ids);

            // Remove the category with ID 7
            $categories = array_filter($categories, fn($id) => $id != 7);

            // Update the venue with the modified categories
            $venue->update(['venue_category_ids' => implode(',', $categories)]);
        }

        return response()->json(['message' => 'Five-star category removed successfully!']);
    }

    public function massUpdateVenueMeals(Request $request)
{

    $veg_templates = [
        [
            'Chaat Counter' => 3,
            'Live Counter' => 2,
            'Welcome Drinks' => 4,
            'Soups' => 2,
            'Veg Starter' => 8,
            'Veg Main Courses' => 6,
            'Salads' => 6,
            'Raita' => 1,
            'Dal' => 1,
            'Rice/Biryani' => 2,
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 3,
        ],
        [
            'Chaat Counter' => 4,
            'Live Counter' => 2,
            'Welcome Drinks' => 3,
            'Soups' => 3,
            'Veg Starter' => 12,
            'Veg Main Courses' => 8,
            'Salads' => 5,
            'Raita' => 2,
            'Dal' => 2,
            'Rice/Biryani' => 2,
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 4,
        ],
        [
            'Chaat Counter' => 5,
            'Live Counter' => 3,
            'Welcome Drinks' => 5,
            'Soups' => 2,
            'Veg Starter' => 14,
            'Veg Main Courses' => 8,
            'Salads' => 4,
            'Raita' => 2,
            'Dal' => 2,
            'Rice/Biryani' => 2,
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 4,
        ],
    ];
    $nonveg_templates = [
        [
            'Chaat Counter' => 3,
            'Live Counter' => 2,
            'Welcome Drinks' => 4,
            'Veg / Non Veg Soup' => "1 + 1",
            'Veg / Non Veg Starter' => "6 + 3",
            'Veg / Non Veg Main Courses' => "3 + 3",
            'Salads' => 6,
            'Raita' => 1,
            'Dal' => 1,
            'Rice/Biryani/Non veg Biryani' => "1 + 1",
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 3,
        ],
        [
            'Chaat Counter' => 4,
            'Live Counter' => 2,
            'Welcome Drinks' => 3,
            'Veg / Non Veg Soup' => "2 + 1",
            'Veg / Non Veg Starter' => "8 + 4",
            'Veg / Non Veg Main Courses' => "6 + 3",
            'Salads' => 6,
            'Raita' => 2,
            'Dal' => 2,
            'Rice/Biryani/Non veg Biryani' => "2 + 1",
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 4,
        ],
        [
            'Chaat Counter' => 5,
            'Live Counter' => 3,
            'Welcome Drinks' => 5,
            'Veg / Non Veg Soup' => "2 + 2",
            'Veg / Non Veg Starter' => "10 + 4",
            'Veg / Non Veg Main Courses' => "6 + 4",
            'Salads' => 6,
            'Raita' => 2,
            'Dal' => 2,
            'Rice/Biryani/Non veg Biryani' => "3 + 1",
            'Assorted Breads/Rotis' => 6,
            'Desserts' => 4,
        ],
    ];

    $venueIds = Venue::pluck('id')->toArray();
    if (!is_array($venueIds) || empty($venueIds)) {
        session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => 'No venues selected for update.']);
        return redirect()->back();
    }
    foreach ($venueIds as $venueId) {
        $venue = Venue::find($venueId);
        if (!$venue) {
            continue;
        }
        $rand_no = rand(0, 2);
        $selected_veg_template = $veg_templates[$rand_no];
        $selected_nonveg_template = $nonveg_templates[$rand_no];

        $veg_food_arr = [];
        foreach ($selected_veg_template as $name => $package) {
            $veg_food_arr[] = ['name' => $name, 'package' => $package];
        }
        $venue->veg_foods = json_encode($veg_food_arr);

        $nonveg_food_arr = [];
        foreach ($selected_nonveg_template as $name => $package) {
            $nonveg_food_arr[] = ['name' => $name, 'package' => $package];
        }
        $venue->nonveg_foods = json_encode($nonveg_food_arr);

        $venue->save();
    }

    return ['success' => true, 'alert_type' => 'success', 'message' => 'Venue meals updated successfully for selected venues.'];

}

}
