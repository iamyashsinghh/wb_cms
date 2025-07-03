<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use App\Models\VenueCategory;
use App\Models\VenueListingMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VenueListingMetaController extends Controller
{
    public function ajax_list()
    {
        $data = VenueListingMeta::select(
            'venue_listing_metas.id',
            'venue_listing_metas.slug',
            'venue_categories.name as category',
            'cities.name as city',
            'locations.name as locality',
            'venue_listing_metas.status',
            'venue_listing_metas.id as action',
        )->join('venue_categories', 'venue_categories.id', 'venue_listing_metas.category_id')
            ->join('cities', 'cities.id', 'venue_listing_metas.city_id')
            ->leftJoin('locations', 'locations.id', 'venue_listing_metas.location_id');
        return datatables($data)->make(false);
    }

    public function manage($meta_id = 0)
    {
        $categories = VenueCategory::select('id', 'name')->orderBy('name', 'asc')->get();
        $cities = City::select('id', 'name')->orderBy('name', 'asc')->get();
        if ($meta_id > 0) {
            $page_heading = "Edit Meta";
            $meta = VenueListingMeta::find($meta_id);
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
                'header_script' => '',
                'caption' => ''
            ]));
            $locations = [];
        }
        return view('venue.manage_listing_meta', compact('meta', 'page_heading', 'categories', 'cities', 'locations'));
    }

    public function manage_process(Request $request, $meta_id = 0)
    {
        $validate = Validator::make($request->all(), [
            'category' => 'required|int|exists:venue_categories,id',
            'city' => 'required|int|exists:cities,id',
            'meta_title' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        try {
            $category = VenueCategory::find($request->category);
            $city = City::find($request->city);
            $location = Location::find($request->location);
            if ($location) {
                $slug = "$category->slug/$city->slug/$location->slug";
            } else {
                $slug = "$category->slug/$city->slug/all";
            }

            if ($meta_id > 0) {
                $meta = VenueListingMeta::find($meta_id);
                $msg = "Meta updated successfully.";
            } else {
                $meta = new VenueListingMeta();
                $msg = "Meta added successfully.";
            }
            $meta->category_id = $request->category;
            $meta->city_id = $request->city;
            $meta->location_id = $request->location;
            $meta->slug = $slug;
            $meta->meta_title = $request->meta_title;
            $meta->meta_description = $request->meta_description;
            $meta->meta_keywords = $request->meta_keywords;
            $meta->header_script = $request->header_script;
            $meta->caption = $request->footer_caption;
            $meta->save();

            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $th->getMessage()]);
        }
        return redirect()->back();
    }

    public function update_status($meta_id, $status)
    {
        try {
            $meta = VenueListingMeta::find($meta_id);
            $meta->status = $status;
            $meta->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }

    public function meta_delete($meta_id)
    {
        $meta = VenueListingMeta::find($meta_id);
        $meta->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated.']);
        return redirect()->back();
    }

    //for faq methods
    public function fetch_faq($meta_id)
    {
        try {
            $faq = VenueListingMeta::select('faq')->where('id', $meta_id)->first();
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
    public function update_faq(Request $request, $meta_id)
    {
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

        $model = VenueListingMeta::find($meta_id);
        if (!$model) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Something went wrong.']);
            return redirect()->back();
        }

        $model->faq = $faq_arr;
        $model->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'FAQ updated.']);
        return redirect()->back();
    }

    public function saveDraft(Request $request, $meta_id)
    {
        $meta = \App\Models\VenueListingMeta::findOrFail($meta_id);
        $meta->draft_data = $request->draft_data ?? '';
        $meta->save();
        return response()->json(['success' => true]);
    }
}
