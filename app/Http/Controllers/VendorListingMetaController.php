<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use App\Models\VendorCategory;
use App\Models\VendorListingMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorListingMetaController extends Controller {
    public function list() {
        $categories = VendorCategory::select('id', 'name')->orderBy('name', 'asc')->get();
        $cities = City::select('id', 'name')->orderBy('name', 'asc')->get();
        return view('vendor.listing_meta_list', compact('categories', 'cities'));
    }

    public function ajax_list() {
        $data = VendorListingMeta::select(
            'vendor_listing_metas.id',
            'vendor_listing_metas.slug',
            'vendor_categories.name as category',
            'cities.name as city',
            'locations.name as locality',
            'vendor_listing_metas.status',
            'vendor_listing_metas.id as action',
        )->join('vendor_categories', 'vendor_categories.id', 'vendor_listing_metas.category_id')
            ->join('cities', 'cities.id', 'vendor_listing_metas.city_id')
            ->leftJoin('locations', 'locations.id', 'vendor_listing_metas.location_id');
        return datatables($data)->make(false);
    }

    public function manage($meta_id = 0) {
        $categories = VendorCategory::select('id', 'name')->orderBy('name', 'asc')->get();
        $cities = City::select('id', 'name')->orderBy('name', 'asc')->get();
        if ($meta_id > 0) {
            $page_heading = "Edit Meta";
            $meta = VendorListingMeta::find($meta_id);
            $locations = Location::select('id', 'name')->where('city_id', $meta->city_id)->orderBy('name', 'asc')->get();
        } else {
            $page_heading = "Add Meta";
            $meta = json_decode(json_encode([
                'id' => '',
                'category_id' => '',
                'city_id' => '',
                'location_id' => '',
                'slug' => '',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'caption' => ''
            ]));
            $locations = [];
        }
        return view('vendor.manage_listing_meta', compact('meta', 'page_heading', 'categories', 'cities', 'locations'));
    }

    public function manage_process(Request $request, $meta_id = 0) {
        $validate = Validator::make($request->all(), [
            'category' => 'required|int|exists:vendor_categories,id',
            'city' => 'required|int|exists:cities,id',
            'meta_title' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        try {
            $category = VendorCategory::find($request->category);
            $city = City::find($request->city);
            $location = Location::find($request->location);
            if ($location) {
                $slug = "$category->slug/$city->slug/$location->slug";
            } else {
                $slug = "$category->slug/$city->slug/all";
            }

            if ($meta_id > 0) {
                $meta = VendorListingMeta::find($meta_id);
                $msg = "Meta updated successfully.";
            } else {
                $meta = new VendorListingMeta();
                $msg = "Meta added successfully.";
            }
            $meta->category_id = $request->category;
            $meta->city_id = $request->city;
            $meta->location_id = $request->location;
            $meta->slug = $slug;
            $meta->caption = $request->caption;
            $meta->meta_title = $request->meta_title;
            $meta->meta_keywords = $request->meta_keywords;
            $meta->meta_description = $request->meta_description;
            $meta->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'danger', 'message' => $th->getMessage()]);
        }
        return redirect()->back();
    }

    public function update_status($meta_id, $status) {
        try {
            $meta = VendorListingMeta::find($meta_id);
            $meta->status = $status;
            $meta->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }

    public function meta_delete($meta_id) {
        $meta = VendorListingMeta::find($meta_id);
        $meta->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated.']);
        return redirect()->back();
    }

    //for faq methods
    public function fetch_faq($meta_id) {
        try {
            $faq = VendorListingMeta::select('faq')->where('id', $meta_id)->first();
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
    public function update_faq(Request $request, $meta_id) {
        $validate = Validator::make($request->all(), [
            'faq_question' => 'required',
            'faq_answer' => 'required'
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

        $model = VendorListingMeta::find($meta_id);
        if (!$model) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $model->faq = $faq_arr;
        $model->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'FAQ updated.']);
        return redirect()->back();
    }
}
