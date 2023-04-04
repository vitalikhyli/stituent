@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

@endsection

@section('main')


<div class="w-full pb-2 ">

    <div class="text-xl border-b-4 border-red py-2">
        Edit User
    </div>  


<form method="POST" id="contact_form" action="/admin/accounts/{{ $theaccount->id }}/users/{{ $user->id }}/update">
    
    @csrf




<table id="accountTable" class="w-full border-t">

    <tr class="border-b">
    
        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Name
        </td>
        <td class="p-1">
            <input name="name" placeholder="User Name" id="user_name" value="{{ $user->name }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
        </td>

    </tr>
    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Email
        </td>
        <td class="p-1">
            <input name="email" placeholder="Email" value="{{ $user->email }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
        </td>

    </tr>

    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right w-1/6" valign="top">
            Teams
        </td>
        <td class="p-1">

            @foreach($user->allteams_and_accountteams() as $theteam)


                <div class="flex">


                    <div class="ml-2">
                        <label class="font-normal" for="team_{{ $theteam->id }}">
                            <input type="checkbox" name="teams[]" id="team_{{ $theteam->id }}" value="{{ $theteam->id }}" {{ (in_array($theteam->id, $member_of_teams)) ? 'checked' : '' }}/><span class="ml-2">{{ $theteam->name }}</span>
                        </label>
                    </div>

                    <div class="flex-grow text-right">

                        @if(!$user->teams->contains($theteam))

                            <div class="inline-block w-64 border bg-grey-lightest px-4 py-2 text-sm text-center text-grey-dark">

                                Team is part of this account but this user doesn't have access. (Check the box to add them.)

                            </div>

                        @else
                            @if($theteam->id == $user->current_team_id)
                                <button type="button"
                                        class="ml-2 rounded-lg text-sm px-3 py-2 text-white bg-blue">
                                    Current Team
                                </button>

                            @else
                                <input type="submit"
                                       name="set-current_{{ $theteam->id }}"
                                       class="ml-2 rounded-lg text-sm px-3 py-2 text-white bg-green hover:bg-green-dark"
                                       value="Set as Current Team"
                                        />
                            @endif

                        @endif

                    </div>

                </div>


                <div class="pl-8 text-grey-dark w-full {{ (!$loop->last) ? 'border-b mb-4 pb-2' : '' }} my-2 cursor-pointer w-full">

                    @foreach($user->getPermissionsArrayFor($theteam) as $key => $permission)

                        @if($key % 4 == 0)
                            <div class="w-full flex">
                        @endif
                        <div class="w-1/4">

                            <label class="font-normal" for="permission_{{ $permission->id }}_{{ $permission->name }}">

                                <input type="hidden" name="permission_{{ $permission->id }}_{{ $permission->name }}" value="0" />

                                <input type="checkbox" name="permission_{{ $permission->id }}_{{ $permission->name }}" id="permission_{{ $permission->id }}_{{ $permission->name }}" value="1" {{ ($permission->value) ? 'checked' : '' }}/><span class="ml-2">{{ $permission->name }}</span>

                            </label>

                        </div>

                        @if($key % 4 == 3 || $loop->last)
                            </div>
                        @endif

                    @endforeach
                </div>

            @endforeach
        </td>

    </tr>



    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Login Link
        </td>
        <td class="p-1">

            @if($user->login_token)
                <span class="text-grey-dark">{{ config('app.url') }}/link/</span>{{ $user->login_token }}

                <span class="float-right">
                    <div id="email_link_to_user" class="bg-grey-lightest rounded-lg px-2 py-1 border text-sm cursor-pointer"><i class="fas fa-envelope"></i> Email link to {{ substr($user->email,0,strpos($user->email,'@')+1) }}&hellip;</div>
                </span>

            @else
                <label for="new_login_token" class="font-normal">
                    <input type="checkbox" name="new_login_token" id="new_login_token" value="1" /> Generate new login token and require change password
                </label>
            @endif

        </td>

    </tr>


    <tr class="border-b">

        <td class="p-1 bg-grey-lighter text-right align-middle w-1/6">
            Manually Set Password
        </td>
        <td class="p-1">

            @livewire('admin.set-password', ['user_id' => $user->id])

        </td>

    </tr>


</table>



<div class="mt-4 flex float-right">
    <input type="submit" name="save" value="Update" class="flex-1 flex-initial mr-2 rounded-lg bg-grey-darker hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />

    <input type="submit" name="save_and_close" formaction="/admin/accounts/{{ $theaccount->id }}/users/{{ $user->id }}/update/close" value="Save & Close" class="flex-1 flex-initial rounded-lg bg-blue hover:bg-blue-dark text-white text-sm px-8 py-2 mt-1 shadow" />
</div>


</form>

</div>


</div>

<br />
<br />
@endsection

@section('javascript')
    @livewireScripts

<script type="text/javascript">
    
    function copyToClipboard() {
      var copyText = document.getElementById("the_password");
      copyText.select(); 
      copyText.setSelectionRange(0, 99999); /*For mobile devices*/
      document.execCommand("copy");
    }

</script>

@endsection