<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BusinessUser;
use App\Models\City;
use App\Models\Location;
use App\Models\Meal;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\Venue;
use App\Models\VenueCategory;

class BusinessUserContentController extends Controller {
    public function manage_content($user_id) {
        $user = BusinessUser::find($user_id);
        if ($user->business_type == 1) {
            $content_data = $user->getVenueContent;
            $content_data->id = $user->migrated_business_id;
            $business_categories = VenueCategory::select('id', 'name')->orderBy('name', 'asc')->get();
            $similar_venues = Venue::select('id', 'name')->where('city_id', $content_data->city_id)->whereNot('id', $content_data->id)->get();
            $veg_meals = Meal::select('id', 'category_id', 'name')->where('category_id', 1)->orderby('name', 'asc')->get();
            $nonveg_meals = Meal::select('id', 'category_id', 'name')->where('category_id', 2)->orderby('name', 'asc')->get();
            $budgets = Budget::all();

            $og_venue = Venue::find($user->migrated_business_id);
            $content_data['slug'] = $og_venue->slug;
            $content_data['phone'] = $og_venue->phone;
            $content_data['email'] = $og_venue->email;
            $content_data['meta_title'] = $og_venue->meta_title;
            $content_data['meta_description'] = $og_venue->meta_description;
            $content_data['summary'] = $og_venue->summary;
            $content_data['similar_venue_ids'] = $og_venue->similar_venue_ids;
            $content_data['related_location_ids'] = $og_venue->related_location_ids;
            $content_data['advance'] = $og_venue->advance;
            $content_data['cancellation_policy'] = $og_venue->cancellation_policy;
            $content_data['parking_at'] = $og_venue->parking_at;
            $content_data['tax_charges'] = $og_venue->tax_charges;
            $content_data['alcohol'] = $og_venue->alcohol;
            $content_data['food'] = $og_venue->food;
            $content_data['decoration'] = $og_venue->decoration;
            $content_data['location_map'] = $og_venue->location_map;
            $content_data['veg_foods'] = $og_venue->veg_foods;
            $content_data['nonveg_foods'] = $og_venue->nonveg_foods;

            $res = [
                'page_heading' => 'Edit Venue',
                'venue' => $content_data,
                'venue_categories' => $business_categories,
                'veg_meals' => $veg_meals,
                'nonveg_meals' => $nonveg_meals,
                'budgets' => $budgets,
                'similar_venues' => $similar_venues,
            ];
            $manage_view = "venue.manage";
        } else {
            $content_data = $user->getVendorContent;
            $content_data->id = $user->migrated_business_id;
            $business_categories = VendorCategory::select('id', 'name')->orderBy('name', 'asc')->get();
            $similar_vendors = Vendor::select('id', 'brand_name')->where('city_id', $content_data->city_id)->whereNot('id', $content_data->id)->get();

            $og_vendor = Vendor::find($user->migrated_business_id);
            $content_data['slug'] = $og_vendor->slug;
            $content_data['phone'] = $og_vendor->phone;
            $content_data['meta_title'] = $og_vendor->meta_title;
            $content_data['meta_description'] = $og_vendor->meta_description;
            $content_data['summary'] = $og_vendor->summary;
            $content_data['similar_vendor_ids'] = $og_vendor->similar_vendor_ids;
            $res = [
                'page_heading' => 'Edit Vendor',
                'vendor_categories' => $business_categories,
                'vendor' => $content_data,
                'similar_vendors' => $similar_vendors,
            ];

            $manage_view = "vendor.manage";
        }

        $cities = City::select('id', 'name')->orderby('name', 'asc')->get();
        $locations = Location::select('id', 'name')->where('city_id', $content_data->city_id)->get();
        $res['cities'] = $cities;
        $res['locations'] = $locations;
        $res['business_user_id'] = $user->id;
        return view($manage_view, $res);
    }

    //for images
    public function manage_images(int $user_id) {
        $user = BusinessUser::find($user_id);
        if ($user->business_type == 1) {
            $user_content = $user->getVenueContent;
            $data = json_decode(json_encode(['id' => $user_content->venue_id, 'name' => $user_content->name, 'images' => $user_content->images]));
        } else {
            $user_content = $user->getVendorContent;
            $data = json_decode(json_encode(['id' => $user_content->vendor_id, 'name' => $user_content->brand_name, 'images' => $user_content->images]));
        }

        $view_used_for = $user->business_type == 1 ? 'venue' : 'vendor';
        $page_heading =  ucfirst($view_used_for) . " Images";
        $user_id = $user->id;

        return view('common.manage_images', compact('data', 'view_used_for', 'page_heading', 'user_id'));
    }
}
