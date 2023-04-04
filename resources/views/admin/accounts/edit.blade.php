@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

@endsection

@section('main')


<div class="w-full pb-2 ">

    <div class="text-xl border-b-4 border-red py-2">
        Edit Account
    </div>  


<form method="POST" id="contact_form" action="/admin/accounts/{{ $theaccount->id }}/update">
    
    @csrf




<table id="accountTable" class="w-full border-t">

    <tr class="border-b">
    
        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Account Name
        </td>
        <td class="p-1">
            <input name="name" placeholder="Account Name" value="{{ $theaccount->name }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
        </td>

    </tr>

    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Contact Name
        </td>
        <td class="p-1">
            <input name="contact_name" placeholder="Contact Name" value="{{ $theaccount->contact_name }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
        </td>

    </tr>

    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Email / Phone
        </td>
        <td class="p-1">
            <input name="email" placeholder="Email" value="{{ $theaccount->email }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
            <input name="phone" placeholder="Phone" value="{{ $theaccount->phone }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
        </td>

    </tr>
    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Address
        </td>
        <td class="p-1">
            <input name="address" placeholder="Address" value="{{ $theaccount->address }}" class="border-2 rounded-lg px-4 py-2 w-1/2 mb-2"/><br />
            <input name="city" placeholder="City" value="{{ $theaccount->city }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
            <input name="state" placeholder="State" value="{{ $theaccount->state }}" class="border-2 rounded-lg px-4 py-2 w-1/6"/>
            <input name="zip" placeholder="Zip" value="{{ $theaccount->zip }}" class="border-2 rounded-lg px-4 py-2 w-1/5"/>
        </td>

    </tr>


</table>


    <div class="text-xl border-b-4 border-red py-2 mt-2">
        Teams
    </div>  


<table class="w-full border-t">

    <tr class="border-b">
        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            New Team Name:
        </td>
        <td class="p-1">
            <input name="team_new_name" placeholder="World's Best Team" class="border-2 rounded-lg px-4 py-2 w-full"/>
        </td>
        <td class="p-1">

            <select name="team_new_state">
                
                <option value="MA">MA</option>
                <option value="RI">RI</option>
                
            </select>
            <select name="team_existing_app_type">
                @foreach(App\Team::all()->pluck('app_type')->unique()->toArray() as $app_type)
                    <option value="{{ $app_type }}">{{ $app_type }}</option>
                @endforeach
            </select>

            <input type="text" name="team_new_app_type" class="border p-2" placeholder="Or create new app type" />

        </td>
        <td class="p-1">
            <input name="team_new_slice" placeholder="Slice e.g. x_MA_S_1314" class="border-2 rounded-lg px-4 py-2 w-full"/>
        </td>
    </tr>

</table>


    @if($theaccount->teams->first())
    <table class="w-full border-t mt-2">
        @foreach($theaccount->teams as $team)
            <tr class="border-b">
                <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
                    <a href="/admin/accounts/{{ $theaccount->id }}/teams/{{ $team->id }}/edit">
                        <button type="button" class="rounded-lg bg-blue text-white px-2 py-1 text-sm mr-2">Edit</button>
                    </a>
                </td>
                <td class="p-1">
                    {{ $team->name }}
                </td>
                <td class="p-1">
                    {{ $team->data_folder_id }} | {{ $team->app_type }}
                </td>
                <td class="p-1">
                    {{ $team->db_slice }}
                </td>
            </tr>
        @endforeach
    </table>
    @else
        <div class="p-2 text-center">
          No Teams Yet
        </div>
    @endif



    <div class="text-xl border-b-4 border-red py-2 mt-2">
        Users
    </div>  


@if(!$theaccount->teams->first())

    <div class="p-2 text-center bg-blue-lightest">
        Create a team first in order to create users
    </div>

    

@else
<table class="w-full border-t">

    <tr class="border-b">
        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            New User
        </td>
        <td class="p-1">
            <div class="text-sm m-2">
                <input name="user_new_name" placeholder="Name" class="border-2 rounded-lg px-4 py-2 w-2/5" />
                <input name="user_new_email" placeholder="Email" class="border-2 rounded-lg px-4 py-2 w-2/5" />
            </div>

            <div class="text-sm m-2">
                Starting team:
                <select name="user_new_team">
                    @foreach($theaccount->teams as $teamoption)
                        <option value="{{ $teamoption->id }}">({{ $teamoption->app_type }}) {{ $teamoption->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-sm m-2">

                    <label class="font-normal" for="developer">
                            <input type="checkbox" name="permissions_developer" id="developer" value="1" class="m-2" /> <span class="">Developer</span>
                            </label>
                    |
                    <label class="font-normal" for="admin">
                        <input type="checkbox" name="permissions_admin" id="admin" value="1" class="m-2" /> <span class="">Admin</span>
                        </label>
                    |
                    <label class="font-normal" for="chat">
                        <input type="checkbox" name="permissions_chat" id="chat" value="1" checked class="m-2" /> <span class="">Chat</span>
                        </label>
                    |
                    <label class="font-normal" for="reports">
                        <input type="checkbox" name="permissions_reports" id="reports" value="1" checked class="m-2" /> <span class="">Reports</span>
                        </label>
                    |
                    <label class="font-normal" fir="metrics">
                        <input type="checkbox" name="permissions_metrics" id="metrics" value="1" checked class="m-2" /> <span class="">Metrics</span>
                        </label>
                    |
                    <label class="font-normal" for="constituents" >
                        <input type="checkbox" name="permissions_constituents" id="constituents" value="1" checked class="m-2" /> <span class="">Constituents</span>
                        </label>
                    |
                    <label class="font-normal" for="creategroups" >
                        <input type="checkbox" name="permissions_creategroups" id="creategroups" value="1" checked class="m-2" /> <span class=""> Groups</span>
                        </label>
            </div>



        </td>
    </tr>

</table>
@endif

    @if($theaccount->users()->first())
    <table class="w-full border-t mt-2 text-sm">
        @foreach($theaccount->users()->each(function($item) {
            if (!$item->permissions) {
                $item['is_guest'] = false;
            } else {
                $item['is_guest'] =  $item->permissions->guest;
            }
        })->sortBy('is_guest') as $user)

            <tr class="border-b {{ ($user->is_guest) ? 'bg-red-lightest' : '' }}">
                <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
                    <a href="/admin/accounts/{{ $theaccount->id }}/users/{{ $user->id }}/edit">
                        <button type="button" class="rounded-lg bg-blue text-white px-2 py-1 text-sm mr-2">Edit</button>
                    </a>
                </td>
                <td class="p-1">
                    <div class="font-bold">
                        @if($user->is_guest)
                            <span class="text-red">
                                {{ $user->name }} <i class="fas fa-hands-helping"></i> <span class="font-normal">Volunteer</span>
                            </span>
                        @else
                            <span class="text-blue">
                                {{ $user->name }}
                            </span>
                        @endif
                    </div>
                    <div>{{ $user->email }}</td>
           

                        @if($user->login_token)
                            <div>
                                Login Link:
                         
                                <span class="text-blue">
                                    <span class="text-grey-dark">{{ config('app.url') }}/link/</span>{{ $user->login_token }}
                                </span>

                                <span class="hidden float-right">
                                    <div id="email_link_to_user" class="bg-grey-lighter rounded-lg px-2 py-1 border text-sm cursor-pointer"><i class="fas fa-envelope"></i> Email link to {{ substr($user->email,0,strpos($user->email,'@')+1) }}&hellip;</div>
                                </span>
                            </div>
                        @endif

                </td>
                <td class="p-1">

                    @if(!$user->teams->contains($user->team))
                        <div class="px-4 py-2 border-2 border-red bg-red-lightest mr-4">
                            <i class="fas fa-exclamation-triangle"></i> Current team is {{ $user->team->name }}
                        </div>
                    @endif

                    <ul>
                        @foreach($user->teams as $userteam)
                            @if($user->current_team_id == $userteam->id)
                                <li>
                       
                                    <span class="bg-blue text-white px-1 mr-2">Current</span>{{ $userteam->name }}
                                </li>
                            @else
                                <li>{{ $userteam->name }}</li>
                            @endif
                        @endforeach
                    </ul>
                </td>
                <td class="p-1">
                    <a href="/admin/mock/{{ $user->id }}">
                        <button class="rounded-lg bg-blue text-white px-2 py-1 w-full hover:bg-blue-darker" type="button">
                            Login as {{ $user->shortName }}
                        </button>
                    </a>
                </td>

            </tr>



        @endforeach
    </table>
    @else
        <div class="p-2 text-center">
          No Users Yet
        </div>
    @endif




<div class="mt-4 flex float-right">
    <input type="submit" name="save" value="Update" class="flex-1 flex-initial mr-2 rounded-lg bg-grey-darker hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />

    <input type="submit" name="save_and_close" formaction="/admin/accounts/{{ $theaccount->id }}/update/close" value="Save & Close" class="flex-1 flex-initial rounded-lg bg-blue hover:bg-blue-dark text-white text-sm px-8 py-2 mt-1 shadow" />
</div>


</form>

</div>


</div>

<br />
<br />
@endsection