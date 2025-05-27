<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use App\Models\Vendor;
use App\Models\C_Number;
use Illuminate\Support\Facades\Log;
use App\Models\VendorCategory;
use App\Models\VendorUserContent;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function ajax_list()
    {
        $vendors = Vendor::select(
            'vendors.id',
            'vendors.brand_name',
            'vendor_categories.name',
            'vendors.phone',
            'cities.name as city',
            'locations.name as locality',
            'vendors.wb_assured',
            'vendors.popular',
            'vendors.status',
            'vendors.images',
            'vendors.id as action',
        )->join('cities', 'cities.id', 'vendors.city_id')
            ->join('locations', 'locations.id', 'vendors.location_id')
            ->join('vendor_categories', 'vendor_categories.id', 'vendors.vendor_category_id');
        return datatables($vendors)->make(false);
    }
    public function destroy($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found.'], 404);
        }
        if ($vendor->delete()) {
            $msg = 'Vendor deleted successfully.';
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } else {
            $msg = 'Unable to delete.';
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $msg]);
        }
        return view('vendor.list');

        // return response()->json(['message' => '']);
    }
    public function manage($vendor_id = 0)
    {
        $cities = City::select('id', 'name')->orderby('name', 'asc')->get();
        $vendor_categories = VendorCategory::select('id', 'name')->orderBy('name', 'asc')->get();
        $all_services = [
            'traditional', 'candid', 'pre-wedding', 'cinematographic', 'drone-shoots', 'photobooth', 'live-screening',
            'airbrush-makeup', 'party-makeup', 'hd-makeup', 'birdal-makeup', 'engagement-makeup', 'outstation-makeup', 'haldimakeup-mehndi-cocktail-roka'
        ];

        $all_occasions = [
            'roka', 'sagan', 'engagement', 'haldi-mehndi', 'cocktail', 'wedding', 'reception', 'anniversary', 'mata-ki-chowki',
            'birthday', 'corporate-event', 'baby-shower'
        ];
        $all_services = json_encode($all_services);
        $all_occasions = json_encode($all_occasions);

        if ($vendor_id > 0) {
            $page_heading = "Edit Vendor";
            $vendor = Vendor::find($vendor_id);
            $similar_vendors = Vendor::select('vendors.id', 'vendors.place_rating', 'cities.name as city', 'vendors.brand_name', 'vendor_categories.name as vendor_category')
                ->join('vendor_categories', 'vendor_categories.id', 'vendors.vendor_category_id')
                ->join('cities', 'cities.id', 'vendors.city_id')
                ->where(['vendor_category_id' => $vendor->vendor_category_id, 'city_id' => $vendor->city_id])->orderBy('brand_name', 'asc')->get();
            $locations = Location::select('id', 'name')->where('city_id', $vendor->city_id)->get();
        } else {
            $nextCompanyNumber = C_Number::where('is_next', 1)->first();
            if (!$nextCompanyNumber) {
                // If no company number is marked as next, default to the first one
                $nextCompanyNumber = C_Number::orderBy('id')->first();
            }
            $page_heading = "Add Vendor";
            $vendor = json_decode(json_encode([
                'id' => 0,
                'city_id' => '',
                'vendor_category_id' => '',
                'related_location_ids' => '',
                'place_rating' => '',
                'yrs_exp' => '',
                'event_completed' => '',
                'location_id' => '',
                'category_id' => '',
                'brand_name' => '',
                'slug' => '',
                'phone' => $nextCompanyNumber->tata_numbers,
                'vendor_address' => '',
                'package_price' => '',
                'meta_title' => '',
                'services' => $all_services,
                'occasions' => $all_occasions,
                'meta_keywords' => '',
                'meta_description' => '',
                'summary' => '',
                'similar_vendor_ids' => '',
                'air_brush_makeup_price' => '',
                'hd_bridal_makeup_price' => '',
                'engagement_makeup_price' => '',
                'party_makeup_price' => '',
                'cinematography_price' => '',
                'candid_photography_price' => '',
                'traditional_photography_price' => '',
                'traditional_video_price' => '',
                'pre_wedding_photoshoot_price' => '',
                'bridal_mehndi_price' => '',
                'engagement_mehndi_price' => '',
                'albums_price' => '',
                'package_option' => null
            ]));
            $similar_vendors = [];
            $locations = [];
        }
        // return $vendor;
        return view('vendor.manage', compact('cities', 'locations', 'page_heading', 'vendor_categories', 'similar_vendors', 'vendor'));
    }
    public function manage_process(Request $request, $vendor_id = 0)
    {
        if ($vendor_id > 0) {
            $vendor = Vendor::find($vendor_id);
            $msg = "Vendor updated successfully.";
        } else {
            $vendor = new Vendor();
            $msg = "Vendor added successfully.";
        }

        try {
            $vendor->city_id = $request->city;
            $vendor->location_id = $request->location;
            $vendor->related_location_ids = is_array($request->related_locations) ? implode(",", $request->related_locations) : null;
            $vendor->vendor_category_id = $request->vendor_category;
            $vendor->brand_name = $request->brand_name;
            $vendor->slug = $request->slug;
            $vendor->phone = $request->phone_number;
            $vendor->vendor_address = $request->address;
            $vendor->package_price = $request->package_price;
            $vendor->yrs_exp = $request->yrs_exp;
            $vendor->event_completed = $request->event_completed;
            $vendor->meta_title = $request->meta_title;
            $vendor->meta_description = $request->meta_description;
            $vendor->meta_keywords = $request->meta_keywords;
            if ($request->vendor_category == 1 || $request->vendor_category == 2 || $request->vendor_category == 3) {
                if ($vendor_id == 0) {
                    if ($request->vendor_category == 1) {
                        $vendor->services = json_encode(['traditional', 'candid', 'pre-wedding', 'cinematographic', 'drone-shoots', 'photobooth', 'live-screening']);
                    } else if ($request->vendor_category == 2) {
                        $vendor->services = json_encode(['airbrush-makeup', 'party-makeup', 'hd-makeup', 'bridal-makeup', 'engagement-makeup', 'outstation-makeup', 'haldimakeup-mehndi-cocktail-roka']);
                    }
                    $vendor->occasions = json_encode([
                        'roka', 'sagan', 'engagement', 'haldi-mehndi', 'cocktail', 'wedding', 'reception', 'anniversary', 'mata-ki-chowki',
                        'birthday', 'corporate-event', 'baby-shower'
                    ]);
                } else {
                    $vendor->services = json_encode($request->services);
                    $vendor->occasions = json_encode($request->occasions);
                }
            } else {
                $vendor->services = null;
                $vendor->occasions = null;
            }
            $vendor->summary = $request->summary;
            $vendor->place_rating = $request->place_rating;
            $vendor->albums_price = $request->albums_price;
            $vendor->pre_wedding_photoshoot_price = $request->pre_wedding_photoshoot_price;
            $vendor->traditional_video_price = $request->traditional_video_price;
            $vendor->traditional_photography_price = $request->traditional_photography_price;
            $vendor->candid_photography_price = $request->candid_photography_price;
            $vendor->cinematography_price = $request->cinematography_price;
            $vendor->party_makeup_price = $request->party_makeup_price;
            $vendor->engagement_makeup_price = $request->engagement_makeup_price;
            $vendor->hd_bridal_makeup_price = $request->hd_bridal_makeup_price;
            $vendor->air_brush_makeup_price = $request->air_brush_makeup_price;
            $vendor->engagement_mehndi_price = $request->engagement_mehndi_price;
            $vendor->bridal_mehndi_price = $request->bridal_mehndi_price;
            $vendor->similar_vendor_ids = $request->similar_vendors ? implode(",", $request->similar_vendors) : null;
            $vendor->package_option = is_array($request->package_option) ? implode(",", $request->package_option) : null;
            $vendor->save();
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

            $migrated_data = $vendor->makeHidden('id', 'slug', 'phone', 'summary', 'similar_vendor_ids', 'popular', 'status', 'deleted_at', 'meta_title', 'meta_description', 'created_at', 'updated_at')->toArray();
            $vendor['vendor_id'] = $request->vendor;
            VendorUserContent::where('vendor_id', $vendor->id)->update($migrated_data);

            if ($request->business_user_id > 0) {
                $vendor_user = BusinessUser::find($request->business_user_id);
                $vendor_user->content_status = false;
                $vendor_user->save();
                $msg = "Business user's content updated!";
            }
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $th->getMessage()]);
        }
        return redirect()->route('vendor.list');
    }
    public function update_status($vendor_id, $status)
    {
        try {
            $vendor = Vendor::find($vendor_id);
            $vendor->status = $status;
            $vendor->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Vendor status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
    public function update_popular_status($vendor_id, $status)
    {
        try {
            $vendor = Vendor::find($vendor_id);
            $vendor->popular = $status;
            $vendor->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'vendor poular status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
    public function update_wb_assured_status($vendor_id, $status)
    {
        try {
            $vendor = Vendor::find($vendor_id);
            $vendor->wb_assured = $status;
            $vendor->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'WB Assured status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
    public function update_phone_number(Request $request, $vendor_id)
    {
        $validate = Validator::make($request->all(), [
            'phone_number' => 'required|min_digits:10|max_digits:11',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
        } else {
            $vendor = Vendor::find($vendor_id);
            $vendor->phone = $request->phone_number;
            $vendor->save();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Phone Number updated.']);
        }
        return redirect()->back();
    }

    //for seo methods
    public function fetch_meta($vendor_id)
    {
        try {
            $meta = Vendor::select('meta_title', 'meta_description')->where('id', $vendor_id)->first();
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
    public function update_meta(Request $request, $vendor_id)
    {
        $validate = Validator::make($request->all(), [
            'meta_title' => 'required|string|max:255',
            'meta_title' => 'required|string'
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $vendor = Vendor::find($vendor_id);
        if (!$vendor) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $vendor->meta_title = $request->meta_title;
        $vendor->meta_description = $request->meta_description;
        $vendor->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meta updated.']);
        return redirect()->back();
    }


    public function get_similar_vendors(int $category_id, int $city_id)
    {
        try {
            $vendors = Vendor::select('vendors.id', 'cities.name as city', 'vendors.brand_name', 'vendor_categories.name as vendor_category')
                ->join('vendor_categories', 'vendor_categories.id', 'vendors.vendor_category_id')
                ->join('cities', 'cities.id', 'vendors.city_id')
                ->where(['vendor_category_id' => $category_id, 'city_id' => $city_id])->orderBy('brand_name', 'asc')->get();
            return response()->json(['success' => true, 'vendors' => $vendors]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }

    public function manage_images(int $vendor_id)
    {
        $data = Vendor::select('id', 'brand_name as name', 'images')->where('id', $vendor_id)->first();
        $view_used_for = "vendor";
        $page_heading = "Vendor Images";
        return view('common.manage_images', compact('data', 'view_used_for', 'page_heading'));
    }

    public function images_manage_process(Request $request, int $vendor_id)
    {
        try {
            if ($request->user_id > 0) {
                $business_user = BusinessUser::find($request->user_id);
                $vendor = $business_user->getVendorContent;
            } else {
                $vendor = Vendor::find($vendor_id);
            }
            $vendor_images_arr = $vendor->images ? explode(",", $vendor->images) : [];
            if (is_array($request->gallery_images)) {
                foreach ($request->gallery_images as $key => $image) {
                    if (is_file($image)) {
                        $ext = $image->getClientOriginalExtension();

                        $sub_str =  substr($vendor->name, 0, 5);
                        $file_name = "vendor_" . strtolower(str_replace(' ', '_', $sub_str)) . "_" . time() + $key . "." . $ext;
                        $path = "uploads/$file_name";
                        Storage::put("public/" . $path, file_get_contents($image));
                        array_push($vendor_images_arr, $file_name);
                    }
                }
            }

            $model = Vendor::find($vendor_id);
            $model->images = implode(",", $vendor_images_arr);
            $model->save();

            VendorUserContent::where('vendor_id', $vendor_id)->update(['images' => implode(",", $vendor_images_arr)]);

            if ($request->user_id > 0) {
                $business_user->images_status = false;
                $business_user->save();
            }

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Images uploaded successfully.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
        return redirect()->back();
    }

    public function image_delete(Request $request, $vendor_id)
    {
        try {
            $vendor = Vendor::find($vendor_id);
            $images_arr = explode(",", $vendor->images);
            $request_image_index = array_search($request->image_name, $images_arr);
            if ($request_image_index !== false) {
                unset($images_arr[$request_image_index]);
            }
            $vendor->images = sizeof($images_arr) > 0 ? implode(",", $images_arr) : null;
            $vendor->save();

            VendorUserContent::where('vendor_id', $vendor_id)->update(['images' => sizeof($images_arr) > 0 ? implode(",", $images_arr) : null]);

            if (Storage::exists("public/uploads/$request->image_name")) {
                $path = Storage::path("public/uploads/$request->image_name");
                unlink($path);
            }
            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Image removed successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'errors' => $th->getMessage()]);
        }
    }

    public function update_images_sorting(Request $request, $vendor_id)
    {
        try {
            $vendor = Vendor::find($vendor_id);
            $vendor->images = implode(",", $request->images);
            $vendor->save();

            VendorUserContent::where('vendor_id', $vendor_id)->update(['images' => implode(",", $request->images)]);

            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Images sorted successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update_redirect($vendor_id, $value)
    {
        $vendor = Vendor::find($vendor_id);
        $vendor->is_redirect = $value;
        $vendor->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Redirect Updated successfully.']);
        return redirect()->back();
    }
}
