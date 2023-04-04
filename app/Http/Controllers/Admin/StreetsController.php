<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Street;
use App\VoterMaster;

class StreetsController extends Controller
{
    public function index()
    {
    	$streets_count = Street::count();
    	$missing_count = Street::whereNull('lat_min')->count();
    	$voters_count  = VoterMaster::whereNull('archived_at')->count();
    	$outlier_count = VoterMaster::whereNull('archived_at')
    							    ->whereNotNull('gis_outlier_at')->count();
    	$estimate_count = VoterMaster::whereNull('archived_at')
    	                            ->whereNotNull('gis_estimated_at')->count();
    	$votermiss_count = VoterMaster::whereNull('archived_at')
    	                            ->whereNull('address_lat')->count();

    	return view('admin.streets.index', compact('streets_count',
    											   'missing_count',
    											   'voters_count',
    											   'outlier_count',
    											   'estimate_count',
    											   'votermiss_count'));
    }
}
