<?php

use App\Http\Controllers;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\FroalaController;
use App\Http\Controllers\MegaDatabaseChangeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('convert_all_the_localities_into_group', [MegaDatabaseChangeController::class, 'convert_all_the_localities_into_group']);
Route::get('yash', [MegaDatabaseChangeController::class, 'rename_all_venue_remove_locality_and_city_from_venue_name']);
Route::get('hi/{country?}/{city?}/{location?}', [MegaDatabaseChangeController::class, 'getLocationCoordinates']);


Route::group(['middleware' => 'AuthCheck'], function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('send_otp', [AuthController::class, 'send_otp'])->name('send_otp');
    Route::post('verify_otp', [AuthController::class, 'verify_otp'])->name('verify_otp');
});

Route::group(['middleware' => ['admin', 'checkLoginTime']], function () {
    Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');
    /*
    |--------------------------------------------------------------------------
    | Account routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('account')->group(function () {
        Route::view('/list', 'account_control.list')->name('account.list');
        Route::get('/list_ajax', [AccountController::class, 'ajax_list'])->name('account.ajax_list');
        Route::get('/manage/{account_id?}', [AccountController::class, 'manage'])->name('account.manage');
        Route::post('/manage_process/{account_id?}', [AccountController::class, 'manage_process'])->name('account.manage_process');
        Route::post('/phone/validate', [AccountController::class, 'validatePhone'])->name('phone.validate');
        Route::get('/user/delete/{user_id?}', [AccountController::class, 'delete'])->name('account.delete');
        Route::get('/user/update-status/{user_id?}/{value?}', [AccountController::class, 'updateStatus'])->name('account.update.status');
        Route::post('/user/update-login-time', [AccountController::class, 'updateLoginTime'])->name('account.update.updateLoginTime');
        Route::get('/user/update-is-all-time-login/{user_id?}/{value?}', [AccountController::class, 'updateIsAllTimeLogin'])->name('account.update.isAllTimeLogin');
    });


    /*
    |--------------------------------------------------------------------------
    | Devices routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('manage_devices')->group(function () {
        Route::get('permit_or_not_more_device_for_login_for_an_acjcount/{member_id?}/{value?}', [DeviceController::class, 'permit_or_not_more_device_for_login_for_an_account'])->name('admin.permit.unpermit.canadddevice');
        Route::get('delete_device/{device_id?}', [DeviceController::class, 'delete_device'])->name('admin.devices.manage.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Roles and Permissions routes
    |--------------------------------------------------------------------------
     */
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    /*
    |--------------------------------------------------------------------------
    | External api routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('external_api')->group(function () {
        Route::get('/maps_review', [ExternalApiController::class, 'maps_review'])->name('api.maps_review');
        Route::post('/maps_review_fetch', [ExternalApiController::class, 'maps_review_fetch'])->name('api.maps_review_fetch');
        Route::get('/list', [ExternalApiController::class, 'list'])->name('api.list');
        Route::get('/ajax_list', [ExternalApiController::class, 'ajax'])->name('api.ajax');
    });

    /*
    |--------------------------------------------------------------------------
    | Other routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('others')->group(function () {
        //City routes
        Route::prefix('city')->controller(Controllers\CityController::class)->group(function () {
            Route::get('/list', 'list')->name('city.list');
            Route::post('/manage_process', 'manage_process')->name('city.manage_process');
            Route::get('/delete/{city_id}', 'delete')->name('city.delete');
        });

        //Location routes
        Route::prefix('location')->controller(Controllers\LocationController::class)->group(function () {
            Route::get('/list', 'list')->name('location.list');
            Route::get('/ajax_list', 'ajax_list')->name('location.ajax_list');
            Route::get('/edit_ajax/{location_id?}', 'edit_ajax')->name('location.edit');
            Route::post('/manage_process/{location_id?}', 'manage_process')->name('location.manage_process');
            Route::get('/delete/{city_id}', 'delete')->name('location.delete');

            //Location group routes
            Route::get('/group/add', 'group_manage')->name('location.add_group');
            Route::get('/group/edit/{group_id?}', 'group_manage')->name('location.edit_group');
            Route::post('/group/manage_process/{group_id?}', 'group_manage_process')->name('location.group_manage_process');

            //Ajax route
            Route::get('/get_locations/{city_id?}', 'get_locations')->name('location.get_locations');
        });

        //Meal routes
        Route::prefix('meal')->controller(Controllers\MealController::class)->group(function () {
            Route::get('/list', 'list')->name('meal.list');
            Route::get('/edit/{meal_id?}', 'edit_ajax')->name('meal.edit');
            Route::post('/manage_process/{meal_id?}', 'manage_process')->name('meal.manage_process');
            Route::get('/delete/{meal_id}', 'delete')->name('meal.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | Web Analytics routes
        |--------------------------------------------------------------------------
         */
        Route::prefix('web_analytics')->controller(Controllers\WebAnalyticsController::class)->group(function () {
            Route::view('/list', 'web_analytics.list')->name('analytics.list');
            Route::get('/ajax_list', 'ajax_list')->name('analytics.ajax_list');
        });

        //Business user routes
        Route::prefix('business_user')->group(function () {
            Route::controller(Controllers\BusinessUserController::class)->group(function () {
                Route::get('/list', 'list')->name('business_user.list');
                Route::get('/ajax_list', 'ajax_list')->name('business_user.ajax_list');
                Route::post('/migrate', 'user_migrate')->name('business_user.migrate');
                Route::get('/update_users_status/{user_id}/{status}', 'update_user_status')->name('business_user.update_user_status');
                Route::get('/delete/{user_id?}', 'user_delete')->name('business_user.delete');
                Route::post('/user_edit_process', 'user_edit_process')->name('business_user.edit_process');
                Route::get('/update_content_status/{user_id}/{status}', 'update_content_status')->name('business_user.update_content_status');
                Route::get('/update_images_status/{user_id?}/{status?}', 'update_images_status')->name('business_user.update_images_status');
                //Ajax routes
                Route::get('/fetch_listed_vendors', 'fetch_listed_vendors')->name('listed_vendors.fetch');
                Route::get('/fetch_listed_venues/{business_type?}', 'fetch_listed_venues')->name('listed_venues.fetch');
                Route::get('/user_edit/{user_id?}', 'user_edit')->name('business_user.edit');
            });

            //Business user content routes
            Route::controller(Controllers\BusinessUserContentController::class)->group(function () {
                Route::get('/manage_content/{user_id?}', 'manage_content')->name('business_user.manage_content');
                Route::get('/manage_images/{user_id?}', 'manage_images')->name('business_user.manage_images');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Venue routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('venue')->group(function () {
        //Venue Routes
        Route::controller(Controllers\VenueController::class)->group(function () {
            Route::view('/list', 'venue.list')->name('venue.list');
            Route::get('/ajax_list', 'ajax_list')->name('venue.ajax_list');
            Route::get('/add', 'manage')->name('venue.add');
            Route::get('/edit/{venue_id?}', 'manage')->name('venue.edit');
            Route::post('/manage_process/{venue_id}', 'manage_process')->name('venue.manage_process');
            Route::post('/update_phone_number/{venue_id?}', 'update_phone_number')->name('venue.update_phoneNumber');
            Route::post('/update_meta/{venue_id?}', 'update_meta')->name('venue.update_meta');
            Route::post('/update_faq/{venue_id?}', 'update_faq')->name('venue.update_faq');
            // delete venue
            Route::delete('/destroy/{id}', 'destroy')->name('venue.destroy');
            //ajax route
            Route::get('/fetch_meta/{venue_id?}', 'fetch_meta')->name('venue.fetch_meta');
            Route::get('/fetch_faq/{venue_id?}', 'fetch_faq')->name('venue.fetch_faq');
            Route::get('/get_similar_venues/{city_id?}', 'get_similar_venues')->name('venue.get_similar_venues');
            Route::get('/update_popular_status/{venue_id?}/{status?}', 'update_popular_status')->name('venue.update_popular_status');
            Route::get('/update_status/{venue_id?}/{status?}', 'update_status')->name('venue.update_status');
            Route::get('/update_wb_assured_status/{venue_id?}/{status?}', 'update_wb_assured_status')->name('venue.update_wb_assured_status');
            //Venue Images routes
            Route::get('/images/manage/{venue_id?}', 'manage_images')->name('venue.manage_images');
            Route::post('/images/manage_process/{venue_id}', 'images_manage_process')->name('venue.images.manage_process');
            Route::post('/images/delete/{venue_id?}', 'image_delete')->name('venue.image.delete');
            Route::post('/images/update_sorting/{venue_id?}', 'update_images_sorting')->name('venue.images.update_sorting');
        });

        //VenueCategory routes
        Route::prefix('venue_category')->controller(Controllers\VenueCategoryController::class)->group(function () {
            Route::get('/list', 'list')->name('venue_category.list');
            Route::get('/edit/{category_id?}', 'edit_ajax')->name('venue_category.edit');
            Route::post('/manage_process/{category_id?}', 'manage_process')->name('venue_category.manage_process');
            Route::get('/delete/{category_id?}', 'delete')->name('venue_category.delete');
        });

        //Venue listing meta
        Route::prefix('listing_meta')->controller(Controllers\VenueListingMetaController::class)->group(function () {
            Route::view('/list', 'venue.listing_meta_list')->name('venue.listing_meta.list');
            Route::get('/ajax_list', 'ajax_list')->name('venue.listing_meta.ajax_list');
            Route::get('manage/{meta_id?}', 'manage')->name('venue.listing_meta.manage');
            Route::get('/delete/{meta_id?}', 'meta_delete')->name('venue.listing_meta.delete');
            Route::post('/manage_process/{meta_id?}', 'manage_process')->name('venue.listing_meta.manage_process');
            Route::post('/update_faq/{meta_id?}', 'update_faq')->name('venue.listing_meta.update_faq');

            //Ajax routes
            Route::get('/update_status/{meta_id?}/{status?}', 'update_status')->name('venue.listing_meta.update_status');
            Route::get('/fetch_faq/{meta_id?}', 'fetch_faq')->name('venue.listing_meta.fetch_faq');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Pahe Listing routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('page_listing_meta')->controller(Controllers\PageController::class)->group(function () {
        Route::view('/list', 'page_listing_meta.listing_meta_list')->name('page_listing_meta.listing_meta.list');
        Route::get('/ajax_list', 'ajax_list')->name('page_listing_meta.listing_meta.ajax_list');
        Route::get('manage/{meta_id?}', 'manage')->name('page_listing_meta.listing_meta.manage');
        Route::get('/delete/{meta_id?}', 'meta_delete')->name('page_listing_meta.listing_meta.delete');
        Route::post('/manage_process/{meta_id?}', 'manage_process')->name('page_listing_meta.listing_meta.manage_process');
        Route::post('/update_faq/{meta_id?}', 'update_faq')->name('page_listing_meta.listing_meta.update_faq');

        //Ajax routes
        Route::get('/update_status/{meta_id?}/{status?}', 'update_status')->name('page_listing_meta.listing_meta.update_status');
        Route::get('/fetch_faq/{meta_id?}', 'fetch_faq')->name('page_listing_meta.listing_meta.fetch_faq');
    });

    /*
    |--------------------------------------------------------------------------
    | Review routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('review')->group(function () {
        Route::controller(Controllers\ReviewController::class)->group(function () {
            Route::get('/list', 'list')->name('review.list');
            Route::get('/ajax_list', 'ajax_list')->name('review.ajax_list');
            Route::get('/add', 'manage')->name('review.add');
            Route::get('/edit/{review_id?}', 'manage')->name('review.edit');
            Route::post('/manage_process/{review_id}', 'manage_process')->name('review.manage_process');
            // delete review
            Route::delete('/destroy/{id}', 'destroy')->name('review.destroy');
            // ajax routes
            Route::get('/update_review_status/{review_id?}/{status?}', 'update_review_status')->name('review.update_review_status');
            Route::get('/get-venues', 'getVenues')->name('review.getvenues');
            Route::get('/get-vendors', 'getVendors')->name('review.getvendors');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Ivr Numbers routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('c_numbers')->group(function () {
        Route::controller(Controllers\CompanyNumber::class)->group(function () {
            Route::get('/list', 'list')->name('c_num.list');
            Route::get('/ajax_list', 'ajax_list')->name('c_num.ajax_list');
            Route::post('/manage_process/{c_num_id}', 'manage_process')->name('c_num.manage_process');
            // delete c_num
            Route::delete('/destroy/{id}', 'destroy')->name('c_num.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Blog routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('blog')->group(function () {
        Route::controller(Controllers\BlogController::class)->group(function () {
            Route::get('/list', 'list')->name('blog.list');
            Route::get('/ajax_list', 'ajax_list')->name('blog.ajax_list');

            Route::get('/manage/{blog_id?}', 'manage')->name('blog.manage');
            Route::post('/manage_process/{blog_id?}', 'manage_process')->name('blog.manage_process');
            Route::get('/update_popular_status/{blog_id?}/{status?}', 'update_popular_status')->name('blog.popular');
            Route::get('/update_blog_status/{blog_id?}/{status?}', 'update_blog_status')->name('blog.status');


            Route::get('/check-slug/{slug?}', 'checkSlug')->name('check-slug');

            // delete blog
            Route::post('/destroy/{id}', 'destroy')->name('blog.destroy');
        });

        Route::post('froala/upload_image', [FroalaController::class, 'uploadImage'])->name('froala.upload_image');
        Route::post('froala/upload_video', [FroalaController::class, 'uploadVideo'])->name('froala.upload_video');
        Route::get('froala/load_images', [FroalaController::class, 'loadImages'])->name('froala.load_images');
        Route::post('froala/delete_image', [FroalaController::class, 'deleteImage'])->name('froala.delete_image');

        Route::prefix('authors')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('author.list');
            Route::get('/create', [AuthorController::class, 'create'])->name('author.create');
            Route::post('/store', [AuthorController::class, 'store'])->name('author.store');
            Route::get('/edit/{id}', [AuthorController::class, 'edit'])->name('author.edit');
            Route::post('/update/{id}', [AuthorController::class, 'update'])->name('author.update');
            Route::get('/delete/{id}', [AuthorController::class, 'delete'])->name('author.delete');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Vendor routes
    |--------------------------------------------------------------------------
     */
    Route::prefix('vendor')->group(function () {
        Route::controller(Controllers\VendorController::class)->group(function () {
            Route::view('/list', 'vendor.list')->name('vendor.list');
            Route::get('/ajax_list', 'ajax_list')->name('vendor.ajax_list');
            Route::get('/add', 'manage')->name('vendor.add');
            Route::get('/edit/{vendor_id?}', 'manage')->name('vendor.edit');
            Route::post('/update_phone_number/{vendor_id?}', 'update_phone_number')->name('vendor.update_phoneNumber');
            Route::get('/delete/{vendor_id?}', 'delete')->name('vendor.delete');
            Route::post('/manage_process/{vendor_id}', 'manage_process')->name('vendor.manage_process');
            Route::post('/update_meta/{vendor_id?}', 'update_meta')->name('vendor.update_meta');
            // delete vendor
            Route::delete('/destroy/{id}', 'destroy')->name('vendor.destroy');
            //ajax route
            Route::get('/fetch_meta/{vendor_id?}', 'fetch_meta')->name('vendor.fetch_meta');
            Route::get('/get_similar_vendors/{category_id?}/{city_id?}', 'get_similar_vendors')->name('vendor.get_similar_vendors');
            Route::get('/update_status/{vendor_id?}/{status?}', 'update_status')->name('vendor.update_status');
            Route::get('/update_popular_status/{vendor_id?}/{status?}', 'update_popular_status')->name('vendor.update_popular_status');
            Route::get('/update_wb_assured_status/{vendor_id?}/{status?}', 'update_wb_assured_status')->name('vendor.update_wb_assured_status');
            //Vendor Images routes
            Route::get('/images/manage/{vendor_id?}', 'manage_images')->name('vendor.manage_images');
            Route::post('/images/manage_process/{vendor_id}', 'images_manage_process')->name('vendor.images.manage_process');
            Route::post('/images/delete/{vendor_id?}', 'image_delete')->name('vendor.image.delete');
            Route::post('/images/update_sorting/{vendor_id?}', 'update_images_sorting')->name('vendor.images.update_sorting');
        });

        //Vendor listing meta
        Route::prefix('listing_meta')->controller(Controllers\VendorListingMetaController::class)->group(function () {
            Route::get('/list', 'list')->name('vendor.listing_meta.list');
            Route::get('/ajax_list', 'ajax_list')->name('vendor.listing_meta.ajax_list');
            Route::get('manage/{meta_id?}', 'manage')->name('vendor.listing_meta.manage');
            Route::post('/manage_process/{meta_id?}', 'manage_process')->name('vendor.listing_meta.manage_process');
            Route::get('/delete/{meta_id?}', 'meta_delete')->name('vendor.listing_meta.delete');
            Route::post('/update_faq/{meta_id?}', 'update_faq')->name('vendor.listing_meta.update_faq');
            //Ajax routes
            Route::get('/update_status/{meta_id?}/{status?}', 'update_status')->name('vendor.listing_meta.update_status');
            Route::get('/fetch_faq/{meta_id?}', 'fetch_faq')->name('vendor.listing_meta.fetch_faq');
        });
    });
});
