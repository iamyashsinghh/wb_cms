<?php

use App\Models\Review;
use App\Models\Venue;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::controller(Controllers\ApiController::class)->group(function () {
    Route::get('home_page/{city_slug?}', 'home_page');
    Route::get('popular_venues/{category_slug}/{city_slug}', 'popular_venues');
    Route::get('state_management', 'state_management');
    Route::get('venue_or_vendor_list/{category_slug}/{city_slug}/{location_slug?}/{page_no?}', 'venue_or_vendor_list');
    Route::get('venue_or_vendor_details/{slug}', 'venue_or_vendor_details');
    Route::get('cities', 'cities');
    Route::get('locations/{city_slug}', 'locations');
    Route::get('/budgets', 'budgets');
    Route::get('/get_json_reviews/{place_id}', 'get_json_reviews');
    Route::get('/get_json_reviews_site/{product_id}', 'get_json_reviews_site');
    Route::get('/get_venue_area_cap/{venue_id}', 'get_venue_area_cap')->name('review.store');
    Route::post('storereview', 'storereview');
    Route::get('search_form_result_venue', 'search_form_result_venue');
    Route::get('search_form_result_vendor', 'search_form_result_vendor');
    Route::get('get_all_venues', 'get_all_venues');
    Route::get('get_all_vendors', 'get_all_vendors');
    Route::get('venues_vendor_page_data/{city?}/{type?}', 'venues_vendor_page_data');

    Route::get('blog_list', 'blog_list');


    Route::get('superb', function() {
        return Venue::select('id', 'name')->whereNull('place_rating')->where('city_id', 1)->count();
    });
    Route::get('delete_review', function() {
        $productIds = Review::select('product_id')
            ->groupBy('product_id')
            ->havingRaw('COUNT(*) > 5')
            ->pluck('product_id');

        foreach ($productIds as $productId) {
            $reviewsToDelete = Review::where('product_id', $productId)
                ->orderBy('created_at', 'asc')
                ->skip(5)
                ->take(PHP_INT_MAX)
                ->pluck('id');

            Review::whereIn('id', $reviewsToDelete)->delete();
        }

        return 'Reviews deleted successfully, leaving the first 5 for each product_id.';
    });

    //For Web Analytcs
    Route::post('click_conversion_handle', 'click_conversion_handle');

    //Vendor users routes
    Route::prefix('business/')->group(function () {
        Route::post('signup', 'business_signup');
        Route::post('login', 'business_login');
        Route::post('login_process', 'business_login_process');

        Route::middleware('VendorAuth')->group(function () {
            Route::post('update_user', 'update_business_user');
            Route::post('update_user_content', 'update_business_user_content');
            Route::get('fetch_user_and_content', 'fetch_business_user_and_content');
        });
    });

    Route::prefix('user/')->group(function () {
        //Website users routes
        Route::post('signup', 'user_signup');
        Route::post('signup_process', 'user_signup_process');
        Route::post('login', 'user_login');

        Route::middleware('WebsiteUserAuth')->group(function () {
            Route::get('venue_liked_by_user/{venue_id}', 'venue_liked_by_user');
            Route::get('get_user', 'get_user');
        });
    });

    // Route::get('shadab_links', 'shadab_links');
});



// Routes for migrate & refactor database.
// Route::get('/get_common_faqs', [RefactorController::class, 'get_common_faqs']);
// Route::get('/budget_refactor', [RefactorController::class, 'budget_refactor']);
// Route::get('/city_refactor', [RefactorController::class, 'city_refactor']);
// Route::get('/location_refactor', [RefactorController::class, 'location_refactor']);
// Route::get('/meal_refactor', [RefactorController::class, 'meal_refactor']);
// Route::get('/venue_category_refactor', [RefactorController::class, 'venue_category_refactor']);
// Route::get('/venue_refactor', [RefactorController::class, 'venue_refactor']);
// Route::get('/vendor_category_refactor', [RefactorController::class, 'vendor_category_refactor']);
// Route::get('/vendor_refactor', [RefactorController::class, 'vendor_refactor']);

// Route::get('/venue_listing_meta_refactor', [RefactorController::class, 'venue_listing_meta_refactor']);
// Route::get('/vendor_listing_meta_refactor', [RefactorController::class, 'vendor_listing_meta_refactor']);

//04:35 - 16%
