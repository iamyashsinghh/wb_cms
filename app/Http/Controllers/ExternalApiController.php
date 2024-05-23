<?php

namespace App\Http\Controllers;

use App\Models\GoogleApiRecords;
use App\Models\Review;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    Log::info('maps_review_fetch called');

    $request->validate([
        'place_id_api_key' => 'required|string',
    ]);

    $api_key = $request->input('place_id_api_key');
    $directory = storage_path('app/public/uploads/all_reviews');

    if (!file_exists($directory)) {
        if (!mkdir($directory, 0777, true)) {
            Log::error('Failed to create directory: ' . $directory);
            return redirect()->route('api.maps_review')->with('error', 'Failed to create directory.');
        }
    }

    $success = true;

    $venues = Venue::select('id', 'city_id', 'location_place_id')
        ->where('city_id', 1)
        ->whereNull('place_rating')
        ->whereNotNull('location_place_id')
        ->limit(10)
        ->get();
        Log::info($venues);
        
    foreach ($venues as $venue) {
        Log::info('Fetching reviews for venue ID: ' . $venue->id);
        $success = $success && $this->fetchAndSaveReviews($api_key, $venue->location_place_id, $directory);
    }

    $venueCount = $venues->count();
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
    Log::info($api_url);

    if ($response->successful()) {
        $result = $response->json();
        if ($result['status'] == 'NOT_FOUND' || $result['status'] == 'INVALID_REQUEST' || $result['status'] == 'REQUEST_DENIED') {
            return true;
        }
        if ($result && $result['status'] === 'OK') {
            $place_name = $result['result']['name'];
            $place_rating = $result['result']['rating'] ?? null; // Use null coalescing operator
            $reviews = $result['result']['reviews'] ?? null; // Use null coalescing operator

            if ($place_rating === null || $reviews === null) {
                Log::info('Rating or reviews not present for place_id: ' . $place_id);
                return true;
            }

            $json_data = json_encode(['place_name' => $place_name, 'place_rating' => $place_rating, 'reviews' => $reviews], JSON_PRETTY_PRINT);
            $file_path = "$directory/{$place_id}_reviews.json";
            if (file_put_contents($file_path, $json_data) === false) {
                Log::error('Failed to write file: ' . $file_path);
                return false;
            }

            $venue = Venue::where('location_place_id', $place_id)->first();
            if ($venue) {
                $venue->place_rating = $place_rating;
                $venue->save();

                foreach ($reviews as $review_data) {
                    $review = new Review();
                    $review->product_id = $venue->id;
                    $review->product_for = 'venue';
                    $review->users_name = $review_data['author_name'];
                    $review->rating = $review_data['rating'];
                    $review->comment = $review_data['text'];
                    $review->status = '1';
                    $review->profile_pic = $review_data['profile_photo_url'];
                    $review->is_read = '1';
                    $review->save();
                }
                return true;
            } else {
                Log::error('Venue not found for place_id: ' . $place_id);
            }
        } else {
            Log::error('API response status not OK: ' . $result['status']);
        }
    } else {
        Log::error('Failed API request: ' . $response->body());
    }
    return false;
}


}
