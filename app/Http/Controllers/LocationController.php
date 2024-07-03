<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller {
    public function list() {
        $cities = City::select('id', 'name')->orderBy('name', 'asc')->get();
        return view('location.list', compact('cities'));
    }

    public function ajax_list() {
        $locations = Location::select(
            'locations.id',
            'locations.name',
            'locations.slug',
            'cities.name as city_name',
            'locations.is_group',
        )->join('cities', 'cities.id', 'locations.city_id');
        return datatables($locations)->make(false);
    }

    //ajax functions:
    public function edit_ajax($location_id) {
        try {
            $location = Location::find($location_id);
            return response()->json(['success' => true, 'location' => $location]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function manage_process(Request $request, $location_id = 0) {
        $validate = Validator::make($request->all(), [
            'city' => 'required|exists:cities,id',
            'location_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($location_id > 0) {
            $location = Location::find($location_id);
            $msg = "Location updated.";
        } else {
            $location = new Location();
            $msg = "Location added.";
        }

        $location->city_id = $request->city;
        $location->name = $request->location_name;
        $location->slug = strtolower(str_replace(" ", "-", $request->location_name));
        $location->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    //location group methods
    public function group_manage($group_id = 0) {
        $cities = City::select('id', 'name')->get();
        if ($group_id > 0) {
            $page_heading = "Edit Location group";
            $group = Location::where(['id' => $group_id, 'is_group' => true])->first();
            if (!$group) {
                return abort(404);
            }
            $locations = Location::select('id', 'name')->where(['city_id' => $group->city_id])->get();
        } else {
            $page_heading = "Add Location group";
            $group = json_decode(json_encode([
                'id' => 0,
                'name' => '',
                'city_id' => '',
                'locality_ids' => ''
            ]));
            $locations = json_decode(json_encode([]));
        }

        return view('location.manage_group', compact('page_heading', 'cities', 'group', 'locations'));
    }

    public function group_manage_process(Request $request, $group_id = 0) {
        $validate = Validator::make($request->all(), [
            'city' => 'required|exists:cities,id',
            'group_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($group_id > 0) {
            $msg = 'Group location updated.';
            $group = Location::find($group_id);
        } else {
            $msg = 'Group location added.';
            $group = new Location();
        }

        $group->name = $request->group_name;
        $group->city_id = $request->city;
        $group->slug = strtolower(str_replace(" ", "-", $request->group_name));
        $group->is_group = true;
        if (is_array($request->localities) && sizeof($request->localities) > 0) {
            $group->locality_ids = implode(",", $request->localities);
        }
        $group->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->route('location.list');
    }

    //ajax functions: used in venue manage page
    public function get_locations(int $city_id) {
        try {
            $locations = Location::select('id', 'name', 'is_group', 'locality_ids')->where('city_id', $city_id)->orderBy('name', 'asc')->get();
            return response()->json(['success' => true, 'locations' => $locations]);
        } catch (\Throwable $th) {
            return response()->json(['success' => true, 'alert_type' => 'error', 'message' => 'Something went wrong.', 'error' => $th->getMessage()]);
        }
    }
}
