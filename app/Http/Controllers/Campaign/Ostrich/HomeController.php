<?php

namespace App\Http\Controllers\Campaign\Ostrich;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
    	return view('campaign.ostrich.dashboard');
    }

    public function walk()
    {
    	return view('campaign.ostrich.walk');
    }

    public function phone()
    {
    	return view('campaign.ostrich.phone');
    }
}
