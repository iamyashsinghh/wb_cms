<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\PageListingMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PageController extends Controller
{
    public function ajax_list() {
        $data = PageListingMeta::select(
            'page_listing_metas.id',
            'page_listing_metas.slug',
            'cities.name as city',
            'page_listing_metas.status',
            'page_listing_metas.id as action',
        )->join('cities', 'cities.id', 'page_listing_metas.city_id');
        $data = $data->get();
        return datatables($data)->make(false);
    }

    public function manage($meta_id = 0) {
        $cities = City::select('id', 'name')->orderBy('name', 'asc')->get();
        if ($meta_id > 0) {
            $page_heading = "Edit Meta";
            $meta = PageListingMeta::find($meta_id);
            $parts = explode('/', $meta->slug);
            if (count($parts) > 1) {
                $meta->type = $parts[1];
            }
        } else {
            $page_heading = "Add Meta";
            $meta = json_decode(json_encode([
                'id' => '',
                'city_id' => '',
                'slug' => '',
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'caption' => '',
                'type' => '',
            ]));
        }
        return view('page_listing_meta.manage_listing_meta', compact('meta', 'page_heading', 'cities'));
    }

    public function manage_process(Request $request, $meta_id = 0) {
        $validate = Validator::make($request->all(), [
            'city' => 'required|int|exists:cities,id',
            'meta_title' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }
        try {
            $city = City::find($request->city);

                $slug = "$city->slug/$request->type";


            if ($meta_id > 0) {
                $meta = PageListingMeta::find($meta_id);
                $msg = "Meta updated successfully.";
            } else {
                $meta = new PageListingMeta();
                $msg = "Meta added successfully.";
            }
            $meta->city_id = $request->city;
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
            $meta = PageListingMeta::find($meta_id);
            $meta->status = $status;
            $meta->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }

    public function meta_delete($meta_id) {
        $meta = PageListingMeta::find($meta_id);
        $meta->delete();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Meta status updated.']);
        return redirect()->back();
    }

    // for faq methods
    public function fetch_faq($meta_id) {
        try {
            $faq = PageListingMeta::select('faq')->where('id', $meta_id)->first();
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

        $model = PageListingMeta::find($meta_id);
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
