<?php

namespace App\Http\Controllers;

use App\Constituent;
use App\TenantModels\DataUpdate;
use Auth;
use Illuminate\Http\Request;

class DataUpdatesController extends Controller
{
    public function show(Request $request)
    {
        $du = request()->user()->team->dataUpdates()->latest()->first();

        return view('data-updates.show', compact('du'));
    }

    public function json()
    {
        $du = request()->user()->team->dataUpdates()->latest()->first();

        return $du;
    }

    public function test()
    {
        return Constituent::all();
    }
}
