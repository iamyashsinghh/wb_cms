<?php

namespace App\Http\Controllers;

use App\Models\WebAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WebAnalyticsController extends Controller {
    public function ajax_list(Request $request) {
        $data = WebAnalytics::select(
            'web_analytics.id',
            'web_analytics.created_at',
            'web_analytics.url',
            'venues.name as venue_name',
            'web_analytics.type',
            'web_analytics.request_handle_by',
            'web_analytics.click_count',
        )->join('venues', 'web_analytics.venue_id', 'venues.id')->orderBy('id', 'desc');

        if ($request->from != null) {
            $from = Carbon::make($request->from);
            if ($request->to != null) {
                $to = Carbon::make($request->to)->endOfDay();
            } else {
                $to = Carbon::make($request->from)->endOfDay();
            }
            $data->whereBetween('web_analytics.created_at', [$from, $to]);
        }

        return datatables($data)->make(false);
    }
}
