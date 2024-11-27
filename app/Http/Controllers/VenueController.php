<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BusinessUser;
use App\Models\City;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Venue;
use App\Models\C_Number;
use App\Models\VenueCategory;
use App\Models\VenueUserContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VenueController extends Controller
{

    public function ajax_list()
    {
        $venues = Venue::select(
            'venues.id',
            'venues.name',
            'venues.phone',
            'cities.name as city',
            'locations.name as locality',
            'venues.wb_assured',
            'venues.popular',
            'venues.status',
            'venues.images',
            'venues.updated_by',
            'venues.created_by',
            'venues.updated_at',
            'venues.created_at',
            'venues.id as action',
        )->join('cities', 'cities.id', 'venues.city_id')
            ->join('locations', 'locations.id', 'venues.location_id');
        return datatables($venues)->make(false);
    }
    public function manage($venue_id = 0)
    {
        $venue_categories = VenueCategory::select('id', 'name')->orderby('name', 'asc')->get();
        $cities = City::select('id', 'name')->orderby('name', 'asc')->get();
        $veg_meals = Meal::select('id', 'category_id', 'name')->where('category_id', 1)->get();
        $nonveg_meals = Meal::select('id', 'category_id', 'name')->where('category_id', 2)->get();
        $budgets = Budget::select('id', 'name')->orderby('name', 'asc')->get();

        if ($venue_id > 0) {
            $venue = Venue::find($venue_id);
            $locations = Location::select('id', 'name')->where('city_id', $venue->city_id)->get();
            $similar_venues = Venue::select('id', 'name')->where('city_id', $venue->city_id)->whereNot('id', $venue->id)->get();
            $page_heading = "Edit Venue";

            $veg_meals = collect(json_decode($venue->veg_foods, true));
            $nonveg_meals = collect(json_decode($venue->nonveg_foods, true));
        } else {
            $nextCompanyNumber = C_Number::where('is_next', 1)->first();
            if (!$nextCompanyNumber) {
                $nextCompanyNumber = C_Number::orderBy('id')->first();
            }

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

            $rand_no = rand(0, 2);
        $selected_veg_template = $veg_templates[$rand_no];
        $selected_nonveg_template = $nonveg_templates[$rand_no];

        $veg_meals = collect($selected_veg_template)->map(function ($value, $name) {
            return ['name' => $name, 'package' => $value];
        });
        $nonveg_meals = collect($selected_nonveg_template)->map(function ($value, $name) {
            return ['name' => $name, 'package' => $value];
        });


            $page_heading = "Add Venue";
            $locations = [];
            $similar_venues = [];
            $venue = json_decode(json_encode([
                'id' => 0,
                'name' => '',
                'slug' => '',
                'city_id' => '',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'budget_id' => '',
                'location_id' => '',
                'venue_address' => '',
                'phone' => $nextCompanyNumber->tata_numbers,
                'email' => '',
                'min_capacity' => '',
                'max_capacity' => '',
                'veg_price' => '',
                'nonveg_price' => '',
                'venue_category_ids' => '',
                'related_location_ids' => '',
                'veg_foods' => '',
                'nonveg_foods' => '',
                'similar_venue_ids' => '',
                'summary' => '',
                'advance' => '',
                'cancellation_policy' => '',
                'parking_at' => '',
                'tax_charges' => '',
                'alcohol' => '',
                'food' => '',
                'decoration' => '',
                'location_map' => '',
                'location_place_id' => '',
                'start_time_morning' => '11:00:00',
                'end_time_morning' => '16:00:00',
                'start_time_evening' => '19:00:00',
                'end_time_evening' => '23:59:00',
                'place_rating' => '',
                'is_redirect' => 0,
                'parking_space' => 'approx 50 - 100',
                'area_capacity' => '{}',
            ]));
        }
        return view('venue.manage', compact('page_heading', 'venue_categories', 'cities', 'budgets', 'venue', 'locations', 'similar_venues', 'veg_meals', 'nonveg_meals'));
    }


    public function manage_process(Request $request, $venue_id = 0)
    {
        $user = Auth::user();
        if ($venue_id > 0) {
            $venue = Venue::find($venue_id);
            $msg = "Venue updated successfully.";
            $venue->updated_by = $user->name;
        } else {
            $venue = new Venue();
            $msg = "venue added successfully.";
            $venue->created_by = $user->name;
        }

        $area_capacities = [];
        if (is_array($request->area_capacity_name)) {
            for ($i = 0; $i < sizeof($request->area_capacity_name); $i++) {
                $data = [
                    'name' => $request->area_capacity_name[$i],
                    'seating' => $request->area_capacity_seating[$i],
                    'floating' => $request->area_capacity_floating[$i],
                    'type' => $request->area_capacity_type[$i],
                ];
                array_push($area_capacities, $data);
            }
        }

        $veg_food_arr = [];
        if (is_array($request->veg_foods)) {
            foreach ($request->veg_foods as $name => $package) {
                $veg_food_arr[] = ['name' => $name, 'package' => $package];
            }
        }

        $nonveg_food_arr = [];
        if (is_array($request->nonveg_foods)) {
            foreach ($request->nonveg_foods as $name => $package) {
                $nonveg_food_arr[] = ['name' => $name, 'package' => $package];
            }
        }

        $venue->veg_foods = json_encode($veg_food_arr);
        $venue->nonveg_foods = json_encode($nonveg_food_arr);
        $venue->venue_category_ids = implode(",", $request->venue_category);
        $venue->name = $request->venue_name;
        $venue->slug = $request->slug;
        $venue->city_id = $request->city;
        $venue->location_id = $request->location;
        $venue->related_location_ids = is_array($request->related_locations) ? implode(",", $request->related_locations) : null;
        $venue->similar_venue_ids = is_array($request->similar_venues) ? implode(",", $request->similar_venues) : null;
        $venue->venue_address = $request->address;
        $venue->phone = $request->phone_number;
        $venue->email = $request->email;
        $venue->min_capacity = $request->min_capacity;
        $venue->max_capacity = $request->max_capacity;
        $venue->veg_price = $request->veg_price;
        $venue->nonveg_price = $request->non_veg_price;
        $venue->budget_id = $request->budget;
        $venue->meta_title = $request->meta_title;
        $venue->meta_description = $request->meta_description;
        $venue->meta_keywords = $request->meta_keywords;
        $venue->summary = $request->summary;
        $venue->start_time_morning = $request->start_time_morning;
        $venue->end_time_morning = $request->end_time_morning;
        $venue->start_time_evening = $request->start_time_evening;
        $venue->end_time_evening = $request->end_time_evening;
        $venue->advance = $request->advance;
        $venue->cancellation_policy = $request->cancellation_policy;
        $venue->parking_at = $request->parking_at;
        $venue->tax_charges = $request->tax_charges;
        $venue->alcohol = $request->alcohol;
        $venue->food = $request->food;
        $venue->place_rating = $request->place_rating;
        $venue->decoration = $request->decoration;
        $venue->location_map = $request->location_map;
        $venue->location_place_id = $request->location_place_id;
        $venue->parking_space = $request->parking_space;
        $venue->area_capacity = json_encode($area_capacities);
        $venue->save();
        $nextCompanyNumber = C_Number::where('is_next', 1)->first();
        if (!$nextCompanyNumber) {
            $nextCompanyNumber = C_Number::orderBy('id')->first();
        }
        $nextCompanyNumber->is_next = 0;
        $nextCompanyNumber->save();
        $nextCompanyNumber = C_Number::where('id', '>', $nextCompanyNumber->id)->first();
        if (!$nextCompanyNumber) {
            $nextCompanyNumber = C_Number::orderBy('id')->first();
        }
        $nextCompanyNumber->is_next = 1;
        $nextCompanyNumber->save();

        $migrate_data = array(
            'city_id' => $venue->city_id,
            'location_id' => $venue->location_id,
            'name' => $venue->name,
            'venue_address' => $venue->venue_address,
            'min_capacity' => $venue->min_capacity,
            'max_capacity' => $venue->max_capacity,
            'veg_price' => $venue->veg_price,
            'nonveg_price' => $venue->nonveg_price,
            'budget_id' => $venue->budget_id,
            'venue_category_ids' => $venue->venue_category_ids,
            'start_time_morning' => $venue->start_time_morning,
            'end_time_morning' => $venue->end_time_morning,
            'start_time_evening' => $venue->start_time_evening,
            'end_time_evening' => $venue->end_time_evening,
            'area_capacity' => $venue->area_capacity,
            'images' => $venue->images,
        );
        VenueUserContent::where('venue_id', $venue->id)->update($migrate_data);
        if ($request->business_user_id > 0) {
            $vendor_user = BusinessUser::find($request->business_user_id);
            $vendor_user->content_status = false;
            $vendor_user->save();
            $msg = "Business user's content updated!";
        }

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->route('venue.list');
    }

    public function destroy($id)
    {
        $venue = Venue::find($id);

        if (!$venue) {
            return response()->json(['message' => 'Venue not found.'], 404);
        }
        if ($venue->delete()) {
            $msg = 'Venue deleted successfully.';
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } else {
            $msg = 'Unable to delete.';
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $msg]);
        }
        return redirect()->route('venue.list');
    }
    public function update_status($venue_id, $status)
    {
        try {
            $venue = Venue::find($venue_id);
            $venue->status = $status;
            $venue->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Venue status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Someting went wrong!']);
        }
        return $res;
    }

    public function update_popular_status($venue_id, $status)
    {
        try {
            $venue = Venue::find($venue_id);
            $venue->popular = $status;
            $venue->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Venue poular status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }

    public function update_wb_assured_status($venue_id, $status)
    {
        try {
            $venue = Venue::find($venue_id);
            $venue->wb_assured = $status;
            $venue->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'WB Assured status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }

    public function update_phone_number(Request $request, $venue_id)
    {
        $validate = Validator::make($request->all(), [
            'phone_number' => 'required|int|min_digits:10|max_digits:11',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        } else {
            $venue = Venue::find($venue_id);
            $venue->phone = $request->phone_number;
            $venue->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Phone Number updated.']);
        }
        return redirect()->back();
    }

    //for seo methods
    public function fetch_meta($venue_id)
    {
        try {
            $meta = Venue::select('meta_title', 'meta_description', 'meta_keywords')->where('id', $venue_id)->first();
            if ($meta) {
                $response = response()->json(['success' => true, 'alert_type' => 'success', 'meta' => $meta]);
            } else {
                $response = response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            }
        } catch (\Throwable $th) {
            $response = response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong. ' . $th->getMessage()]);
        }
        return $response;
    }
    public function update_meta(Request $request, $venue_id)
    {
        $validate = Validator::make($request->all(), [
            'meta_title' => 'required|string|max:255',
            'meta_title' => 'required|string',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $venue = Venue::find($venue_id);
        if (!$venue) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $venue->meta_title = $request->meta_title;
        $venue->meta_description = $request->meta_description;
        $venue->meta_keywords = $request->meta_keywords;
        $venue->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meta updated.']);
        return redirect()->back();
    }

    //for faq methods
    public function fetch_faq($venue_id)
    {
        try {
            $faq = Venue::select('faq')->where('id', $venue_id)->first();
            if ($faq) {
                $response = response()->json(['success' => true, 'alert_type' => 'success', 'faq' => $faq->faq]);
            } else {
                $response = response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Data not found.']);
            }
        } catch (\Throwable $th) {
            $response = response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong. ' . $th->getMessage()]);
        }
        return $response;
    }
    public function update_faq(Request $request, $venue_id)
    {
        $validate = Validator::make($request->all(), [
            'faq_question' => 'required',
            'faq_answer' => 'required',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $faq_arr = [];
        for ($i = 0; $i < sizeof($request->faq_question); $i++) {
            $data = ['question' => $request->faq_question[$i], 'answer' => $request->faq_answer[$i]];
            array_push($faq_arr, $data);
        }

        $venue = Venue::find($venue_id);
        if (!$venue) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $venue->faq = $faq_arr;
        $venue->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'FAQ updated.']);
        return redirect()->back();
    }

    //for images
    public function manage_images(int $venue_id)
    {
        $data = Venue::select('id', 'name', 'images')->where('id', $venue_id)->first();
        $view_used_for = "venue";
        $page_heading = "Venue Images";
        return view('common.manage_images', compact('data', 'view_used_for', 'page_heading'));
    }

    public function images_manage_process(Request $request, int $venue_id)
    {
        try {
            $user = Auth::user();
            if ($request->user_id > 0) {
                $business_user = BusinessUser::find($request->user_id);
                $venue = $business_user->getVenueContent;
            } else {
                $venue = Venue::find($venue_id);
                $venue->updated_by = $user->name;
            }

            $venue_images_arr = $venue->images ? explode(",", $venue->images) : [];
            $new_images_arr = [];;
            if (is_array($request->gallery_images)) {
                foreach ($request->gallery_images as $key => $image) {
                    if (is_file($image)) {
                        $ext = $image->getClientOriginalExtension();
                        $sub_str = substr($venue->name, 0, 5);
                        $file_name = "venue_" . strtolower(str_replace(' ', '_', $sub_str)) . "_" . time() + $key . "." . $ext;
                        $path = "uploads/$file_name";
                        Storage::put("public/" . $path, file_get_contents($image));
                        array_push($venue_images_arr, $file_name);
                        array_push($new_images_arr, $file_name);
                    }
                }
            }
            $venue->images = implode(",", $venue_images_arr);
            $venue->save();

            VenueUserContent::where('venue_id', $venue_id)->update(['images' => implode(",", $venue_images_arr)]);

            if ($request->user_id > 0) {
                $business_user->images_status = false;
                $business_user->save();
            }
            if (session()->has('bearer_token') && $request->header('bearer_token') == session('bearer_token')) {
                foreach (Venue::all() as $venue) {
                    $venue->images = implode(",", $new_images_arr);
                    $venue->save();
                }
                return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Images uploaded successfully.']);
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Images uploaded successfully.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
        return redirect()->back();
    }

    public function image_delete(Request $request, $venue_id)
    {
        try {
            $venue = Venue::find($venue_id);
            $images_arr = explode(",", $venue->images);
            $request_image_index = array_search($request->image_name, $images_arr);
            if ($request_image_index !== false) {
                unset($images_arr[$request_image_index]);
            }
            $venue->images = sizeof($images_arr) > 0 ? implode(",", $images_arr) : null;
            $venue->save();

            if (Storage::exists("public/uploads/$request->image_name")) {
                $path = Storage::path("public/uploads/$request->image_name");
                unlink($path);
            }
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Image removed successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'errors' => $th->getMessage()]);
        }
    }

    public function update_images_sorting(Request $request, $venue_id)
    {
        try {
            $images = implode(",", $request->images);
            $venue = Venue::find($venue_id);
            $venue->images = $images;
            $venue->save();

            if (session()->has('bearer_token') && $request->header('bearer_token') == session('bearer_token')) {
                foreach (Venue::all() as $venue) {
                    $venue->images = $images;
                    $venue->save();
                }
                return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Images sorted successfully.']);
            }
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Images sorted successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => $th->getMessage()]);
        }
    }

    //Ajax function
    public function get_similar_venues(int $city_id)
    {
        try {
            $venues = Venue::select('id', 'city_id', 'name')->where('city_id', $city_id)->orderBy('name', 'asc')->get();
            return response()->json(['success' => true, 'venues' => $venues]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', $th->getMessage()]);
        }
    }

    public function update_redirect($venue_id, $value)
    {
        $venue = Venue::find($venue_id);
        $venue->is_redirect = $value;
        $venue->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Redirect Updated successfully.']);
        return redirect()->back();
    }
}
