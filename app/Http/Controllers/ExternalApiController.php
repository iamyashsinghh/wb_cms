<?php

namespace App\Http\Controllers;

use App\Models\GoogleApiRecords;
use App\Models\Venue;
use Illuminate\Http\Request;

class ExternalApiController extends Controller
{

    public function ajax()
    {
        $GoogleApiRecords = GoogleApiRecords::select(
            'id',
            'total_requests',
            'created_at'
        );
        return datatables($GoogleApiRecords)->make(false);
    }
    public function list()
    {
        // $yash = Venue::select('id', 'city_id', 'location_place_id')
        // ->where('city_id', 1)
        // ->whereNotNull('location_place_id')
        // ->where('location_place_id', '<>', '')
        // ->get() ;
        // var_dump($yash);

        return view('external_api.list');
    }

    public function maps_review()
    {
        return view('external_api.maps_review');
    }

    public function maps_review_fetch(Request $request)
    {
        $request->validate([
            'place_id_api_key' => 'required|string',
        ]);

        $placeIdApiKey = $request->input('place_id_api_key');
        $api_key = $placeIdApiKey;
        $directory = storage_path('app/public/uploads/all_reviews');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $success = true;

        foreach (Venue::select('id', 'city_id', 'location_place_id')
            ->where('city_id', 1)
            ->whereNotNull('location_place_id')
            ->where('location_place_id', '<>', '')
            ->get() as $venue) {
            $success = $success && $this->fetchAndSaveReviews($api_key, $venue->location_place_id, $directory);
        }

        $venues = Venue::select('id', 'city_id', 'location_place_id')
            ->where('city_id', 1)
            ->whereNotNull('location_place_id')
            ->where('location_place_id', '<>', '')
            ->get();
        $venueCount = count($venues);
        GoogleApiRecords::create([
            'total_requests' => $venueCount,
        ]);
        if ($success) {
            $msg = 'Reviews fetched and saved successfully.';
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } else {
            $msg = 'Error fetching or saving reviews.';
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $msg]);
        }

        return redirect()->route('api.maps_review')->with('success', 'Data submitted successfully.');
    }

    private function fetchAndSaveReviews($api_key, $place_id, $directory)
    {
        $api_url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$place_id&fields=name,rating,reviews&key=$api_key";
        $response = \Http::get($api_url);

        if ($response->successful()) {
            $result = $response->json();
            if ($result && $result['status'] === 'OK') {
                $place_name = $result['result']['name'];
                $place_rating = $result['result']['rating'];
                $reviews = $result['result']['reviews'];
                $json_data = json_encode(['place_name' => $place_name, 'place_rating' => $place_rating, 'reviews' => $reviews], JSON_PRETTY_PRINT);
                $file_path = "$directory/{$place_id}_reviews.json";
                file_put_contents($file_path, $json_data);
                return true;
            }
        }
    }

}