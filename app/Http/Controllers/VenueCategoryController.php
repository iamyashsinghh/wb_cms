<?php

namespace App\Http\Controllers;

use App\Models\VenueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VenueCategoryController extends Controller {
    public function list() {
        $categories = VenueCategory::orderBy('id', 'desc')->get();
        return view('venue_category.list', compact('categories'));
    }

    public function edit_ajax($category_id) {
        try {
            $category = VenueCategory::find($category_id);
            return response()->json(['success' => true, 'category' => $category]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    public function manage_process(Request $request, $category_id = 0) {
        $validate = Validator::make($request->all(), [
            'category_name' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $validate->errors()->first()]);
            return redirect()->back();
        }

        if ($category_id > 0) {
            $msg = "Category updated.";
            $category = VenueCategory::find($category_id);
        } else {
            $msg = "Category added.";
            $category = new VenueCategory();
        }

        $category->name = $request->category_name;
        $category->slug = strtolower(str_replace(" ", "-", $request->category_name));
        $category->save();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        return redirect()->back();
    }

    public function delete($category_id) {
        $category = VenueCategory::find($category_id);
        $category->delete();

        session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => 'Category deleted.']);
        return redirect()->back();
    }
}
