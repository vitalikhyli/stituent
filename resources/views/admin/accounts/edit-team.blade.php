@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

@endsection

@section('main')


<div class="w-full pb-2 ">

    <div class="text-xl border-b-4 border-red py-2">
        Edit Team
    </div>  


<form method="POST" id="contact_form" action="/admin/accounts/{{ $theaccount->id }}/teams/{{ $team->id }}/update">
    
    @csrf




<table id="accountTable" class="w-full border-t">

    <tr class="border-b">

        <td class="p-2 bg-grey-lighter text-right align-middle w-1/6">
            State
        </td>
        <td class="p-2">

            <select name="state">

                @foreach(App\Account::where('state', '!=', '')
                                ->whereNotNull('state')
                                ->get()
                                ->pluck('state')
                                ->map(function ($item) { return strtoupper($item); })
                                ->unique()
                                ->sortBy('state') as $state)

                    <option {{ ($team->data_folder_id == $state) ? 'selected' : '' }} value="{{ $state }}">
                        {{ $state }}
                    </option>

                @endforeach

            </select>

        </td>

    </tr>

    <tr class="border-b">
    
        <td class="p-2 bg-grey-lighter text-right align-middle w-1/6">
            Name
        </td>
        <td class="p-2">
            <input name="name" placeholder="Team Name" value="{{ $team->name }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>

            <span class="mx-2">Short Name:</span>

            <input name="short_name" placeholder="Team Name" value="{{ $team->short_name }}" class="border-2 rounded-lg px-4 py-2 w-1/4"/>


        </td>

    </tr>

    <tr class="border-b">
    
        <td class="p-2 bg-grey-lighter text-right align-middle w-1/6">
            District Type
        </td>
        <td class="p-2">


            <select name="district_type"
                    class="border"/>

                <option value="">-- NONE --</option>

                @foreach($available_district_types as $district_type_option)

                    <option value="{{ $district_type_option }}"
                            {{ ($district_type_option == $team->district_type) ? 'selected' : '' }} >{{ $district_type_option }}</option>

                @endforeach

            </select>


            <input name="district_id" placeholder="ID #" size="6" value="{{ $team->district_id }}" class="border-2 rounded-lg px-4 py-2"/>
        </td>

    </tr>


    <tr class="border-b">

        <td class="p-2 bg-grey-lighter text-right align-middle w-1/6">
            DB Slice
        </td>
        <td class="p-2">

            <select name="db_slice"
                    class="border"/>

                <option value="">-- NONE --</option>

                @foreach($available_slices as $slice_option)

                    <option value="{{ $slice_option->name }}"
                            {{ ($slice_option->name == $team->db_slice) ? 'selected' : '' }} >{{ $slice_option->state }} | {{ $slice_option->name }}</option>

                @endforeach

            </select>

            @if(!$team->db_slice)

                <span class="text-red ml-2">
                    Not Using a Voter Slice
                </span>

            @elseif(!$slice)

                <span class="text-red ml-2">
                    "{{ $team->db_slice }}" Slice does not Exist
                </span>

            @endif

        </td>

    </tr>





    <tr class="border-b">

        <td class="p-2 bg-grey-lighter text-right align-middle w-1/6">
            App Type
        </td>
        <td class="p-2 flex">

            <div class="py-2">

                <select name="app_type">

                    @foreach(App\Team::all()->pluck('app_type')->unique()->toArray() as $app_type)
                        <option {{ ($team->app_type == $app_type) ? 'selected' : '' }} value="{{ $app_type }}">{{ $app_type }}</option>
                    @endforeach

                </select>

            </div>


            @if($team->app_type == 'u')

                <div class="p-2">
                    <label for="pilot" class="font-normal ml-4">
                        <input type="checkbox" name="pilot" id="pilot" {{ ($team->pilot) ? 'checked' : '' }} />
                        University Option: PILOT
                    </label>
                </div>

            @endif

            <div x-data="{ open: false }" class="p-2 flex-grow text-right">
                <button type="button" @click="open = true" class="text-blue">Create New App Type</button>

                <div
                    x-show="open"
                    @click.away="open = false"
                    class="mt-2"
                >
                    <input type="text" name="new_app_type" class="border p-2" placeholder="New app type" />
                </div>
            </div>
            


        </td>

    </tr>


</table>



<div class="border-t py-4">

    <div class="font-bold border-b-4 border-red-dark pb-1">
        Add / Remove Developers to this Team:
    </div>

    @foreach(\App\User::whereIn('id',
                                \App\Permission::where('developer', true)->pluck('user_id')->toArray()
                                )->get() as $developer)


        <div class="mt-4">

            <input name="remove_developers_from_team[]" value="{{ $developer->id }}" class="hidden" />

            <label class="font-normal" for="developer_{{ $developer->id }}">
                <input type="checkbox" {{ ($developer->memberOfTeam($team)) ? 'checked' : '' }} name="add_developers_to_team[]" id="developer_{{ $developer->id }}" value="{{ $developer->id }}" class="m-2" /> <span class="">{{ $developer->name }} #{{ $developer->id }}</span>
            </label>

        </div>

    @endforeach

</div>


<div class="mt-4 flex float-right">
    <input type="submit" name="save" value="Update" class="flex-1 flex-initial mr-2 rounded-lg bg-grey-darker hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />

    <input type="submit" name="save_and_close" formaction="/admin/accounts/{{ $theaccount->id }}/teams/{{ $team->id }}/update/close" value="Save & Close" class="flex-1 flex-initial rounded-lg bg-blue hover:bg-blue-dark text-white text-sm px-8 py-2 mt-1 shadow" />
</div>


</form>

</div>


</div>

<br />
<br />
@endsection