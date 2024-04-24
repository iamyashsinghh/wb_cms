<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Venue;
use App\Models\WebAnalytics;

class DashboardController extends Controller
{
    public function index()
    {
        $total_vendors = Vendor::count();
        $total_venues = Venue::count();
        $totalAnalitics = WebAnalytics::count();

        return view('dashboard', compact('total_vendors', 'total_venues', 'totalAnalitics'));
    }
}
