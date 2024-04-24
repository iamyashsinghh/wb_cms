<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\City;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorListingMeta;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\VenueListingMeta;
use Illuminate\Support\Facades\DB;

class RefactorController extends Controller {
    public function get_common_faqs() {
        $old_faqs = DB::connection('mysql2')->table('faq')->get();
        $faq_arr = [];
        foreach ($old_faqs as $list) {
            $data = ['question' => $list->question, 'answer' => $list->answer];
            array_push($faq_arr, $data);
        }

        return $faq_arr;
    }

    public function budget_refactor() {
        $data = DB::connection('mysql2')->table('budget')->get();
        foreach ($data as $key => $list) {
            $model = new Budget();
            $model->id = $list->id;
            $model->name = $list->name;
            $model->min = $key + 1 . "00000";
            $model->max = $key + 2 . "00000";
            $model->save();
        }
        return "Success";
    }

    public function city_refactor() {
        $data = DB::connection('mysql2')->table('state')->get();
        foreach ($data as $key => $list) {
            $model = new City();
            $model->id = $list->id;
            $model->name = $list->name;
            $model->slug = trim(strtolower(str_replace(" ", "-", $list->name)));
            $model->save();
        }
        return "Success";
    }
    public function location_refactor() {
        $data = DB::connection('mysql2')->table('city')->get();
        foreach ($data as $key => $list) {
            $model = new Location();
            $model->id = $list->id;
            $model->city_id = $list->state_id;
            $model->name = $list->name;
            $model->slug = trim(strtolower(str_replace(" ", "-", $list->name)));
            $model->save();
        }

        return "Success";
    }
    public function meal_refactor() {
        $data = DB::connection('mysql2')->table('meals')->get();
        foreach ($data as $key => $list) {
            $model = new Meal();
            $model->id = $list->id;
            $model->category_id = $list->category == 5 ? 1 : 2;
            $model->name = $list->item;
            $model->save();
        }

        return "Success";
    }
    public function venue_category_refactor() {
        $data = DB::connection('mysql2')->table('occasion_category')->select('id', 'name', 'slug')->where('type', 1)->get();
        foreach ($data as $key => $list) {
            $slug_arr = explode("-in-", $list->slug);

            $model = new VenueCategory();
            $model->id = $list->id;
            $model->name = $list->name;
            $model->slug = $slug_arr[0];
            $model->save();
        }

        return "Success";
    }
    public function venue_refactor() {
        /*
            Note: Before run this function check locality must not be null;
        */
        // important code to store this json content into all venues food details
        $veg_foods_arr = [];
        $nonveg_foods_arr = [];
        $meal = Meal::all();
        foreach ($meal as $key => $list) {
            if ($list->category_id == 1) {
                array_push($veg_foods_arr, ['name' => $list->name, 'package' => 1]);
            } else {
                array_push($nonveg_foods_arr, ['name' => $list->name, 'package' => 1]);
            }
        }


        $old_venues = DB::connection('mysql2')->table('venues')->get();
        foreach ($old_venues as $key => $list) {
            $name_arr = explode(",", preg_replace('/[\["\]]/', '', $list->areaName));
            $seating_arr = explode(",", preg_replace('/[\["\]]/', '', $list->areaSeating));
            $capacity_arr = explode(",", preg_replace('/[\["\]]/', '', $list->areaCapicity));
            $in_out_door = explode(",", preg_replace('/[\["\]]/', '', $list->inOutDoor));

            $area_capacity_arr = [];
            if ($name_arr[0] != "") {
                for ($i = 0; $i < sizeof($name_arr); $i++) {
                    $data = [
                        'name' => isset($name_arr[$i]) ? $name_arr[$i] : null,
                        'seating' => isset($seating_arr[$i]) ? $seating_arr[$i] : null,
                        'floating' => isset($capacity_arr[$i]) ? $capacity_arr[$i] : null,
                        'type' => isset($in_out_door[$i]) ? ucfirst(strtolower(preg_replace('/[0-9]+/', '', $in_out_door[$i]))) : null,
                    ];
                    array_push($area_capacity_arr, $data);
                }
            }

            $model = new Venue();
            $model->id = $list->id;
            $model->city_id = $list->city_id;
            $model->location_id = $list->locality;
            $model->name = $list->name;
            $model->slug = $list->url;
            $model->phone = $list->phone_number;
            $model->email = $list->email;
            $model->venue_address = $list->address;
            $model->min_capacity = $list->min_capacity;
            $model->max_capacity = $list->max_capacity;
            $model->veg_price = $list->rackrate;
            $model->veg_foods = json_encode($veg_foods_arr);
            $model->nonveg_price = $list->non_veg_rackrate;
            $model->nonveg_foods = json_encode($nonveg_foods_arr);
            $model->budget_id = $list->budget_id;
            $model->venue_category_ids = $list->occasion_id;
            $model->related_location_ids = $list->related_location;
            $model->similar_venue_ids = preg_replace('/[\["\]]/', '', $list->similer_package);
            $model->start_time_morning = $list->mStartTime;
            $model->end_time_morning = $list->mEndTime;
            $model->start_time_evening = $list->eStartTime;
            $model->end_time_evening = $list->eEndTime;
            $model->area_capacity = json_encode($area_capacity_arr);
            $model->meta_title = $list->meta_title;
            $model->meta_description = $list->meta_description;
            $model->summary = $list->venue_summary;
            $model->images = preg_replace('/[\["\]]/', '', $list->image);
            $model->advance = $list->adv_money;
            $model->cancellation_policy = $list->can_charge;
            $model->parking_at = $list->parking_at;
            $model->tax_charges = $list->taxes;
            $model->alcohol = $list->alcohol;
            $model->food = $list->food;
            $model->decoration = $list->decoration;
            $model->location_map = $list->location_map;
            $model->popular = $list->populer;
            $model->status = 1;
            $model->save();
        }

        return "Success";
    }

    public function vendor_category_refactor() {
        $old_categories = DB::connection('mysql2')->table('service_category')->get();
        foreach ($old_categories as $list) {
            $slug_arr = explode("-in-", $list->slug);

            $model = new VendorCategory();
            $model->id = $list->id;
            $model->name = $list->name;
            $model->slug = $slug_arr[0];
            $model->save();
        }
        return "Success";
    }

    public function vendor_refactor() {
        $old_vendors = DB::connection('mysql2')->table('vendors')->where('meta_keyword', '!=', "")->get();
        return $old_vendors;

        foreach ($old_vendors as $list) {
            $images = preg_replace('/[\["\]]/', '', $list->image);
            $similar_vendors = str_replace(['"', '[', ']'], '', $list->similer_venues);
            $package_option = preg_replace('/[\["\]]/', '', $list->package_options);

            $vendor = new Vendor();
            $vendor->id = $list->id;
            $vendor->city_id = $list->v_city;
            $vendor->location_id = $list->v_locality;
            $vendor->vendor_category_id = $list->v_type;
            $vendor->brand_name = $list->vb_name;
            $vendor->slug = $list->slug;
            $vendor->phone = trim($list->v_mobile);
            $vendor->vendor_address = $list->v_address;
            $vendor->vendor_address = $list->v_address;
            $vendor->package_price = $list->v_price;
            $vendor->summary = $list->v_summary;
            $vendor->images = $images;
            $vendor->similar_vendor_ids = trim($similar_vendors);
            $vendor->package_option = $package_option;
            $vendor->meta_title = $list->meta_title;
            $vendor->meta_description = $list->meta_description;
            $vendor->popular = $list->show_front;
            $vendor->status = $list->status;
            $vendor->save();
        }
        return "Success";
    }

    public function venue_listing_meta_refactor() {
        $old_metas = DB::connection('mysql2')->table('occasion_category')->get();
        foreach ($old_metas as $key => $list) {
            $temp_arr = explode("-in-", $list->slug);
            $category = VenueCategory::where('slug', $temp_arr[0])->first();
            $locality = Location::where('slug', $temp_arr[1])->first();
            if (!$category) {
                return ['msg' => 'Category not found.', 'data' => $list];
            }
            // elseif (!$locality) {
            //     return ['msg' => 'Location not found.', 'data' => $list];
            // }

            $meta = new VenueListingMeta();
            $meta->category_id = $category->id;
            $meta->city_id = 1;
            $meta->location_id = $locality ? $locality->id : null;
            $meta->meta_title = $list->meta_title;
            $meta->meta_description = $list->meta_description;
            $meta->meta_keywords = $list->meta_keyword;
            $meta->caption = $list->home_caption;
            if ($temp_arr[1] == "delhi") {
                $meta->slug = "$temp_arr[0]/$temp_arr[1]/all";
            } else {
                $meta->slug = "$temp_arr[0]/delhi/$temp_arr[1]";
            }
            $meta->save();
        }

        return "Success!";
    }

    public function vendor_listing_meta_refactor() {
        $old_metas = DB::connection('mysql2')->table('service_category')->get();
        foreach ($old_metas as $key => $list) {
            $meta = new VendorListingMeta();
            $meta->category_id = 1;
            $meta->city_id = 1;
            $meta->slug = str_replace("-in-", "/", $list->slug) . "/all";
            $meta->meta_title = $list->meta_title;
            $meta->meta_description = $list->meta_description;
            $meta->meta_keywords = $list->meta_keyword;
            $meta->caption = $list->home_caption;
            $meta->save();
        }

        return "Success!";
    }

    // public function migrate_keywords() {
    //     $old_venues = DB::connection('mysql2')->table('venues')->limit(2)->get();
    //     return $old_venues;
    // }
}
