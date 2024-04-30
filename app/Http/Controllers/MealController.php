<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MealController extends Controller {
    public function list() {
        $meals = Meal::select(
            '.id',
            'name',
            'category_id',
        )->orderBy('id', 'desc')->get();
        return view('meal.list', compact('meals'));
    }

    //ajax functions:
    public function edit_ajax($meal_id) {
        try {
            $meal = Meal::find($meal_id);
            return response()->json(['success' => true, 'meal' => $meal]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function manage_process(Request $request, $meal_id = 0) {
        $validate = Validator::make($request->all(), [
            'category' => 'required|int|min:1|max:2',
            'meal_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($meal_id > 0) {
            $msg = "Meal updated.";
            $meal = Meal::find($meal_id);
        } else {
            $msg = "Meal added.";
            $meal = new Meal();
        }

        $meal->category_id = $request->category;
        $meal->name = $request->meal_name;
        $meal->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }




    public function delete($meal_id) {
        $meal = Meal::find($meal_id);
        $meal->delete();

        session()->flash('status', ['success' => false, 'alert_type' => 'success', 'message' => 'Meal deleted.']);
        return redirect()->back();
    }
}
