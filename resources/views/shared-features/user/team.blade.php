@extends(Auth::user()->team->app_type.'.base')


@section('title')
    Team & Settings
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->app_type }}">Home</a>
    > Team Administration

@endsection

@section('style')

	<style>


	</style>

	@livewireStyles


@endsection

@section('main')



<div class="flex pb-2">
	<div class="w-full">

		@if(Auth::user()->permissions->admin)
		<a href="/{{ Auth::user()->team->app_type }}/users/new">
			<button type="button" class="float-right bg-blue text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark">
				Create New User
			</button>
		</a>
		@endif

		<div class="text-2xl font-sans">
			Team: {{ $team->name }}
		</div>
	</div>
</div>


<div class="border-b-4 border-blue py-1 text-xl mt-4">
	 Members
</div>

<table class="w-full border-t text-sm">
	<tr class="border-b-2 border-black bg-grey-lighter text-sm">
		<td class="p-2" colspan="2">
			User
		</td>
		<td class="p-2 text-grey-darker">
			Email
		</td>
		<td class="p-2">
			Title
		</td>
		<td class="p-2 text-xs">
			Dev
		</td>
		<td class="p-2 text-xs">
			Admin
		</td>
		<td class="p-2 text-xs">
			Export
		</td>
		<td class="p-2 text-xs">
			Reports
		</td>
		<td class="p-2 text-xs">
			Metrics
		</td>
		<td class="p-2 text-xs">
			People
		</td>
	</tr>
	@foreach($users as $theuser)
	@if ($theuser->permissions)
		<tr class="clickable border-b cursor-pointer hover:bg-orange-lightest {{ (Auth::user()->id == $theuser->id) ? 'bg-blue-lightest border-grey-dark font-semibold' : '' }}" data-href="/{{ Auth::user()->team->app_type }}/users/{{ $theuser->id }}/edit">
			<td class="">
				<button type="button" class="bg-blue text-white px-2 py-1 rounded-lg text-xs hover:bg-blue-dark">
					Edit
				</button>
			</td>
			<td class="p-2">
				<i class="fas fa-user-cog mr-2"></i>{{ $theuser->name }}
			</td>
			<td class="p-2 text-grey-darker">
				{{ $theuser->email }}
			</td>
			<td class="p-2">
				{{ $theuser->permissionsForTeam(Auth::user()->team)->title }}
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->developer) ? '<i class="fas fa-check-circle"></i>' : '' !!} 
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->admin) ? '<i class="fas fa-check-circle"></i>' : '' !!}
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->export) ? '<i class="fas fa-check-circle"></i>' : '' !!}
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->reports) ? '<i class="fas fa-check-circle"></i>' : '' !!}
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->metrics) ? '<i class="fas fa-check-circle"></i>' : '' !!}
			</td>
			<td class="p-2 text-blue w-12">
				{!! ($theuser->permissionsForTeam(Auth::user()->team)->constituents) ? '<i class="fas fa-check-circle"></i>' : '' !!}
			</td>
		</tr>
	@endif
	@endforeach
</table>


@if($other_teams->first() && (Auth::user()->permissions->developer || Auth::user()->permissions->admin))

	<div class="border-b-4 border-blue py-1 text-xl mt-6">
		Members of All Teams in your Account
	</div>

	<div class="py-2 text-grey-dark mb-4 italic">
		Click to toggle membership of users on each team. You can then edit permissions within each team. <b>Note:</b> Adding users to a campaign team can only be done from within the campaign team.
	</div>

	<table>
		<tr>
			<td class="border-r border-b p-2">
				&nbsp;
			</td>
			@foreach(Auth::user()->team->account->teams as $team)

				@if(Auth::user()->team->app_type == 'office' && $team->app_type == 'campaign')
					@continue
				@endif

				<td class="border-b border-r p-2 bg-blue text-white text-center">
					<div>
						<i class="{{ $team->fa_logo() }} text-3xl"></i>
					</div>
					<div class="p-2">
						{{ $team->name }}
					</div>
				</td>
			@endforeach
		</tr>

		@foreach(Auth::user()->team->account->users()->sortBy('name') as $user)

			@if($user->permissions && $user->permissions->guest)
				@continue
			@endif

			<tr>
				<td class="{{ (Auth::user()->id == $user->id) ? 'bg-orange-lightest font-bold' : '' }} border p-1 w-1/{{ Auth::user()->team->account->teams->count() + 1 }} w-48 truncate">
					{{ $user->name }}
				</td>

				@foreach(Auth::user()->team->account->teams as $team)

					@if(Auth::user()->team->app_type == 'office' && $team->app_type == 'campaign')
						@continue
					@endif

					@if(Auth::user()->permissionsForTeam($team)->admin ||
						Auth::user()->permissionsForTeam($team)->developer ||
						Auth::user()->permissions->developer)

						<td class="{{ (Auth::user()->id == $user->id) ? 'bg-orange-lightest' : '' }} text-center border p-1 text-lg  w-1/{{ Auth::user()->team->account->teams->count() + 1 }}">
							
							@if(!$user->memberOfTeam($team))
								
									<a href="/{{ $team->app_type }}/users/{{ $user->id }}/jointeam/{{ $team->id }}">
										<span class="text-grey"><i class="fas fa-times"></i></span>
									</a>

							@else
								@if($user->allTeams->count() > 1)
									<a href="/{{ $team->app_type }}/users/{{ $user->id }}/leaveteam/{{ $team->id }}">
										<span class="text-blue"><i class="fas fa-check-circle" title="{{ $user->permissionsForTeam($team)->title }}" data-toggle="tooltip" data-placement="top"></i></span>
									</a>
								@else
									<span class="text-blue"><i class="fas fa-check-circle opacity-50" title="{{ $user->permissionsForTeam($team)->title }}" data-toggle="tooltip" data-placement="top"></i></span>
								@endif
							@endif
							
						</td>
					@else

						<td class="bg-grey-lighter text-center border p-1 text-lg  w-1/{{ Auth::user()->team->account->teams->count() + 1 }}">
							
							@if(!$user->memberOfTeam($team))
								<span class="text-grey"><i class="fas fa-times"></i></span>
							@else
								<span class="text-blue"><i class="fas fa-check-circle" title="{{ $user->permissionsForTeam($team)->title }}" data-toggle="tooltip" data-placement="top"></i></span>
							@endif
							
						</td>

					@endif
				@endforeach
			</tr>
		@endforeach
	</table>

@endif


@if(Auth::user()->permissions->developer)

<div class="border-b-4 border-blue pb-1 mt-6 text-xl font-sans">
	Team Options
</div>

<div class="w-full">

	@if(in_array(Auth::user()->team->app_type, ['office', 'u']))

		<div class="flex border-b w-full">

			<div class="p-2 text-right bg-grey-lighter w-1/6">
				Shared Cases
			</div>

			<div class="p-2 w-5/6">

				<div>

					<label for="access_office" class="font-normal">
						<input id="shared" name="access_office" type="checkbox" />
						<span class="ml-1">Enable Shared Cases</span>
					</label>

					
					@if(Auth::user()->team->shared_cases)
						<span class="text-blue ml-2 float-right">Shared Cases are: on</span>
					@else
						<span class="text-red ml-2 float-right">Shared Cases are: off</span>
					@endif

				</div>

			</div>

		</div>

	@endif
	

	<div class="flex border-b w-full">

		<div class="p-2 text-right bg-grey-lighter w-1/6">
			Data Controls
		</div>

		<div class="p-2 w-5/6">

			@if(
				Auth::user()->team->app_type == 'campaign' &&
				Auth::user()->team->account->hasTeamType('office')
				)

				<div>

					<div>

						<label for="access_office" class="font-normal">
							<input id="access_office" name="access_office" type="checkbox" />
							<span class="ml-1">Allow access to <b>office</b> data from within your <b>campaign</b> team</span>
						</label>

						
						@if(Auth::user()->team->access_office)
							<span class="text-blue ml-2 float-right">Access is: on</span>
						@else
							<span class="text-red ml-2 float-right">Access is: off</span>
						@endif

					</div>

					<div class="mb-4 ml-10 pl-4 mt-2 border-l-4 border-blue leading-relaxed">
						<i class="fas fa-info-circle text-blue mr-1 "></i> You may want to keep data that is collected by your <b>office</b> staff separate from your <b>campaign</b> activities. However, if your situation allows, you may enable access. Community Fluency recommends that you consult state ethics rules.
					</div>

				</div>

			@endif



			@if(
				Auth::user()->team->app_type == 'office' &&
				Auth::user()->team->account->hasTeamType('campaign')
				)

				<div>

					<div>

						<label for="access_campaign" class="font-normal">
							<input id="access_campaign" name="access_campaign" type="checkbox" />
							<span class="ml-1">Allow access to <b>campaign</b> data from within your <b>office</b> team</span>
						</label>

						
						@if(Auth::user()->team->access_campaign)
							<span class="text-blue ml-2 float-right">Access is: on</span>
						@else
							<span class="text-red ml-2 float-right">Access is: off</span>
						@endif

					</div>

					<div class="mb-4 ml-10 pl-4 mt-2 border-l-4 border-blue leading-relaxed">
						<i class="fas fa-info-circle text-blue mr-1 "></i> You may want to keep data that is collected by your <b>campaign</b> staff separate from your <b>office</b> activities. However, if your situation allows, you may enable access. Community Fluency recommends that you consult state ethics rules.
					</div>

				</div>

			@endif



		</div>

	</div>

	<div class="flex border-b">

		<div class="p-2 text-right bg-grey-lighter w-1/6">
			Dashboard Logo
		</div>

		<div class="p-2 w-5/6">

			@livewire('files.logo', ['formMode' => true])

		</div>

	</div>

</div>

@endif



<br />
<br />
@endsection

@section('javascript')

	@livewireScripts

	<script type="text/javascript">
		$(document).ready(function() {

		    $(".clickable").click(function() {
		        window.location = $(this).data("href");
		    });

		    $("input[type=file]").change(function(e){
	            var fileName = e.target.files[0].name;
		    	$("#file_selected").text(fileName); 
		    });

			$( "#file_upload_form" ).submit(function( event ) {
				var fileName = $('#fileToUpload').val();
				if (fileName == '') { event.preventDefault(); }
			});

		});
	</script>

@endsection
