<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Http\Request;

class MockController extends Controller
{
    public function sandbox(Request $request, $app_type)
    {
        $human = false;
        $answer = strtolower(request('answer'));

        if (base64_decode(request('question')) == 'graduation') {
            if (($answer == 'hat') || ($answer == 'cap')) {
                $human = true;
            }
        }

        if (! $human) {
            return redirect()->back();
        }

        $sandbox_account = \App\Account::where('name', 'like', 'Sandbox%')->first();
        if (! $sandbox_account) {
            return redirect()->back();
        }

        $team = \App\Team::where('account_id', $sandbox_account->id)
                           ->where('name', 'like', 'SANDBOX:%')
                           ->where('app_type', $app_type)
                           ->first();

        if (env('APP_ENV') == 'production') {
            return redirect()->back();
        }    // Not in Live for now

        if (! $team) {
            return redirect()->back();
        }

        // Create several users maybe in case of simultaneous use by potential clients

        $user = $team->usersall->sortBy('last_login')->first();
        $user->current_team_id = $team->id;
        $user->last_login = now();   // Less likely potential clients will get same user
        $user->save();

        Auth::loginUsingId($user->id);

        return redirect('/'.Auth::user()->team->app_type);
    }

    public function loginAs($id)
    {
        session()->flush();
        $original_id = Auth::user()->id;
        if (is_numeric($id)) {
            Auth::loginUsingId($id);
            session(['mocking' => $original_id]);
        } else {
            $user = User::where('username', $id)->first();
            if ($user) {
                Auth::loginUsingId($user->id);
                session(['mocking' => $original_id]);
            }
        }

        return redirect('/home');
    }

    public function restore()
    {
        $account_id = Auth::user()->team->account_id;
        $id = session('mocking');
        session()->flush();
        Auth::loginUsingId($id);

        return redirect('/admin/accounts/'.$account_id.'/edit');
    }
}
