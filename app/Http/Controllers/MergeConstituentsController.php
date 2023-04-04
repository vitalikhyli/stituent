<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;

class MergeConstituentsController extends Controller
{
    public function index()
    {
    	$one = null;
    	if (request('one')) {
    		$one = Person::find(request('one'));
    	}
        return view('office.merge-constituents.index', compact('one'));
    }
}
