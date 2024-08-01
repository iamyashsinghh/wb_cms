<?php

namespace App\Http\Controllers;

use App\Mail\NotifyReceivedUser;
use App\Mail\ThanksForSignin;
use App\Mail\VerificationMail;
use App\Models\Author;
use App\Models\Blog;
use App\Models\Budget;
use App\Models\BusinessUser;
use App\Models\City;
use App\Models\Location;
use App\Models\PageListingMeta;
use App\Models\Review;
use App\Models\User;
use App\Models\UserSignupRequest;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorListingMeta;
use App\Models\VendorUserContent;
use App\Models\Venue;
use App\Models\VenueCategory;
use App\Models\VenueListingMeta;
use App\Models\VenueUserContent;
use App\Models\WebAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function cities()
    {
        try {
            $cities = City::select('id', 'name', 'slug')->get();
            $response = [
                'success' => true,
                'data' => $cities,
                'message' => 'Data fetched succesfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }

        return $response;
    }

    public function get_all_venues()
    {
        $venues = Venue::select('venues.id', 'venues.name', 'venues.slug', 'venues.city_id', 'venues.location_id', 'venues.images', 'cities.slug as city_slug')
            ->join('cities', 'venues.city_id', '=', 'cities.id')
            ->get();

        return $venues;
    }

    public function get_all_vendors()
    {
        $vendors = Vendor::select('vendors.*', 'cities.slug as city_slug')
            ->join('cities', 'vendors.city_id', '=', 'cities.id')
            ->get();

        return $vendors;
    }

    public function budgets()
    {
        $budgets = Budget::select('id', 'name', 'min', 'max')->get();

        return response()->json(['success' => true, 'data' => $budgets, 'message' => 'Data fetched successfully.']);
    }

    public function locations(string $city_slug)
    {
        try {
            $locations = Location::select('locations.id', 'locations.name', 'locations.slug', 'locations.is_group')
                ->join('cities', 'cities.id', 'locations.city_id')
                ->where('cities.slug', $city_slug)->get();
            $response = [
                'success' => true,
                'data' => $locations,
                'message' => 'Data fetched succesfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }

        return $response;
    }

    public function get_json_reviews($place_id)
    {
        $get_json_reviews = Storage::get('public/uploads/all_reviews/' . $place_id . '_reviews.json');

        return $get_json_reviews;
    }

    public function get_json_reviews_site($product_id)
    {
        $get_json_reviews_site = Review::where('product_id', $product_id)->where('status', 1)->get();

        return $get_json_reviews_site;
    }

    public function search_form_result_venue()
    {
        $venue = Venue::Select('id', 'name', 'slug')->get();
        return $venue;
    }

    public function search_form_result_vendor()
    {
        $vendor = Vendor::Select('id', 'brand_name', 'slug')->get();
        return $vendor;
    }

    public function state_management()
    {
        try {
            $cities = City::select('id', 'name', 'slug')->get();
            $venue_categories = VenueCategory::select('id', 'name', 'slug')->get();
            foreach ($venue_categories as $venuecat) {
                $venue_count = Vendor::where('vendor_category_id', $venuecat->id)->count();
                $venuecat['venue_count'] = $venue_count;
            }
            $vendor_categories = VendorCategory::select('id', 'name', 'slug')->get();
            foreach ($vendor_categories as $cat) {
                $vendor_count = Vendor::where('vendor_category_id', $cat->id)->count();
                $cat['vendors_count'] = $vendor_count;
            }

            $data = compact(
                'cities',
                'venue_categories',
                'vendor_categories',
            );
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data fetched succesfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }

        return $response;
    }

    public function venues_vendor_page_data($city, $type)
    {
        $data = PageListingMeta::where('slug', "$city/$type")->where('status', 1)->first();
        if ($data) {
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data fetched succesfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => [],
            ];
        }

        return $response;
    }

    public function popular_venues(string $category_slug, string $city_slug)
    {
        try {
            $category_id = VenueCategory::where('slug', $category_slug)->first()->id;
            $city_id = City::where('slug', $city_slug)->first()->id;

            $popular_venues = Venue::select(
                'id',
                'name',
                'images',
                'venue_address',
                'phone',
                'slug',
                'min_capacity',
                'max_capacity',
                'veg_price',
                'nonveg_price',
                'wb_assured',
                'place_rating',
                DB::raw('COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.product_id = venues.id), 158) as reviews_count')
            )
                ->where(['venues.city_id' => $city_id, 'venues.popular' => true])
                ->whereRaw('FIND_IN_SET(?, venue_category_ids)', [$category_id])
                ->limit(10)
                ->get();
            $response = [
                'success' => true,
                'data' => $popular_venues,
                'message' => 'Data fetched succesfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }

        return $response;
    }

    public function home_page(string $city_slug = 'delhi')
    {
        try {
            $cities = $this->cities()['data'];
            $venue_categories = VenueCategory::select('id', 'name', 'slug')->get();
            $vendor_categories = VendorCategory::select('id', 'name', 'slug')->get();
            foreach ($vendor_categories as $cat) {
                $vendor_count = Vendor::where('vendor_category_id', $cat->id)->count();
                $cat['vendors_count'] = $vendor_count;
            }

            $popular_venues = $this->popular_venues('banquet-halls', $city_slug)['data'];
            $blogs = DB::connection('mysql2')->table('wp_posts')->select(
                'wp_posts.ID',
                'wp_posts.post_title',
                'wp_posts.post_name as post_slug',
                'wp_posts.post_date',
                DB::raw(
                    '(select for_image.guid from wp_posts as for_image where for_image.post_parent = wp_posts.ID and for_image.post_type = "attachment" limit 1) as post_thumbnail'
                ),
            )->where([
                'wp_posts.post_type' => 'post',
                'wp_posts.post_status' => 'publish',
            ])->orderBy('wp_posts.ID', 'desc')->limit(3)->get();

            $data = compact('cities', 'venue_categories', 'vendor_categories', 'popular_venues', 'blogs');
            $response = [
                'success' => true,
                'data' => $data,
                'message' => 'Data fetched succesfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }

        return $response;
    }

    // public function venue_or_vendor_list(Request $request, string $category_slug, string $city_slug, string $location_slug = 'all', int $page_no = 1)
    // {
    //     try {
    //         $cities = City::select('id', 'name', 'slug')->orderBy('name', 'asc')->get();

    //         $items_per_page = 9;
    //         $offset = ($page_no - 1) * $items_per_page;

    //         $venue_category = VenueCategory::where('slug', $category_slug)->first();
    //         $vendor_category = VendorCategory::where('slug', $category_slug)->first();

    //         $city = City::where('slug', $city_slug)->first();
    //         if (!$city) {
    //             throw new \Exception("City not found");
    //         }

    //         $location = Location::where(['city_id' => $city->id, 'slug' => $location_slug])->first();
    //         $slug = "$category_slug/$city_slug/$location_slug";

    //         if ($venue_category) {
    //             $data = Venue::select(
    //                 'venues.id',
    //                 'venues.name',
    //                 'venues.venue_address',
    //                 'venues.summary',
    //                 'venues.veg_price',
    //                 'venues.nonveg_price',
    //                 'venues.phone',
    //                 'venues.images',
    //                 'venues.slug',
    //                 'venues.min_capacity',
    //                 'venues.max_capacity',
    //                 'venues.popular',
    //                 'venues.wb_assured',
    //                 'venues.place_rating',
    //                 'venues.venue_category_ids',
    //                 DB::raw('COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.product_id = venues.id), 158) as reviews_count'),
    //                 'locations.name as location_name',
    //                 'cities.name as city_name',
    //                 'locations.id as locationid',
    //             )
    //                 ->join('locations', 'locations.id', '=', 'venues.location_id')
    //                 ->join('cities', 'cities.id', '=', 'venues.city_id')
    //                 ->where(['venues.status' => 1, 'venues.city_id' => $city->id]);

    //             if ($location) {
    //                 if ($location->is_group) {
    //                     $localityIds = explode(',', $location->locality_ids);
    //                     if (!in_array($location->id, $localityIds)) {
    //                         $localityIds[] = $location->id;
    //                     }
    //                     $data->whereIn('venues.location_id', $localityIds);
    //                 } elseif ($location_slug != 'all') {
    //                     $data->where('venues.location_id', $location->id);
    //                 }
    //             }

    //             $data->whereRaw("find_in_set(?, venues.venue_category_ids)", [$venue_category->id]);

    //             if ($request->guest) {
    //                 $params = explode(',', $request->guest);
    //                 $data->whereBetween('venues.max_capacity', [$params[0], $params[1]]);
    //             }

    //             if ($request->per_plate) {
    //                 $params = explode(',', $request->per_plate);
    //                 $data->whereBetween('venues.veg_price', [$params[0], $params[1]]);
    //             }

    //             if ($request->per_budget) {
    //                 $params = explode(',', $request->per_budget);
    //                 $data->join('budgets', 'budgets.id', '=', 'venues.budget_id')
    //                     ->whereBetween('budgets.min', [$params[0], $params[1]]);
    //             }

    //             if ($request->multi_localities) {
    //                 $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
    //                 $arr = $request->multi_localities;
    //                 foreach ($group_locations as $list) {
    //                     $arr .= ',' . $list->locality_ids;
    //                 }
    //                 $params = explode(',', $arr);
    //                 $data->whereIn('venues.location_id', array_unique($params));
    //             }

    //             if ($request->food_type) {
    //                 $food_type = $request->food_type . '_price';
    //                 $data->whereNotNull($food_type);
    //             }

    //             $meta = VenueListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
    //                 ->where('slug', $slug)->first();

    //             $tag = 'venues';
    //         } else {
    //             $data = Vendor::select(
    //                 'vendors.id',
    //                 'vendors.brand_name',
    //                 'vendors.vendor_address',
    //                 'vendors.package_price',
    //                 'vendors.phone',
    //                 'vendors.slug',
    //                 'vendors.summary',
    //                 'vendors.yrs_exp',
    //                 'vendors.event_completed',
    //                 'vendors.images',
    //                 'vendors.popular',
    //                 'vendors.wb_assured',
    //                 'locations.name as location_name',
    //                 'cities.name as city_name',
    //                 'locations.id as locationid',
    //                 'vendors.services',
    //                 'vendors.occasions',
    //             )
    //                 ->join('cities', 'cities.id', '=', 'vendors.city_id')
    //                 ->join('locations', 'locations.id', '=', 'vendors.location_id')
    //                 ->where([
    //                     'vendors.status' => 1,
    //                     'cities.slug' => $city_slug,
    //                     'vendors.vendor_category_id' => $vendor_category->id
    //                 ]);

    //             if ($location) {
    //                 if ($location->is_group) {
    //                     $localityIds = explode(',', $location->locality_ids);
    //                     if (!in_array($location->id, $localityIds)) {
    //                         $localityIds[] = $location->id;
    //                     }
    //                     $data->whereIn('vendors.location_id', $localityIds);
    //                 } elseif ($location_slug != 'all') {
    //                     $data->where('vendors.location_id', $location->id);
    //                 }
    //             }

    //             if ($request->multi_localities) {
    //                 $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
    //                 $arr = $request->multi_localities;
    //                 foreach ($group_locations as $list) {
    //                     $arr .= ',' . $list->locality_ids;
    //                 }
    //                 $params = explode(',', $arr);
    //                 $data->whereIn('vendors.location_id', array_unique($params));
    //             }

    //             if ($request->makeup_bridal_budget) {
    //                 $budgetRange = explode(',', $request->makeup_bridal_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->makeup_engagement_budget) {
    //                 $budgetRange = explode(',', $request->makeup_engagement_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->photographer_service_budget) {
    //                 $budgetRange = explode(',', $request->photographer_service_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->mehndi_package_budget) {
    //                 $budgetRange = explode(',', $request->mehndi_package_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->banquet_decor_package_budget) {
    //                 $budgetRange = explode(',', $request->banquet_decor_package_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->home_decor_package_budget) {
    //                 $budgetRange = explode(',', $request->home_decor_package_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->band_baja_ghodiwala_budget) {
    //                 $budgetRange = explode(',', $request->band_baja_ghodiwala_budget);
    //                 $data->where(function ($query) use ($budgetRange) {
    //                     $query->whereBetween('vendors.package_price', $budgetRange)
    //                         ->orWhereNull('vendors.package_price')
    //                         ->orWhere('vendors.package_price', 0)
    //                         ->orWhere('vendors.package_price', '');
    //                 });
    //             }

    //             if ($request->experience) {
    //                 $expRange = explode(',', $request->experience);
    //                 $minExp = $expRange[0];
    //                 $maxExp = $expRange[1];

    //                 $data->where(function ($query) use ($minExp, $maxExp) {
    //                     $query->whereBetween('vendors.yrs_exp', [$minExp, $maxExp]);
    //                     if ($maxExp == 500) {
    //                         $query->orWhere('vendors.yrs_exp', '>=', $maxExp);
    //                     }
    //                     $query->orWhereNull('vendors.yrs_exp')
    //                         ->orWhere('vendors.yrs_exp', 0)
    //                         ->orWhere('vendors.yrs_exp', '');
    //                 });
    //             }

    //             if ($request->events_completed) {
    //                 $eventCompletedRange = explode(',', $request->events_completed);
    //                 $minEvents = $eventCompletedRange[0];
    //                 $maxEvents = $eventCompletedRange[1];

    //                 $data->where(function ($query) use ($minEvents, $maxEvents) {
    //                     $query->whereBetween('vendors.event_completed', [$minEvents, $maxEvents]);
    //                     if ($maxEvents == 500) {
    //                         $query->orWhere('vendors.event_completed', '>=', $maxEvents);
    //                     }
    //                     $query->orWhereNull('vendors.event_completed')
    //                         ->orWhere('vendors.event_completed', 0)
    //                         ->orWhere('vendors.event_completed', '');
    //                 });
    //             }

    //             $data->orderBy('vendors.location_id', 'asc');

    //             $meta = VendorListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
    //                 ->where('slug', $slug)->first();

    //             $tag = 'vendors';
    //         }

    //         $total_items = $data->count();
    //         if ($location_slug != 'all') {
    //             $venues_or_vendors = $data->orderBy('locationid', 'asc')->orderBy('popular', 'desc')->orderBy('id', 'desc')->skip($offset)->take($items_per_page)->get();
    //         } else {
    //             $venues_or_vendors = $data->orderBy('popular', 'desc')->orderBy('id', 'desc')->skip($offset)->take($items_per_page)->get();
    //         }

    //         if ($venue_category) {
    //             foreach ($venues_or_vendors as $venue_or_vendor) {
    //                 $category_ids = explode(',', $venue_or_vendor->venue_category_ids);
    //                 $category_names = VenueCategory::whereIn('id', $category_ids)->pluck('name')->toArray();
    //                 $venue_or_vendor->venue_category_ids = implode(', ', $category_names);
    //             }
    //         }

    //         if ($tag == 'vendors') {
    //             $venues_or_vendors = $venues_or_vendors->map(function ($vendor) {
    //                 $services = json_decode($vendor->services, true);
    //                 $occasions = json_decode($vendor->occasions, true);

    //                 if (is_array($services)) {
    //                     foreach ($services as $service) {
    //                         $vendor->{$service} = 1;
    //                     }
    //                 }

    //                 if (is_array($occasions)) {
    //                     foreach ($occasions as $occasion) {
    //                         $vendor->{$occasion} = 1;
    //                     }
    //                 }
    //                 return $vendor;
    //             });

    //             if ($request->photographer_service || $request->photographer_occation || $request->makeup_service || $request->makeup_occasion) {
    //                 $photographerService = $request->photographer_service ? explode(',', $request->photographer_service) : [];
    //                 $photographerOccasion = $request->photographer_occation ? explode(',', $request->photographer_occation) : [];
    //                 $makeupService = $request->makeup_service ? explode(',', $request->makeup_service) : [];
    //                 $makeupOccasion = $request->makeup_occasion ? explode(',', $request->makeup_occasion) : [];

    //                 $venues_or_vendors = $venues_or_vendors->filter(function ($venue_or_vendor) use ($photographerService, $photographerOccasion, $makeupService, $makeupOccasion) {

    //                     $vendorServices = $venue_or_vendor->services ? json_decode($venue_or_vendor->services, true) : [];
    //                     $vendorOccasions = $venue_or_vendor->occasions ? json_decode($venue_or_vendor->occasions, true) : [];
    //                     $vendorPhotographerService = $vendorServices;
    //                     $vendorPhotographerOccasion = $vendorOccasions;
    //                     $vendorMakeupService = $vendorServices;
    //                     $vendorMakeupOccasion = $vendorOccasions;
    //                     $matchesPhotographerService = !empty(array_intersect($photographerService, $vendorPhotographerService));
    //                     $matchesPhotographerOccasion = !empty(array_intersect($photographerOccasion, $vendorPhotographerOccasion));
    //                     $matchesMakeupService = !empty(array_intersect($makeupService, $vendorMakeupService));
    //                     $matchesMakeupOccasion = !empty(array_intersect($makeupOccasion, $vendorMakeupOccasion));

    //                     return $matchesPhotographerService || $matchesPhotographerOccasion || $matchesMakeupService || $matchesMakeupOccasion;
    //                 });

    //                 $total_items = $venues_or_vendors->count();
    //             }

    //         }

    //         $response = [
    //             'success' => true,
    //             'tag' => $tag,
    //             'count' => $total_items,
    //             'data' => $venues_or_vendors,
    //             'meta' => $meta,
    //             'cities' => $cities,
    //             'pagination' => [
    //                 'current_page' => $page_no,
    //                 'last_page' => ceil($total_items / $items_per_page),
    //                 'per_page' => $items_per_page,
    //                 'total' => $total_items,
    //             ],
    //             'message' => 'Data fetched successfully',
    //         ];
    //     } catch (\Throwable $th) {
    //         Log::error("Error in venue_or_vendor_list_data: " . $th->getMessage());
    //         $response = [
    //             'success' => false,
    //             'data' => [],
    //             'message' => $th->getMessage(),
    //         ];
    //     }
    //     return response()->json($response);
    // }

    // public function venue_or_vendor_list(Request $request, string $category_slug, string $city_slug, string $location_slug = 'all', int $page_no = 1)
    // {
    //     try {
    //         $cities = City::select('id', 'name', 'slug')->orderBy('name', 'asc')->get();

    //         $items_per_page = 9;
    //         $offset = ($page_no - 1) * $items_per_page;

    //         $venue_category = VenueCategory::where('slug', $category_slug)->first();
    //         $vendor_category = VendorCategory::where('slug', $category_slug)->first();

    //         $city = City::where('slug', $city_slug)->first();
    //         if (!$city) {
    //             throw new \Exception("City not found");
    //         }

    //         $location = Location::where(['city_id' => $city->id, 'slug' => $location_slug])->first();
    //         $slug = "$category_slug/$city_slug/$location_slug";

    //         $meta = null;
    //         $tag = null;
    //         $all_items = collect();

    //         if ($venue_category) {
    //             $data = Venue::select(
    //                 'venues.id',
    //                 'venues.name',
    //                 'venues.venue_address',
    //                 'venues.summary',
    //                 'venues.veg_price',
    //                 'venues.nonveg_price',
    //                 'venues.phone',
    //                 'venues.images',
    //                 'venues.slug',
    //                 'venues.min_capacity',
    //                 'venues.max_capacity',
    //                 'venues.popular',
    //                 'venues.wb_assured',
    //                 'venues.place_rating',
    //                 'venues.venue_category_ids',
    //                 DB::raw('COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.product_id = venues.id), 158) as reviews_count'),
    //                 'locations.name as location_name',
    //                 'cities.name as city_name',
    //                 'locations.id as locationid'
    //             )
    //             ->join('locations', 'locations.id', '=', 'venues.location_id')
    //             ->join('cities', 'cities.id', '=', 'venues.city_id')
    //             ->where(['venues.status' => 1, 'venues.city_id' => $city->id]);

    //             if ($location) {
    //                 if ($location->is_group) {
    //                     $localityIds = explode(',', $location->locality_ids);
    //                     if (!in_array($location->id, $localityIds)) {
    //                         $localityIds[] = $location->id;
    //                     }
    //                     $data->whereIn('venues.location_id', $localityIds);
    //                 } elseif ($location_slug != 'all') {
    //                     $data->where('venues.location_id', $location->id);
    //                 }
    //             }

    //             $data->whereRaw("find_in_set(?, venues.venue_category_ids)", [$venue_category->id]);

    //             if ($request->guest) {
    //                 $params = explode(',', $request->guest);
    //                 $data->whereBetween('venues.max_capacity', [$params[0], $params[1]]);
    //             }

    //             if ($request->per_plate) {
    //                 $params = explode(',', $request->per_plate);
    //                 $data->whereBetween('venues.veg_price', [$params[0], $params[1]]);
    //             }

    //             if ($request->per_budget) {
    //                 $params = explode(',', $request->per_budget);
    //                 $data->join('budgets', 'budgets.id', '=', 'venues.budget_id')
    //                     ->whereBetween('budgets.min', [$params[0], $params[1]]);
    //             }

    //             if ($request->multi_localities) {
    //                 $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
    //                 $arr = $request->multi_localities;
    //                 foreach ($group_locations as $list) {
    //                     $arr .= ',' . $list->locality_ids;
    //                 }
    //                 $params = explode(',', $arr);
    //                 $data->whereIn('venues.location_id', array_unique($params));
    //             }

    //             if ($request->food_type) {
    //                 $food_type = $request->food_type . '_price';
    //                 $data->whereNotNull($food_type);
    //             }

    //             $meta = VenueListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
    //                 ->where('slug', $slug)->first();

    //             $tag = 'venues';
    //             $all_items = $data->get();

    //             // Process venue categories
    //             foreach ($all_items as $item) {
    //                 $category_ids = explode(',', $item->venue_category_ids);
    //                 $category_names = VenueCategory::whereIn('id', $category_ids)->pluck('name')->toArray();
    //                 $item->venue_category_ids = implode(', ', $category_names);
    //             }
    //         } else if ($vendor_category) {
    //             $data = Vendor::select(
    //                 'vendors.id',
    //                 'vendors.brand_name',
    //                 'vendors.vendor_address',
    //                 'vendors.package_price',
    //                 'vendors.phone',
    //                 'vendors.slug',
    //                 'vendors.summary',
    //                 'vendors.yrs_exp',
    //                 'vendors.event_completed',
    //                 'vendors.images',
    //                 'vendors.popular',
    //                 'vendors.wb_assured',
    //                 'locations.name as location_name',
    //                 'cities.name as city_name',
    //                 'locations.id as locationid',
    //                 'vendors.services',
    //                 'vendors.occasions'
    //             )
    //             ->join('cities', 'cities.id', '=', 'vendors.city_id')
    //             ->join('locations', 'locations.id', '=', 'vendors.location_id')
    //             ->where([
    //                 'vendors.status' => 1,
    //                 'cities.slug' => $city_slug,
    //                 'vendors.vendor_category_id' => $vendor_category->id
    //             ]);

    //             if ($location) {
    //                 if ($location->is_group) {
    //                     $localityIds = explode(',', $location->locality_ids);
    //                     if (!in_array($location->id, $localityIds)) {
    //                         $localityIds[] = $location->id;
    //                     }
    //                     $data->whereIn('vendors.location_id', $localityIds);
    //                 } elseif ($location_slug != 'all') {
    //                     $data->where('vendors.location_id', $location->id);
    //                 }
    //             }

    //             if ($request->multi_localities) {
    //                 $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
    //                 $arr = $request->multi_localities;
    //                 foreach ($group_locations as $list) {
    //                     $arr .= ',' . $list->locality_ids;
    //                 }
    //                 $params = explode(',', $arr);
    //                 $data->whereIn('vendors.location_id', array_unique($params));
    //             }

    //             $budget_filters = [
    //                 'makeup_bridal_budget',
    //                 'makeup_engagement_budget',
    //                 'photographer_service_budget',
    //                 'mehndi_package_budget',
    //                 'banquet_decor_package_budget',
    //                 'home_decor_package_budget',
    //                 'band_baja_ghodiwala_budget'
    //             ];

    //             foreach ($budget_filters as $filter) {
    //                 if ($request->$filter) {
    //                     $budgetRange = explode(',', $request->$filter);
    //                     $data->where(function ($query) use ($budgetRange) {
    //                         $query->whereBetween('vendors.package_price', $budgetRange)
    //                             ->orWhereNull('vendors.package_price')
    //                             ->orWhere('vendors.package_price', 0)
    //                             ->orWhere('vendors.package_price', '');
    //                     });
    //                 }
    //             }

    //             if ($request->experience) {
    //                 $expRange = explode(',', $request->experience);
    //                 $minExp = $expRange[0];
    //                 $maxExp = $expRange[1];

    //                 $data->where(function ($query) use ($minExp, $maxExp) {
    //                     $query->whereBetween('vendors.yrs_exp', [$minExp, $maxExp]);
    //                     if ($maxExp == 500) {
    //                         $query->orWhere('vendors.yrs_exp', '>=', $maxExp);
    //                     }
    //                     $query->orWhereNull('vendors.yrs_exp')
    //                         ->orWhere('vendors.yrs_exp', 0)
    //                         ->orWhere('vendors.yrs_exp', '');
    //                 });
    //             }

    //             if ($request->events_completed) {
    //                 $eventCompletedRange = explode(',', $request->events_completed);
    //                 $minEvents = $eventCompletedRange[0];
    //                 $maxEvents = $eventCompletedRange[1];

    //                 $data->where(function ($query) use ($minEvents, $maxEvents) {
    //                     $query->whereBetween('vendors.event_completed', [$minEvents, $maxEvents]);
    //                     if ($maxEvents == 500) {
    //                         $query->orWhere('vendors.event_completed', '>=', $maxEvents);
    //                     }
    //                     $query->orWhereNull('vendors.event_completed')
    //                         ->orWhere('vendors.event_completed', 0)
    //                         ->orWhere('vendors.event_completed', '');
    //                 });
    //             }

    //             $meta = VendorListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
    //                 ->where('slug', $slug)->first();

    //             $tag = 'vendors';
    //             $all_items = $data->get();

    //             // Apply services and occasions filtering
    //             $all_items = $all_items->map(function ($vendor) {
    //                 $services = json_decode($vendor->services, true);
    //                 $occasions = json_decode($vendor->occasions, true);

    //                 if (is_array($services)) {
    //                     foreach ($services as $service) {
    //                         $vendor->{$service} = 1;
    //                     }
    //                 }

    //                 if (is_array($occasions)) {
    //                     foreach ($occasions as $occasion) {
    //                         $vendor->{$occasion} = 1;
    //                     }
    //                 }
    //                 return $vendor;
    //             });

    //             if ($request->photographer_service || $request->photographer_occation || $request->makeup_service || $request->makeup_occasion) {
    //                 $photographerService = $request->photographer_service ? explode(',', $request->photographer_service) : [];
    //                 $photographerOccasion = $request->photographer_occation ? explode(',', $request->photographer_occation) : [];
    //                 $makeupService = $request->makeup_service ? explode(',', $request->makeup_service) : [];
    //                 $makeupOccasion = $request->makeup_occasion ? explode(',', $request->makeup_occasion) : [];

    //                 $all_items = $all_items->filter(function ($vendor) use ($photographerService, $photographerOccasion, $makeupService, $makeupOccasion) {
    //                     $vendorServices = $vendor->services ? json_decode($vendor->services, true) : [];
    //                     $vendorOccasions = $vendor->occasions ? json_decode($vendor->occasions, true) : [];
    //                     $matchesPhotographerService = !empty(array_intersect($photographerService, $vendorServices));
    //                     $matchesPhotographerOccasion = !empty(array_intersect($photographerOccasion, $vendorOccasions));
    //                     $matchesMakeupService = !empty(array_intersect($makeupService, $vendorServices));
    //                     $matchesMakeupOccasion = !empty(array_intersect($makeupOccasion, $vendorOccasions));

    //                     return $matchesPhotographerService || $matchesPhotographerOccasion || $matchesMakeupService || $matchesMakeupOccasion;
    //                 });
    //             }
    //         }

    //         // Get total items count after filtering
    //         $total_items = $all_items->count();

    //         // Apply pagination to the filtered items
    //         if ($location_slug != 'all') {
    //             $filtered_items = $all_items->sortBy('locationid')->sortByDesc('popular')->sortByDesc('id')->slice($offset, $items_per_page)->values();
    //         } else {
    //             $filtered_items = $all_items->sortByDesc('popular')->sortByDesc('id')->slice($offset, $items_per_page)->values();
    //         }

    //         $response = [
    //             'success' => true,
    //             'tag' => $tag,
    //             'count' => $total_items,
    //             'data' => $filtered_items->all(), // Reset the keys of the collection
    //             'meta' => $meta,
    //             'cities' => $cities,
    //             'pagination' => [
    //                 'current_page' => $page_no,
    //                 'last_page' => ceil($total_items / $items_per_page),
    //                 'per_page' => $items_per_page,
    //                 'total' => $total_items,
    //             ],
    //             'message' => 'Data fetched successfully',
    //         ];
    //     } catch (\Throwable $th) {
    //         Log::error("Error in venue_or_vendor_list_data: " . $th->getMessage());
    //         $response = [
    //             'success' => false,
    //             'data' => [],
    //             'message' => $th->getMessage(),
    //         ];
    //     }
    //     return response()->json($response);
    // }

    public function venue_or_vendor_list(Request $request, string $category_slug, string $city_slug, string $location_slug = 'all', int $page_no = 1)
{
    try {
        $cities = City::select('id', 'name', 'slug')->orderBy('name', 'asc')->get();

        $items_per_page = 9;
        $offset = ($page_no - 1) * $items_per_page;

        $venue_category = VenueCategory::where('slug', $category_slug)->first();
        $vendor_category = VendorCategory::where('slug', $category_slug)->first();

        $city = City::where('slug', $city_slug)->first();
        if (!$city) {
            throw new \Exception("City not found");
        }

        $location = Location::where(['city_id' => $city->id, 'slug' => $location_slug])->first();
        $slug = "$category_slug/$city_slug/$location_slug";

        $meta = null;
        $tag = null;
        $all_items = collect();

        if ($venue_category) {
            $data = Venue::select(
                'venues.id',
                'venues.name',
                'venues.venue_address',
                'venues.summary',
                'venues.veg_price',
                'venues.nonveg_price',
                'venues.phone',
                'venues.images',
                'venues.slug',
                'venues.min_capacity',
                'venues.max_capacity',
                'venues.popular',
                'venues.wb_assured',
                'venues.place_rating',
                'venues.venue_category_ids',
                DB::raw('COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.product_id = venues.id), 158) as reviews_count'),
                'locations.name as location_name',
                'cities.name as city_name',
                'locations.id as locationid'
            )
            ->join('locations', 'locations.id', '=', 'venues.location_id')
            ->join('cities', 'cities.id', '=', 'venues.city_id')
            ->where(['venues.status' => 1, 'venues.city_id' => $city->id]);

            if ($location) {
                if ($location->is_group) {
                    $localityIds = explode(',', $location->locality_ids);
                    if (!in_array($location->id, $localityIds)) {
                        $localityIds[] = $location->id;
                    }
                    $data->whereIn('venues.location_id', $localityIds);
                } elseif ($location_slug != 'all') {
                    $data->where('venues.location_id', $location->id);
                }
            }

            $data->whereRaw("find_in_set(?, venues.venue_category_ids)", [$venue_category->id]);

            if ($request->guest) {
                $params = explode(',', $request->guest);
                $data->whereBetween('venues.max_capacity', [$params[0], $params[1]]);
            }

            if ($request->per_plate) {
                $params = explode(',', $request->per_plate);
                $data->whereBetween('venues.veg_price', [$params[0], $params[1]]);
            }

            if ($request->per_budget) {
                $params = explode(',', $request->per_budget);
                $data->join('budgets', 'budgets.id', '=', 'venues.budget_id')
                    ->whereBetween('budgets.min', [$params[0], $params[1]]);
            }

            if ($request->multi_localities) {
                $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
                $arr = $request->multi_localities;
                foreach ($group_locations as $list) {
                    $arr .= ',' . $list->locality_ids;
                }
                $params = explode(',', $arr);
                $data->whereIn('venues.location_id', array_unique($params));
            }

            if ($request->food_type) {
                $food_type = $request->food_type . '_price';
                $data->whereNotNull($food_type);
            }

            $meta = VenueListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
                ->where('slug', $slug)->first();

            $tag = 'venues';

            // Get total items count after filtering
            $total_items = $data->count();

            // Apply pagination to the filtered items
            $filtered_items = $data->orderBy('popular', 'desc')->orderBy('id', 'desc')->skip($offset)->take($items_per_page)->get();

            // Process venue categories
            foreach ($filtered_items as $item) {
                $category_ids = explode(',', $item->venue_category_ids);
                $category_names = VenueCategory::whereIn('id', $category_ids)->pluck('name')->toArray();
                $item->venue_category_ids = implode(', ', $category_names);
            }

            $all_items = $filtered_items;
        } else if ($vendor_category) {
            $data = Vendor::select(
                'vendors.id',
                'vendors.brand_name',
                'vendors.vendor_address',
                'vendors.package_price',
                'vendors.phone',
                'vendors.slug',
                'vendors.summary',
                'vendors.yrs_exp',
                'vendors.event_completed',
                'vendors.images',
                'vendors.popular',
                'vendors.wb_assured',
                'locations.name as location_name',
                'cities.name as city_name',
                'locations.id as locationid',
                'vendors.services',
                'vendors.occasions'
            )
            ->join('cities', 'cities.id', '=', 'vendors.city_id')
            ->join('locations', 'locations.id', '=', 'vendors.location_id')
            ->where([
                'vendors.status' => 1,
                'cities.slug' => $city_slug,
                'vendors.vendor_category_id' => $vendor_category->id
            ]);

            if ($location) {
                if ($location->is_group) {
                    $localityIds = explode(',', $location->locality_ids);
                    if (!in_array($location->id, $localityIds)) {
                        $localityIds[] = $location->id;
                    }
                    $data->whereIn('vendors.location_id', $localityIds);
                } elseif ($location_slug != 'all') {
                    $data->where('vendors.location_id', $location->id);
                }
            }

            if ($request->multi_localities) {
                $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
                $arr = $request->multi_localities;
                foreach ($group_locations as $list) {
                    $arr .= ',' . $list->locality_ids;
                }
                $params = explode(',', $arr);
                $data->whereIn('vendors.location_id', array_unique($params));
            }

            $budget_filters = [
                'makeup_bridal_budget',
                'makeup_engagement_budget',
                'photographer_service_budget',
                'mehndi_package_budget',
                'banquet_decor_package_budget',
                'home_decor_package_budget',
                'band_baja_ghodiwala_budget'
            ];

            foreach ($budget_filters as $filter) {
                if ($request->$filter) {
                    $budgetRange = explode(',', $request->$filter);
                    $data->where(function ($query) use ($budgetRange) {
                        $query->whereBetween('vendors.package_price', $budgetRange)
                            ->orWhereNull('vendors.package_price')
                            ->orWhere('vendors.package_price', 0)
                            ->orWhere('vendors.package_price', '');
                    });
                }
            }

            if ($request->experience) {
                $expRange = explode(',', $request->experience);
                $minExp = $expRange[0];
                $maxExp = $expRange[1];

                $data->where(function ($query) use ($minExp, $maxExp) {
                    $query->whereBetween('vendors.yrs_exp', [$minExp, $maxExp]);
                    if ($maxExp == 500) {
                        $query->orWhere('vendors.yrs_exp', '>=', $maxExp);
                    }
                    $query->orWhereNull('vendors.yrs_exp')
                        ->orWhere('vendors.yrs_exp', 0)
                        ->orWhere('vendors.yrs_exp', '');
                });
            }

            if ($request->events_completed) {
                $eventCompletedRange = explode(',', $request->events_completed);
                $minEvents = $eventCompletedRange[0];
                $maxEvents = $eventCompletedRange[1];

                $data->where(function ($query) use ($minEvents, $maxEvents) {
                    $query->whereBetween('vendors.event_completed', [$minEvents, $maxEvents]);
                    if ($maxEvents == 500) {
                        $query->orWhere('vendors.event_completed', '>=', $maxEvents);
                    }
                    $query->orWhereNull('vendors.event_completed')
                        ->orWhere('vendors.event_completed', 0)
                        ->orWhere('vendors.event_completed', '');
                });
            }

            $meta = VendorListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
                ->where('slug', $slug)->first();

            $tag = 'vendors';

            // Get total items count after filtering
            $total_items = $data->count();

            // Apply pagination to the filtered items
            $filtered_items = $data->orderBy('locationid', 'asc')->orderBy('popular', 'desc')->orderBy('id', 'desc')->skip($offset)->take($items_per_page)->get();

            // Apply services and occasions filtering
            $filtered_items = $filtered_items->map(function ($vendor) {
                $services = json_decode($vendor->services, true);
                $occasions = json_decode($vendor->occasions, true);

                if (is_array($services)) {
                    foreach ($services as $service) {
                        $vendor->{$service} = 1;
                    }
                }

                if (is_array($occasions)) {
                    foreach ($occasions as $occasion) {
                        $vendor->{$occasion} = 1;
                    }
                }
                return $vendor;
            });

            if ($request->photographer_service || $request->photographer_occation || $request->makeup_service || $request->makeup_occasion) {
                $photographerService = $request->photographer_service ? explode(',', $request->photographer_service) : [];
                $photographerOccasion = $request->photographer_occation ? explode(',', $request->photographer_occation) : [];
                $makeupService = $request->makeup_service ? explode(',', $request->makeup_service) : [];
                $makeupOccasion = $request->makeup_occasion ? explode(',', $request->makeup_occasion) : [];

                $filtered_items = $filtered_items->filter(function ($vendor) use ($photographerService, $photographerOccasion, $makeupService, $makeupOccasion) {
                    $vendorServices = $vendor->services ? json_decode($vendor->services, true) : [];
                    $vendorOccasions = $vendor->occasions ? json_decode($vendor->occasions, true) : [];
                    $matchesPhotographerService = !empty(array_intersect($photographerService, $vendorServices));
                    $matchesPhotographerOccasion = !empty(array_intersect($photographerOccasion, $vendorOccasions));
                    $matchesMakeupService = !empty(array_intersect($makeupService, $vendorServices));
                    $matchesMakeupOccasion = !empty(array_intersect($makeupOccasion, $vendorOccasions));

                    return $matchesPhotographerService || $matchesPhotographerOccasion || $matchesMakeupService || $matchesMakeupOccasion;
                });
            }

            $all_items = $filtered_items->values();
        }

        $response = [
            'success' => true,
            'tag' => $tag,
            'count' => $total_items,
            'data' => $all_items->all(), // Reset the keys of the collection
            'meta' => $meta,
            'cities' => $cities,
            'pagination' => [
                'current_page' => $page_no,
                'last_page' => ceil($total_items / $items_per_page),
                'per_page' => $items_per_page,
                'total' => $total_items,
            ],
            'message' => 'Data fetched successfully',
        ];
    } catch (\Throwable $th) {
        Log::error("Error in venue_or_vendor_list_data: " . $th->getMessage());
        $response = [
            'success' => false,
            'data' => [],
            'message' => $th->getMessage(),
        ];
    }
    return response()->json($response);
}






    public function venue_or_vendor_list_data(Request $request, string $category_slug, string $city_slug, string $location_slug = 'all', int $page_no = 1)
    {
        try {
            $cities = City::select('id', 'name', 'slug')->orderBy('name', 'asc')->get();
            Log::info("venue_or_vendor_list_data called with params: ", [
                'category_slug' => $category_slug,
                'city_slug' => $city_slug,
                'location_slug' => $location_slug,
                'page_no' => $page_no,
            ]);

            $items_per_page = 9;
            $offset = ($page_no - 1) * $items_per_page;

            $venue_category = VenueCategory::where('slug', $category_slug)->first();
            $vendor_category = VendorCategory::where('slug', $category_slug)->first();

            $city = City::where('slug', $city_slug)->first();
            if (!$city) {
                throw new \Exception("City not found");
            }

            $location = Location::where(['city_id' => $city->id, 'slug' => $location_slug])->first();
            $slug = "$category_slug/$city_slug/$location_slug";

            if ($venue_category) {
                $data = Venue::select(
                    'venues.id',
                    'venues.name',
                    'venues.venue_address',
                    'venues.summary',
                    'venues.veg_price',
                    'venues.nonveg_price',
                    'venues.phone',
                    'venues.images',
                    'venues.slug',
                    'venues.min_capacity',
                    'venues.max_capacity',
                    'venues.popular',
                    'venues.wb_assured',
                    'venues.place_rating',
                    'venues.venue_category_ids',
                    DB::raw('COALESCE((SELECT COUNT(*) FROM reviews WHERE reviews.product_id = venues.id), 158) as reviews_count'),
                    'locations.name as location_name',
                    'cities.name as city_name'
                )
                    ->join('locations', 'locations.id', '=', 'venues.location_id')
                    ->join('cities', 'cities.id', '=', 'venues.city_id')
                    ->where(['venues.status' => 1, 'venues.city_id' => $city->id]);

                if ($location) {
                    if ($location->is_group) {
                        $localityIds = explode(',', $location->locality_ids);
                        if (!in_array($location->id, $localityIds)) {
                            $localityIds[] = $location->id;
                        }
                        $data->whereIn('venues.location_id', $localityIds);
                    } elseif ($location_slug != 'all') {
                        $data->where('venues.location_id', $location->id);
                    }
                }

                $data->whereRaw("find_in_set(?, venues.venue_category_ids)", [$venue_category->id]);

                if ($request->guest) {
                    $params = explode(',', $request->guest);
                    $data->whereBetween('max_capacity', [$params[0], $params[1]]);
                }

                if ($request->per_plate) {
                    $params = explode(',', $request->per_plate);
                    $data->whereBetween('veg_price', [$params[0], $params[1]]);
                }

                if ($request->per_budget) {
                    $params = explode(',', $request->per_budget);
                    $data->join('budgets', 'budgets.id', '=', 'venues.budget_id')
                        ->whereBetween('budgets.min', [$params[0], $params[1]]);
                }

                if ($request->multi_localities) {
                    $group_locations = Location::whereIn('id', explode(',', $request->multi_localities))->where('is_group', 1)->get();
                    $arr = $request->multi_localities;
                    foreach ($group_locations as $list) {
                        $arr .= ',' . $list->locality_ids;
                    }
                    $params = explode(',', $arr);
                    $data->whereIn('location_id', array_unique($params));
                }

                if ($request->food_type) {
                    $food_type = $request->food_type . '_price';
                    $data->whereNotNull($food_type);
                }

                $meta = VenueListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
                    ->where('slug', $slug)->first();

                $tag = 'venues';
            } else {
                $data = Vendor::select(
                    'vendors.id',
                    'vendors.brand_name',
                    'vendors.vendor_address',
                    'vendors.package_price',
                    'vendors.phone',
                    'vendors.slug',
                    'vendors.images',
                    'vendors.popular',
                    'vendors.wb_assured',
                    'locations.name as location_name',
                    'cities.name as city_name'
                )
                    ->join('cities', 'cities.id', '=', 'vendors.city_id')
                    ->join('locations', 'locations.id', '=', 'vendors.location_id')
                    ->where([
                        'vendors.status' => 1,
                        'cities.slug' => $city_slug,
                        'vendors.vendor_category_id' => $vendor_category->id
                    ]);

                if ($location_slug != 'all') {
                    $data->where('locations.slug', $location_slug);
                }

                $meta = VendorListingMeta::select('meta_title', 'meta_description', 'meta_keywords', 'caption', 'faq')
                    ->where('slug', $slug)->first();

                $tag = 'vendors';
            }

            $total_items = $data->count();
            $venues_or_vendors = $data->orderBy('popular', 'desc')->orderBy('id', 'desc')->skip($offset)->take($items_per_page)->get();

            if ($venue_category) {
                foreach ($venues_or_vendors as $venue_or_vendor) {
                    $category_ids = explode(',', $venue_or_vendor->venue_category_ids);
                    $category_names = VenueCategory::whereIn('id', $category_ids)->pluck('name')->toArray();
                    $venue_or_vendor->venue_category_ids = implode(', ', $category_names);
                }
            }

            $response = [
                'success' => true,
                'tag' => $tag,
                'count' => $total_items,
                'data' => $venues_or_vendors,
                'meta' => $meta,
                'cities' => $cities,
                'pagination' => [
                    'current_page' => $page_no,
                    'last_page' => ceil($total_items / $items_per_page),
                    'per_page' => $items_per_page,
                    'total' => $total_items,
                ],
                'message' => 'Data fetched successfully',
            ];
        } catch (\Throwable $th) {
            Log::error("Error in venue_or_vendor_list_data: " . $th->getMessage());
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function venue_or_vendor_details(string $slug)
    {
        try {
            $data = [];

            $venue = Venue::where('slug', $slug)->first();
            if ($venue) {
                $similar_packages = Venue::select('id', 'name', 'images', 'venue_address', 'phone', 'slug', 'min_capacity', 'max_capacity', 'veg_price', 'nonveg_price', 'wb_assured')
                    ->whereIn('id', explode(',', $venue->similar_venue_ids))
                    ->get();

                $data['venue'] = $venue;
                $data['similar_packages'] = $similar_packages;
                $tag = 'venue';
                $city = City::where('id', $venue->city_id)->first();
                $reviews = Review::where('product_id', $venue->id)->get();
                $is_redirect = $venue->is_redirect;

                $city_slug = $venue->get_city ? $venue->get_city->slug : '';
                $locality = $venue->get_locality ? $venue->get_locality->slug : '';
                $category = $venue->get_category ? $venue->get_category->slug : '';

                $redirect_url = "$category/$city_slug/$locality";
            } else {

                $vendor = Vendor::where('slug', $slug)->first();

                $similar_vendors = Vendor::select('id', 'brand_name', 'package_price', 'vendor_address', 'phone', 'slug', 'images', 'wb_assured')
                    ->whereIn('id', explode(',', trim($vendor->similar_vendor_ids)))
                    ->get();

                $data['vendor'] = $vendor;
                $data['similar_vendors'] = $similar_vendors;
                $tag = 'vendor';
                $reviews = '';
                $city = City::where('id', $vendor->city_id)->first();
                $is_redirect = $vendor->is_redirect;

                $city_slug = $vendor->get_city ? $vendor->get_city->slug : '';
                $locality = $vendor->get_locality ? $vendor->get_locality->slug : '';
                $category = $vendor->get_category ? $vendor->get_category->slug : '';

                $redirect_url = "$category/$city_slug/all";
            }

            $response = [
                'success' => true,
                'tag' => $tag,
                'data' => $data,
                'city' => $city,
                'reviews' => $reviews,
                'is_redirect' => $is_redirect,
                'redirect_url' => $redirect_url,
                'message' => 'Data fetched successfully',
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
            Log::error('Error fetching venue or vendor details: ' . $th->getMessage());
        }
        return $response;
    }

    public function blog_list(Request $request)
    {
        $blogs = Blog::select('blogs.id', 'blogs.slug', 'blogs.heading', 'blogs.excerpt', 'blogs.image', 'blogs.image_alt', 'blogs.author_id', 'blogs.publish_date', 'authors.name as author_name')
            ->leftJoin('authors', 'blogs.author_id', '=', 'authors.id')
            ->where('blogs.status', 1)
            ->paginate(5);

        return response()->json([
            'status' => 'success',
            'data' => $blogs,
        ]);
    }


    public function blog_detail($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found',
            ], 404);
        }

        $popular = Blog::select('blogs.id', 'blogs.slug', 'blogs.heading', 'blogs.image', 'blogs.image_alt', 'blogs.publish_date', 'blogs.author_id', 'authors.name as author_name')
            ->leftJoin('authors', 'blogs.author_id', '=', 'authors.id')
            ->where('blogs.popular', 1)
            ->where('blogs.status', 1)
            ->orderBy('blogs.publish_date', 'desc')
            ->limit(4)
            ->get();

        $latest = Blog::select('blogs.id', 'blogs.slug', 'blogs.heading', 'blogs.image', 'blogs.image_alt', 'blogs.publish_date', 'blogs.author_id', 'authors.name as author_name')
            ->leftJoin('authors', 'blogs.author_id', '=', 'authors.id')
            ->where('blogs.status', 1)
            ->where('blogs.id', '!=', $blog->id)
            ->orderBy('blogs.publish_date', 'desc')
            ->limit(4)
            ->get();

        $author = Author::where('id', $blog->author_id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $blog,
            'author' => $author,
            'popular' => $popular,
            'latest' => $latest,
        ]);
    }

    public function business_signup(Request $request)
    {
        try {
            $is_valid_business_category = $request->business_type == 1 ? 'exists:venue_categories,slug' : 'exists:vendor_categories,slug';
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'business_type' => 'required|int|min:1|max:2',
                'business_category' => 'required|' . $is_valid_business_category,
                'email' => 'required|email|unique:business_users,email',
                'phone' => 'required|int|unique:business_users,phone|min_digits:10|max_digits:10',
                'city' => 'required|exists:cities,slug',
                'address' => 'required|string|max:255',
            ]);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
            }
            $city = City::where('slug', $request->city)->first();
            if ($request->business_type == 1) {
                $business_category = VenueCategory::where('slug', $request->business_category)->first();
            } else {
                $business_category = VendorCategory::where('slug', $request->business_category)->first();
            }
            if (!$city || !$business_category) {
                return response()->json(['success' => false, 'message' => 'Something went wrong.']);
            }

            $business_user = new BusinessUser();
            $business_user->name = $request->name;
            $business_user->business_name = $request->business_name;
            $business_user->business_type = $request->business_type;
            $business_user->business_category_id = $business_category->id;
            $business_user->email = $request->email;
            $business_user->phone = $request->phone;
            $business_user->city_id = $city->id;
            $business_user->address = $request->address;
            $business_user->save();

            $mail_data = ['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone, 'business_name' => $request->business_name, 'business_type' => $request->business_type == 1 ? 'Venue' : 'Vendor', 'business_category' => $business_category->name, 'city' => $city->name];

            if (env('MAIL_STATUS') == true) {
                Mail::to($request->email)->send(new ThanksForSignin($mail_data));
                Mail::to(env('WB_TEAM_EMAIL'))->cc(explode(',', env('WB_TEAM_CC')))->send(new NotifyReceivedUser($mail_data));
            }

            return response()->json(['success' => true, 'message' => 'Thanks for signup, your profile is in under review. Our customer executive will contact you shortly.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'err' => $th->getMessage()]);
        }
    }

    public function business_login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'phone' => 'required|int|exists:business_users,phone|min_digits:10|max_digits:10',
            ]);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
            }
            $verification_code = rand(111111, 999999);
            $business_user = BusinessUser::where('phone', $request->phone)->first();
            if ($business_user && $business_user->user_status == 1) {
                $business_user->otp_code = $verification_code;
                $business_user->save();
                if (env('INTERAKT_STATUS') == true) {
                    if (strlen($business_user->business_name) > 15) {
                        $str = str_split($business_user->business_name, 13);
                        $business_name = $str[0] . '..';
                    } else {
                        $business_name = $business_user->business_name;
                    }
                    $this->interakt_wa_msg_send($business_user->phone, $business_name, $verification_code, 'otp_login_alert');
                }

                $mail_data = ['name' => $business_user->business_name, 'otp' => $verification_code];
                if (env('MAIL_STATUS') == true) {
                    Mail::mailer('smtp2')->to($business_user->email)->send(new VerificationMail($mail_data));
                }

                return response()->json(['success' => true, 'message' => 'Success, We have just sent a verifiction code on your whatsApp number. Type your code in the field & click on login button.']);
            } else {
                return response()->json(['success' => false, 'message' => 'User is not active.']);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }

    public function business_login_process(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'phone' => 'required|int|exists:business_users,phone|min_digits:10|max_digits:10',
                'otp_code' => 'required|int',
            ]);
            if ($validate->fails()) {
                return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
            }
            $business_user = BusinessUser::where('phone', $request->phone)->first();
            if ($business_user->otp_code != $request->otp_code) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials.']);
            }
            $token = $business_user->createToken('remember_token')->plainTextToken;
            $business_user->otp_code = null;
            $business_user->remember_token = $token;
            $business_user->save();

            return response()->json(['success' => true, 'token' => $token]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }

    public function update_business_user_content(Request $request)
    {
        $user = BusinessUser::where('remember_token', $request->header('bearer'))->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid request!']);
        }

        if ($user->business_type == 1) {
            $validate = Validator::make($request->all(), [
                'business_name' => 'required|string|max:255',
                'business_category' => 'required|string|max:255',
                'city' => 'required|exists:cities,slug',
                'location' => 'required|exists:locations,slug',
                'address' => 'required|string|max:255',
                'min_capacity' => 'required|int',
                'max_capacity' => 'required|int',
                'budget_id' => 'required|int|exists:budgets,id',
                'veg_price' => 'required|int',
                'nonveg_price' => 'required|int',
            ]);

            if ($validate->fails()) {
                return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
            }

            $content_model = $user->getVenueContent;
            if (!$content_model) {
                $content_model = new VenueUserContent();
                $content_model->venue_id = $user->migrated_business_id;
            }

            $content_model->name = $request->business_name;
            $content_model->venue_address = $request->address;
            $content_model->min_capacity = $request->min_capacity;
            $content_model->max_capacity = $request->max_capacity;
            $content_model->veg_price = $request->veg_price;
            $content_model->nonveg_price = $request->nonveg_price;
            $content_model->budget_id = $request->budget_id;
            $content_model->venue_category_ids = $request->business_category;
            $content_model->start_time_morning = $request->start_time_morning != 'null' ? $request->start_time_morning : '';
            $content_model->end_time_morning = $request->end_time_morning != 'null' ? $request->end_time_morning : '';
            $content_model->start_time_evening = $request->start_time_evening != 'null' ? $request->start_time_evening : '';
            $content_model->end_time_evening = $request->end_time_evening != 'null' ? $request->end_time_evening : '';
            $content_model->area_capacity = $request->area_capacity;
        } else {
            $is_validate_yrs_exp = $request->yrs_exp != 0 ? 'required|int|max_digits:6' : '';
            $is_validate_event_completed = $request->event_completed != 0 ? 'required|int|max_digits:6' : '';
            $is_validate_package_option = $request->package_option != null ? 'required|string' : '';
            $validate = Validator::make($request->all(), [
                'city' => 'required|exists:cities,slug',
                'location' => 'required|exists:locations,slug',
                'business_category' => 'required|exists:vendor_categories,id',
                'business_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'package_price' => 'required|integer|max_digits:6',
                'yrs_exp' => $is_validate_yrs_exp,
                'event_completed' => $is_validate_event_completed,
                'package_option' => $is_validate_package_option,
            ]);

            if ($validate->fails()) {
                return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
            }

            $content_model = $user->getVendorContent;
            if (!$content_model) {
                $content_model = new VendorUserContent();
                $content_model->vendor_id = $user->migrated_business_id;
            }
            $content_model->brand_name = $request->business_name;
            $content_model->vendor_address = $request->address;
            $content_model->package_price = $request->package_price;
            $content_model->yrs_exp = $request->yrs_exp;
            $content_model->event_completed = $request->event_completed;
            $content_model->package_option = $request->package_option;
            $content_model->vendor_category_id = $request->business_category;
        }

        try {
            $city_id = City::where('slug', $request->city)->first()->id;
            $location_id = Location::where('slug', $request->location)->first()->id;

            // $slug = str_replace(" ", "-", strtolower($request->business_name));

            $content_model->city_id = $city_id;
            $content_model->location_id = $location_id;

            $images_arr = $content_model->images ? explode(',', $content_model->images) : [];
            if (is_array($request->images)) {
                $image_file_tag = $user->business_type == 1 ? 'venue_' : 'vendor_';
                foreach ($request->images as $key => $image) {
                    $ext = $image->getClientOriginalExtension();
                    $sub_str = substr($request->business_name, 0, 5);
                    $file_name = $image_file_tag . strtolower(str_replace(' ', '_', $sub_str)) . '_' . time() + $key . '.' . $ext;
                    $path = "uploads/$file_name";
                    Storage::put('public/' . $path, file_get_contents($image));
                    array_push($images_arr, $file_name);
                }
                $content_model->images = implode(',', $images_arr);
                $user->images_status = 1; // 1=pending;
            }
            $content_model->save();
            $user->content_status = 1; // 1=pending;
            $user->save();

            return response()->json(['success' => true, 'message' => 'content updated.', 'data' => $this->fetch_business_user_and_content($request)->original['data']['content']]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Someting went wrong.', 'error' => $th->getMessage()]);
        }
    }

    public function fetch_business_user_and_content(Request $request)
    {
        try {
            $user = BusinessUser::select(
                'business_users.name',
                'business_users.phone',
                'business_users.email',
                'cities.slug as city',
                'business_users.address',
                'business_users.about',
                'business_users.business_type',
                'business_users.content_status',
                'business_users.images_status',
                'business_users.migrated_business_id',
                'business_users.images_status',
            )->join('cities', 'cities.id', 'business_users.city_id')
                ->where('remember_token', $request->header('bearer'))->first();

            if ($user->business_type == 1) {
                $model = VenueUserContent::from('venue_user_contents as model')->where('model.venue_id', $user->migrated_business_id);
                if ($model->count() === 0) {
                    $model = Venue::from('venues as model')->where('model.id', $user->migrated_business_id)->withTrashed()->whereNull('model.deleted_at');
                }
                $model->select(
                    'model.name as business_name',
                    'model.venue_address as address',
                    'model.min_capacity',
                    'model.max_capacity',
                    'model.veg_price',
                    'model.nonveg_price',
                    'model.budget_id',
                    'model.venue_category_ids as business_category',
                    'model.start_time_morning',
                    'model.end_time_morning',
                    'model.start_time_evening',
                    'model.end_time_evening',
                    'model.area_capacity',
                    'model.images',
                    'cities.slug as city',
                    'locations.slug as location',
                );
            } else {
                $model = VendorUserContent::from('vendor_user_contents as model')->where('model.vendor_id', $user->migrated_business_id);
                if ($model->count() === 0) {
                    $model = Vendor::from('vendors as model')->where('model.id', $user->migrated_business_id)->withTrashed()->whereNull('model.deleted_at');
                }
                $model->select(
                    'model.brand_name as business_name',
                    'model.vendor_category_id as business_category',
                    'model.vendor_address as address',
                    'model.package_price',
                    'model.yrs_exp',
                    'model.event_completed',
                    'model.package_option',
                    'model.images',
                    'cities.slug as city',
                    'locations.slug as location',
                );
            }

            $content = $model->join('cities', 'cities.id', 'model.city_id')->join('locations', 'locations.id', 'model.location_id')->first();

            return response()->json(['success' => true, 'message' => 'Data fetched successfully.', 'data' => compact('user', 'content')]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'data' => []]);
        }
    }

    public function update_business_user(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city' => 'required|exists:cities,slug',
            'email' => 'required|email',
            'phone' => 'required|numeric|min_digits:10|max_digits:10',
            'address' => 'required|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
        }
        try {
            $user = BusinessUser::where('remember_token', $request->header('bearer'))->first();
            $exist_with_phone = BusinessUser::where('phone', $request->phone)->whereNot('id', $user->id)->get();
            $exist_with_email = BusinessUser::where('email', $request->email)->whereNot('id', $user->id)->get();
            if (count($exist_with_phone) > 0) {
                return response()->json(['success' => false, 'message' => 'invalid phone number.']);
            } elseif (count($exist_with_email) > 0) {
                return response()->json(['success' => false, 'message' => 'invalid email.']);
            }
            $city_id = City::where('slug', $request->city)->first()->id;
            $user->name = $request->name;
            $user->city_id = $city_id;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->about = $request->about;
            $user->save();

            $data = BusinessUser::select(
                'business_users.business_type',
                'business_users.business_category_id',
                'cities.slug as city',
                'business_users.name',
                'business_users.business_name',
                'business_users.email',
                'business_users.phone',
                'business_users.address',
                'business_users.user_status',
                'business_users.content_status',
                'business_users.images_status',
                'business_users.migrated_business_id',
            )->join('cities', 'cities.id', 'business_users.city_id')->where('remember_token', $request->header('bearer'))->first();

            return response()->json(['success' => true, 'message' => 'User updated!', 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'log' => 'th']);
        }
    }

    //for website users auth, dashboard, and profile related methods
    public function user_signup(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|int|unique:users,phone|min_digits:10|max_digits:10',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
        }

        try {
            $verification_code = rand(111111, 999999);

            $signup_request = UserSignupRequest::where('phone', $request->phone)->first();
            if (!$signup_request) {
                $signup_request = new UserSignupRequest();
            }
            $signup_request->phone = $request->phone;
            $signup_request->otp_code = $verification_code;
            $signup_request->save();

            // if (env('INTERAKT_STATUS') == true) {
            //     if (strlen($request->name) > 15) {
            //         $str = str_split($request->name, 13);
            //         $user_name = $str[0] . "..";
            //     } else {
            //         $user_name = $request->name;
            //     }
            //     $this->interakt_wa_msg_send($request->phone, $user_name, $verification_code, 'otp_login_alert');
            // }

            if (env('MAIL_STATUS') == true) {
                $mail_data = ['name' => $request->name, 'otp' => $verification_code];
                Mail::mailer('smtp')->to($request->email)->send(new VerificationMail($mail_data));
            }

            return response()->json(['success' => true, 'message' => 'Signup processed successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function user_signup_process(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city' => 'required|exists:cities,slug',
            'location' => 'required|exists:locations,slug',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|int|unique:users,phone|min_digits:10|max_digits:10',
            'user_type' => 'required|int|max:3',
            'password' => 'required|string|min:8',
            'otp_code' => 'required|int|min_digits:6|max_digits:6',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
        }
        try {
            $signup_request = UserSignupRequest::where('phone', $request->phone)->first();
            if (!$signup_request || ($signup_request->otp_code != $request->otp_code)) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials.']);
            }

            $user = new User();
            $city_id = City::where('slug', $request->city)->first()->id;
            $location_id = Location::where('slug', $request->location)->first()->id;

            $user->city_id = $city_id;
            $user->location_id = $location_id;
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->user_type = $request->user_type;
            $user->password = password_hash($request->password, PASSWORD_DEFAULT);
            $user->phone_verified_at = date('Y-m-d H:i:s');
            $user->save();

            $token = $user->createToken('remember_token')->plainTextToken;
            $user->remember_token = $token;
            $user->save();

            $signup_request->delete();

            $res_user = ['name' => $user->name, 'city' => $user->get_city->slug, 'location' => $user->get_location->slug, 'venues_liked' => $user->venues_liked];

            return response()->json(['success' => true, 'token' => $token, 'user' => $res_user]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }

    public function user_login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
        }

        $user = User::where('email', $request->username)->orWhere('phone', $request->username)->first();
        if (!$user || !password_verify($request->password, $user->password) || $user->phone_verified_at == null) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.']);
        }

        $token = $user->createToken('remember_token')->plainTextToken;
        $user->remember_token = $token;
        $user->save();

        $res_user = ['name' => $user->name, 'city' => $user->get_city->slug, 'location' => $user->get_location->slug, 'venues_liked' => $user->venues_liked];

        return response()->json(['success' => true, 'token' => $token, 'user' => $res_user]);
    }

    public function get_user(Request $request)
    {
        $user = User::select(
            'users.id',
            'users.name',
            'users.phone',
            'users.email',
            'cities.slug as city',
            'users.venues_liked',
        )->join('cities', 'cities.id', 'users.city_id')
            ->where('users.remember_token', $request->header('bearer'))->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid request.']);
        }
        $venues_liked = Venue::select(
            'id',
            'name',
            'images',
            'venue_address',
            'phone',
            'slug',
            'min_capacity',
            'max_capacity',
            'veg_price',
            'nonveg_price'
        )->whereIn('id', explode(',', $user->venues_liked))->get();

        $user->venues_liked = $venues_liked;

        return response()->json(['success' => true, 'message' => 'Data fetched successfully.', 'user' => $user]);
    }

    public function venue_liked_by_user(Request $request, int $venue_id)
    {
        try {
            $user = User::where('remember_token', $request->header('bearer'))->first();
            $liked_arr = $user->venues_liked != null ? explode(',', $user->venues_liked) : [];
            $index = array_search($venue_id, $liked_arr);
            if ($index === false) {
                array_push($liked_arr, $venue_id);
            } else {
                unset($liked_arr[$index]);
            }

            if (count($liked_arr) > 0) {
                $user->venues_liked = implode(',', $liked_arr);
                $user->save();
            }

            return response()->json(['success' => true, 'message' => 'Liked success.', 'liked' => $liked_arr]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function click_conversion_handle(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'url' => 'required|string|max:255',
            'venue_id' => 'required|int|exists:venues,id',
            'type' => 'required|string|max:20',
            'request_handle_by' => 'required|string|max:5',
        ]);

        if ($validate->fails()) {
            return response()->json(['success' => false, 'message' => $validate->errors()->first()]);
        }

        try {
            $exist_count = WebAnalytics::where('venue_id', $request->venue_id)->count();

            $model = new WebAnalytics();
            $model->venue_id = $request->venue_id;
            $model->url = $request->url;
            $model->type = $request->type;
            $model->request_handle_by = $request->request_handle_by;
            $model->click_count = $exist_count + 1;
            $model->save();

            return response()->json(['success' => true, 'message' => 'Request resolved successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function storereview(Request $request)
    {
        try {
            $data = $request->json()->all();
            $review = Review::create([
                'users_name' => $data['name'],
                'comment' => $data['comment'],
                'c_number' => $data['number'],
                'rating' => $data['rating'],
                'product_id' => $data['product_id'],
                'product_for' => $data['product_for'],
                'status' => 0,
                'is_read' => 0,
            ]);

            return response()->json(['message' => 'Review saved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save review'], 500);
        }
    }
}
