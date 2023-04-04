<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    public function index()
    {
        $presets = Category::select('preset')->groupBy('preset')->get();

        $cats = Category::where('preset', '<>', null)->get();

        $groups = Group::all();

        return view('admin.groups.index', compact('presets', 'cats', 'groups'));
    }
}
