<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function main()
    {
        $active_accounts = Account::where('active', true)
                                  ->with('teams.users')
                                  ->orderBy('name')
                                  ->get();

        //dd($active_accounts);
        //dd($active_accounts->count());
        return view('admin.activity.main', compact('active_accounts'));
    }

    public function inactive()
    {
        $inactive_accounts = Account::where('active', false)->orderBy('name')->get();

        return view('admin.activity.inactive', compact('inactive_accounts'));
    }

    public function test()
    {
        $active_accounts = Account::where('active', true)
                                  ->with('teams.users')
                                  ->orderBy('name')
                                  ->get();
        dd($active_accounts->count(), 'Test');
    }
}
