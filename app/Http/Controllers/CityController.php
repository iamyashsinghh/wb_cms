<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller {

    public function list() {
        $cities = City::orderBy('id', 'asc')->get();
        return view('city.list', compact('cities'));
    }

    public function manage_process(Request $request, $city_id = 0) {
        $validate = Validator::make($request->all(), [
            'city_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($city_id > 0) {
            $city = City::find($city_id);
            $msg = "City added.";
        } else {
            $city = new City();
            $msg = "City updated.";
        }
        $city->name = $request->city_name;
        $city->slug = strtolower(str_replace(" ", "-", $request->city_name));
        $city->save();
        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function delete($city_id) {
        try {
            $city = City::find($city_id);
            $city->delete();
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'City deleted.']);
        } catch (\Throwable $th) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Soomething went wrong.']);
        }

        return redirect()->back();
    }

    // public function db_refactor_process() {

    //     $old_data = DB::connection('mysql2')->table('budget')->get();
    //     foreach ($old_data as $list) {
    //         $model = new Budget();
    //         $model->id = $list->id;
    //         $model->name = $list->name;
    //         $model->save();
    //     }
    //     return "Success";
    //     // return datatables($cities)->toJson();
    // }
}
