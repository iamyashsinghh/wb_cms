<?php

namespace App\Http\Controllers;

use App\Models\BusinessUser;
use App\Models\Vendor;
use App\Models\VendorUserContent;
use App\Models\Venue;
use App\Models\VenueUserContent;
use App\Models\City;
use App\Models\VendorCategory;
use App\Models\VenueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessUserController extends Controller {
    public function list() {
        $business_users = BusinessUser::all()->makeHidden(['created_at', 'updated_at', 'remember_token', 'otp_code', 'deleted_at']);
        $cities = City::select('id', 'name')->get();
        return view('business_user.list', compact('business_users', 'cities'));
    }

    public function user_ajax_list() {
        $users = BusinessUser::select(
            'business_users.id',
            'business_users.name',
            'business_users.business_name',
            'business_users.phone',
            'business_users.email',
            'cities.name as city',
            'business_users.address',
            'business_users.user_status',
            'business_users.content_status',
            'business_users.images_status',
            'business_users.migrated_business_id',
            'business_users.id as action',
        )->join('vendor_categories as vc', 'vc.id', 'business_users.business_category_id')
            ->join('cities', 'cities.id', 'business_users.city_id');
        return datatables($users)->make(false);
    }

    public function user_migrate(Request $request) {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|int|exists:business_users,id',
            'listed_business' => 'required|int',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $user = BusinessUser::find($request->user_id);
        if ($user->business_type == 1) {
            $venue = Venue::select(
                'city_id',
                'location_id',
                'name',
                'venue_address',
                'min_capacity',
                'max_capacity',
                'veg_price',
                'nonveg_price',
                'budget_id',
                'venue_category_ids',
                'start_time_morning',
                'end_time_morning',
                'start_time_evening',
                'end_time_evening',
                'area_capacity',
                'images'
            )->find($request->listed_business)->toArray();
            $venue['venue_id'] = $request->listed_business;
            $venue['created_at'] = date('Y-m-d H:i:s');
            $venue['updated_at'] = date('Y-m-d H:i:s');

            VenueUserContent::insert($venue);
        } else {
            $vendor = Vendor::find($request->listed_business)->makeHidden('id', 'slug', 'phone', 'summary', 'similar_vendor_ids', 'popular', 'status', 'deleted_at', 'meta_title', 'meta_description', 'meta_keywords', 'wb_assured', 'created_at', 'updated_at')->toArray();
            $vendor['vendor_id'] = $request->listed_business;
            $vendor['created_at'] = date('Y-m-d H:i:s');
            $vendor['updated_at'] = date('Y-m-d H:i:s');
            VendorUserContent::insert($vendor);
        }

        $user->migrated_business_id = $request->listed_business;
        $user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'User migrated successfully.']);
        return redirect()->back();
        // $last_insert_id = VendorUserContent::insertGetId($vendor->replicate()->toArray()); //we can use this sytax for later
    }

    public function update_user_status($user_id, $status) {
        $vendor_user = BusinessUser::find($user_id);
        $vendor_user->user_status = $status;
        $vendor_user->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'User status updated.']);
        return redirect()->back();
    }
    public function user_delete($user_id) {
        $vendor_user = BusinessUser::find($user_id);
        $vendor_user->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'User deleted.']);
        return redirect()->back();
    }

    public function user_edit($user_id) {
        try {
            $user = BusinessUser::find($user_id);
            if ($user->business_type == 1) {
                $categories = VenueCategory::select('id', 'name')->orderBy('name', 'asc')->get();
            } else {
                $categories = VendorCategory::select('id', 'name')->orderBy('name', 'asc')->get();
            }
            $res = response()->json(['success' => true, 'user' => $user, 'categories' => $categories]);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'message' => 'Someting went wrong.']);
        }
        return $res;
    }

    public function user_edit_process(Request $request) {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required|int|exists:business_users,id',
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'business_category' => 'required|int',
            'phone_number' => 'required|int|min_digits:10|max_digits:10',
            'email' => 'required|email',
            'city' => 'required|exists:cities,id',
            'address' => 'required|string|max:255'
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        $user = BusinessUser::find($request->user_id);
        $user->city_id = $request->city;
        $user->business_category_id = $request->business_category;
        $user->name = $request->name;
        $user->business_name = $request->business_name;
        $user->phone = $request->phone_number;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Business user updated.']);
        return redirect()->back();
    }

    public function update_images_status(int $user_id, int $status) {
        $vendor_user = BusinessUser::find($user_id);
        $vendor_user->images_status = $status;
        $vendor_user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Images status updated.']);
        return redirect()->back();
    }
    public function update_content_status(int $user_id, int $status) {
        $vendor_user = BusinessUser::find($user_id);
        $vendor_user->content_status = $status;
        $vendor_user->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Content status updated.']);
        return redirect()->back();
    }

    public function fetch_listed_vendors(Request $request) {

        $model = Vendor::select('id', 'brand_name as text');
        if ($request->term != null) {
            $model = $model->where('brand_name', 'like', "%$request->term%");
        }
        $model = $model->orderBy('brand_name', 'asc')->get();
        return response()->json(['results' => $model]);
    }

    public function fetch_listed_venues(Request $request) {

        $model = Venue::select('id', 'name as text');
        if ($request->term != null) {
            $model = $model->where('name', 'like', "%$request->term%");
        }
        $model = $model->orderBy('name', 'asc')->get();
        return response()->json(['results' => $model]);
    }
}
