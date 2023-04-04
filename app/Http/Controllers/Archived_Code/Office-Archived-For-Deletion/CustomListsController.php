<?php
/* NOT SURE THIS CONTROLLER IS NEEDED -- NOT SURE WHAT IT DOES

namespace App\Http\Controllers\Office;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Category;
use Auth;

class CustomListsController extends Controller
{
    public function bulkEmail()
    {
    	$categories = Category::where('team_id', Auth::user()->team->id)
                            ->orderBy('name')
                            ->get();

    	return view('office.bulkemail.custom-list-form-modal', compact('categories'));
    }
}
