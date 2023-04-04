@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit User
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Edit', 'edit', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')


	<form method="POST" action="/{{ Auth::user()->team->app_type }}/users/{{$user->id}}/update">
		{{ csrf_field() }}

	<div class="flex border-b mb-4 pb-2">
		<div class="w-full">

		<div class="float-right">
			<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
			<input type="submit" formaction="/{{ Auth::user()->team->app_type }}/users/{{$user->id}}/update/close" "name="update" value="Save and Close" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
		</div>
			<div class="text-2xl font-sans">
				<i class="fas fa-user mr-4"></i>{{ $user->name }}
			</div>
		</div>
	</div>

	@include('elements.errors')

	<div class="border-b-4 border-grey text-lg py-1 px-2 bg-blue text-white rounded-t-lg">
		Basics
	</div>

	<table class="w-full">
		<tr class="border-b text-left p-2">
			<td class="w-1/5 border-b px-2 py-2 text-right">
				Name
			</td>
			<td class="border-b text-left p-2">
				<input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : $user->name }}" />
			</td>
		</tr>
		<tr class="border-b text-left p-2">
			<td class="w-1/5 border-b px-2 py-2 text-right">
				Email
			</td>
			<td class="border-b text-left p-2">
				<input type="text" name="email" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('email') : $user->email }}" />
			</td>
		</tr>
		<tr class="border-b text-left p-2">
			<td class="w-1/5 border-b px-2 py-2 text-right">
				Username (optional)
			</td>
			<td class="border-b text-left p-2">
				<input type="text" name="username" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('username') : $user->username }}" />
			</td>
		</tr>

		@if(Auth::user()->permissions->admin)
			<tr class="border-b text-left p-2">
				<td class="w-1/5 border-b px-2 py-2 text-right">
					Status
				</td>
				<td class="border-b text-left p-2">
					<label for="active_1"><input type="radio" name="active" id="active_1" value="1" {{ ($user->active) ? 'checked' : '' }}/> Active</label> | <label for="active_2"><input type="radio" name="active" id="active_2" value="0" {{ (!$user->active) ? 'checked' : '' }} /> Inactive</label>
				</td>
			</tr>
		@endif

		@if(Auth::user()->permissions->admin)
			<tr class="text-left p-2">
				<td class="w-1/5 px-2 py-2 text-right">
					On Team
				</td>
				<td class="text-left p-2">
					<label for="on_team" class="font-normal"><input type="checkbox" name="on_team" id="on_team" value="1" {{ ($user->memberOfTeam(Auth::user()->team)) ? 'checked' : '' }}/> <span class="font-bold text-blue">{{ Auth::user()->team->name }}</span></label>

					<div class="float-right italic text-grey-darker text-sm pt-1">
						Unchecking this will remove the user from the team
					</div>
				</td>
			</tr>
		@endif

	</table>


	<div class="border-b-4 border-grey text-lg py-1 px-2 bg-blue text-white rounded-t-lg mt-6">

		Change Password

		@if($user->change_password && $user->id == Auth::user()->id)

			<span class="font-medium pl-1 text-orange-lighter text-base">
				**** Please change your password
			</span>

		@endif

	</div>

		<table class="w-full">

			@if(Auth::user()->permissions->admin)

			<tr class="border-b text-left p-2">
				<td class="w-1/5 border-b px-2 py-2 text-right">
					Change Password
				</td>
				<td class="border-b text-left p-2">
					<label for="change_password" class="font-normal">
						@if($user->password)
							<input type="checkbox" name="change_password" id="change_password" value="1" {{ ($user->change_password) ? 'checked' : '' }} />
							Require user to change password
						@else
							<input type="checkbox" disabled name="change_password" id="change_password" value="1" {{ ($user->change_password) ? 'checked' : '' }} />
							Require User to create password
						@endif
					</label>
				</td>
			</tr>
			
			@endif

			@if(($user->login_token) || (Auth::user()->permissions->admin))

			<tr class="border-b text-left p-2">
				<td class="w-1/5 border-b px-2 py-2 text-right align-top">
					
				</td>
				<td class="border-b text-left p-2">
					@if($user->login_token)
						<span class="text-grey-dark">communityfluency.com/link/</span>{{ $user->login_token }}
						@if(Auth::user()->permissions->admin)
							<span class="float-right">
								<div id="email_link_to_user" class="bg-grey-lighter rounded-lg px-2 py-1 border text-sm cursor-pointer"><i class="fas fa-envelope"></i> Email link to {{ substr($user->email,0,strpos($user->email,'@')+1) }}&hellip;</div>
							</span>
						@endif
					@else
						@if(Auth::user()->permissions->admin)
							<label for="new_login_token" class="font-normal">
								<input type="checkbox" name="new_login_token" id="new_login_token" value="1" /> Generate new login token (save page to see token)
							</label>
						@endif
					@endif
				</td>
			</tr>

			@endif



			<tr class="border-b text-left p-2">
				<td class="w-1/5 border-b px-2 py-2 text-right">
					New Password:
				</td>
				<td class="border-b text-left p-2">
					<input type="password" name="new_1" value="{{ old('new_1') ? old('new_1') : '' }}" class="rounded-lg px-2 py-1 border"/>
				</td>
			</tr>
			<tr class="text-left p-2">
				<td class="w-1/5 px-2 py-2 text-right">
					Confirm:
				</td>
				<td class="text-left p-2">
					<input type="password" name="new_2" value="{{ old('new_2') ? old('new_2') : '' }}" class="rounded-lg px-2 py-1 border"/>
				</td>
			</tr>
		</table>


		<div class="border-b-4 border-grey text-lg py-1 px-2 bg-blue text-white rounded-t-lg mt-6">
			Preferences
		</div>

			<table class="w-full">
				<tr class="text-left p-2">
					<td class="w-1/5 px-2 py-2 text-right">
						Language
					</td>
					<td class="text-left p-2">
						<select name="language">
							<option value="de" disabled {!! ($user->language == 'de') ? 'selected' : '' !!}>Deutsch</option>
							<option value="en" {!! ($user->language == 'en') ? 'selected' : '' !!}>English</option>
							<option value="es" {!! ($user->language == 'es') ? 'selected' : '' !!}>Espa&ntilde;ol</option>
							<option value="ga" disabled {!! ($user->language == 'ga') ? 'selected' : '' !!}>Gaeilge</option>

						</select>
					</td>
				</tr>
			</table>

	@if(Auth::user()->permissions->admin)

		<div class="border-b-4 border-grey text-lg py-1 px-2 bg-blue text-white rounded-t-lg mt-6">
			Roles and Permissions
		</div>

		<table class="w-full">
			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24 p-2">Title:</div><input type="text" name="title" class="border rounded-lg p-2 w-1/2 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('title') : $user->permissionsForTeam(Auth::user()->team)->title }}" />
				</td>
			</tr>

			@if(Auth::user()->permissions->developer)
				<tr class="text-left p-2">
					<td class="text-left p-2 flex">
						<div class="w-24">&nbsp;</div>
						<input type="hidden" name="developer_is_option" value="yes" />
						<label class="font-normal" for="developer">
						<input type="checkbox" name="developer" id="developer" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->developer) ? 'checked' : '' }} /> <span class="ml-2">Developer</span>
						</label>
					</td>
				</tr>
			@endif

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="admin_is_option" value="yes" />
					<label class="font-normal" for="admin">
					<input type="checkbox" name="admin" id="admin" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->admin) ? 'checked' : '' }} /> <span class="ml-2">Admin</span>
					</label>
				</td>
			</tr>

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="export_is_option" value="yes" />
					<label class="font-normal" for="export">
					<input type="checkbox" name="export" id="export" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->export) ? 'checked' : '' }} /> <span class="ml-2">Export</span>
					</label>
				</td>
			</tr>

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="reports_is_option" value="yes" />
					<label class="font-normal" for="reports">
					<input type="checkbox" name="reports" id="reports" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->reports) ? 'checked' : '' }} /> <span class="ml-2">Reports</span>
					</label>
				</td>
			</tr>

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="metrics_is_option" value="yes" />
					<label class="font-normal" fir="metrics">
					<input type="checkbox" name="metrics" id="metrics" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->metrics) ? 'checked' : '' }} /> <span class="ml-2">Metrics</span>
					</label>
				</td>
			</tr>

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="constituents_is_option" value="yes" />
					<label class="font-normal" for="constituents">
					<input type="checkbox" name="constituents" id="constituents" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->constituents) ? 'checked' : '' }} /> <span class="ml-2">Constituents</span>
					</label>
				</td>
			</tr>
			
			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="creategroups_is_option" value="yes" />
					<label class="font-normal" for="creategroups">
					<input type="checkbox" name="creategroups" id="creategroups" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->creategroups) ? 'checked' : '' }} /> <span class="ml-2">Create / Delete Groups</span>
					</label>
				</td>
			</tr>

			<tr class="text-left p-2">
				<td class="text-left p-2 flex">
					<div class="w-24">&nbsp;</div>
					<input type="hidden" name="createconstituents_is_option" value="yes" />
					<label class="font-normal" for="createconstituents" >
					<input type="checkbox" name="createconstituents" id="createconstituents" value="1" {{ ($user->permissionsForTeam(Auth::user()->team)->createconstituents) ? 'checked' : '' }} /> <span class="ml-2">Create Constituents</span>
					</label>
				</td>
			</tr>
			
		</table>

	@endif


	


	@if(Auth::user()->permissions->developer)

		<div class="border-b-4 border-grey text-lg py-1 px-2 bg-blue text-white rounded-t-lg mt-6">
			Secret Developer Section
		</div>

			<table class="w-full text-black font-mono text-sm">
				<tr class="border-b text-left p-2">
					<td class="w-1/5 border-b px-2 py-2 text-right whitespace-no-wrap align-top">
						Memory
					</td>
					<td class="border-b text-left p-2">
						@if($user->memory)
						<?php
							$memory = $user->memory;
							foreach ($memory as $key => $value) {
								if (is_array($value)) {
									echo '<div class="flex"><div class="w-64 font-medium text-blue">'.$key.'</div>  <i>'.implode(',',$value).'</i></div>';
								} else {
									echo '<div class="flex"><div class="w-64 font-medium text-blue">'.$key.'</div>  <i>'.$value.'</i></div>';
								}
							}
						?>
						@endif
					</td>
				</tr>
				<tr class="border-b text-left p-2">
					<td class="w-1/5 border-b px-2 py-2 text-right whitespace-no-wrap">
						Accepted Terms
					</td>
					<td class="border-b text-left p-2">
						@if(!$user->accepted_terms )
							<span class="text-red">Terms not accepted</span>
						@else
							{{ $user->accepted_terms }}
						@endif
					</td>
				</tr>
				<tr class="border-b text-left p-2">
					<td class="w-1/5 border-b px-2 py-2 text-right whitespace-no-wrap">
						Last Login
					</td>
					<td class="border-b text-left p-2">
						{{ \Carbon\Carbon::parse($user->last_login)->format("h:i:s F d, Y") }}
					</td>
				</tr>
			</table>
	@endif

			<div class="p-4 text-center w-full">
				<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
				<input type="submit" formaction="/{{ Auth::user()->team->app_type }}/users/{{$user->id}}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
			</div>

		</form>

<br />
<br />

@endsection

@section('javascript')

<script type="text/javascript">
	$(document).ready(function() {

	    $(document).on("click", "#email_link_to_user", function() {

	    	if($('#email_link_to_user').attr("done") != 1) {

				$('#email_link_to_user').html('<i class="fas fa-spinner fa-spin mr-2"></i>Waiting...');

		    	var url = '/{{ Auth::user()->team->app_type }}/users/emaillinktouser/{!! base64_encode($user->id) !!}';

		    	// alert(url);
		    	
		        $.get(url, function(response) {
		        	$('#email_link_to_user').attr("done", 1);
		            $('#email_link_to_user').html(response);
		            $('#email_link_to_user').addClass('bg-orange-lighter shadow');
		        }); 

	    	} else {
	    		//alert('Already sent.');
	    	}

        });

    });
</script>


@endsection
