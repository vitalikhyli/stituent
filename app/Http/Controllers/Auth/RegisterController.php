<?php

namespace App\Http\Controllers\Auth;

use App\Account;
use App\DataFolder;
use App\Http\Controllers\Controller;
use App\Jobs\CreateTeamDatabase;
use App\Permission;
use App\Team;
use App\User;
use DB;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'team_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $data_folder_id = 1;
        $data_folder = DataFolder::find(1);
        if (! $data_folder) {
            $data_folder = new DataFolder;
            $data_folder->name = 'Massachusetts';
            $data_folder->save();
        }
        $account = new Account;
        $account->name = $data['team_name'].' Account';
        $account->save();

        $team = new Team;
        $team->account_id = $account->id;
        $team->name = $data['team_name'];
        $team->encryption_key = Str::random(32);
        $team->data_folder_id = $data_folder_id;
        $team->save();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->current_team_id = $team->id;
        $user->save();

        $team->owner_id = $user->id;
        $team->save();

        $permissions = new Permission;
        $permissions->user_id = $user->id;
        $permissions->team_id = $team->id;
        $permissions->save();

        // Job to create database
        //CreateTeamDatabase::dispatch($team);

        return $user;
    }
}
