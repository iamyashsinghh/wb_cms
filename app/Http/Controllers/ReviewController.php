<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function list()
    {
        $venue = Venue::select('id', 'name')->get();
        $vendor = Vendor::select('id', 'brand_name')->get();

        // ✅ NEW CODE — Count pending reviews
        $disabledReviewsCount = Review::where('status', 0)->count();

        // ✅ Pass it to blade view
        return view('review.list', compact('venue', 'vendor', 'disabledReviewsCount'));
    }

    public function pending()
    {
        $pendingReviews = Review::where('status', 0)->get();
        $venues = Venue::select('id', 'name')->get();
        $vendors = Vendor::select('id', 'brand_name')->get();

        return view('review.pending_list', compact('pendingReviews', 'venues', 'vendors'));
    }


    public function ajax_list()
    {
        $reviews = Review::select(
            'id',
            'users_name',
            'rating',
            'product_for', // get venue or vendor detail
            'product_id', // get id of venue or vendor
            'status',
        );
        return datatables($reviews)->make(false);
    }

    public function manage($review_id = 0)
    {
        if ($review_id > 0) {
            $review = Review::find($review_id);
            $page_heading = "Edit Review";
        } else {
            $page_heading = "Add Review";
            $review = json_decode(json_encode([
                'id' => 0,
                'product_id' => '',
                'product_for' => '',
                'users_name' => '',
                'comment' => '',
                'rating' => '',
                'status' => '',
                'is_read' => '',
                'c_number' => '',
            ]));
        }
        return view('review.manage', compact('page_heading', 'review'));
    }
    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }
        if ($review->delete()) {
            $msg = 'Review deleted successfully.';
            session()->flash('status', ['success' => true, 'alert_type' => 'success', 'message' => $msg]);
        } else {
            $msg = 'Unable to delete.';
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => $msg]);
        }
        return redirect()->route('review.list');
    }
    public function getVenues()
    {
        $venues = Venue::select('id', 'name')->get();
        return response()->json($venues);
    }

    public function getVendors()
    {
        $vendors = Vendor::select('id', 'brand_name')->get();
        return response()->json($vendors);
    }
    public function manage_process(Request $request, $review_id = 0)
    {
        $review = ($review_id > 0) ? Review::find($review_id) : new Review();

        if ($review_id > 0) {
            $review->product_id = $review->product_id;
            $review->product_for = $review->product_for;
        } else {
            $review->product_id = $request->product_id;
            $review->product_for = $request->product_for;
        }
        $review->users_name = $request->users_name;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = $request->status;
        $review->c_number = $request->c_number;
        $review->is_read = 1;
        $review->save();

        $totalReviews = Review::where('product_id', $review->product_id)
            ->where('product_for', $review->product_for)
            ->count();

        if ($review->product_for === 'venue') {
            $venue = Venue::find($review->product_id);
            if ($venue) {
                $venue->review_count = $totalReviews;
                $venue->save();
            }
        } elseif ($review->product_for === 'vendor') {
            $vendor = Vendor::find($review->product_id);
            if ($vendor) {
                $vendor->review_count = $totalReviews;
                $vendor->save();
            }
        }

        session()->flash('status', [
            'success' => true,
            'alert_type' => 'success',
            'message' => ($review_id > 0) ? 'Review updated successfully.' : 'Review added successfully.',
        ]);

        return redirect()->route('review.list');
    }

    public function update_review_status($review_id, $status)
    {
        try {
            $review = Review::find($review_id);
            $review->status = $status;
            $review->save();
            $res = response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Status updated!']);
        } catch (\Throwable $th) {
            $res = response()->json(['success' => false, 'alert_type' => 'danger', 'message' => 'Something went wrong!']);
        }
        return $res;
    }
}
