@extends('campaign.base')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
	HQ
@endsection


@section('style')

	@livewireStyles

@endsection


@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2 mb-3 flex">

		@if(!$campaign_current)
			<div class="text-center">

				<div class="flex-shrink float-right text-base">
					<form action="/{{ Auth::user()->team->app_type }}/campaigns/new" method="post">
						@csrf

						<input required type="text" name="name" placeholder="New Campaign Name" class="border p-2 rounded-lg" />

						<button class="rounded-lg bg-blue text-white p-2 text-sm uppercase">
							Create New Campaign
						</button>

					</form>
				</div>
			</div>
			
		@else

			{{ $campaign_current->name }}

			<div class="ml-4 mt-1 border-l-4 pl-3">
				<div class="text-sm font-bold text-blue">
					{{ \Carbon\Carbon::parse($campaign_current->election_day)->format("F j, Y") }}
				</div>
				<div class="text-xs text-grey-darker">
					@if(\Carbon\Carbon::parse($campaign_current->election_day) == \Carbon\Carbon::today())
						Today
					@else
						{{ \Carbon\Carbon::parse($campaign_current->election_day)->diffForHumans(['options' => \Carbon\CarbonInterface::ROUND]) }}
					@endif
				</div>
			</div>

			<div class="flex-1 text-right font-normal flex-grow">
			
				<a href="/{{ Auth::user()->team->app_type }}/campaigns" class="rounded-full px-3 py-1 text-xs mt-1 ml-2 bg-blue text-grey-lightest">
						All Campaigns
				</a>

			</div>

		@endif
				
	</div>


	@livewire('files.logo')



<div class="flex">



	<div class="w-2/3 pr-8">

		@if($campaign_current)

			@include('campaign.dashboard.votes-needed')

		@endif

		@include('campaign.dashboard.support-stats')

		<div class="border-b-4 mt-8 text-xl pb-1">

			<div class="float-right text-sm text-grey-dark pt-1">
				<a href="/campaign/lists">See All...</a>
			</div>

			Recent <b>Lists</b>
		</div>

		@if ($lists->count() > 0)
		<table class="w-full text-grey-dark">
			@foreach ($lists as $list)
				<tr class="hover:bg-grey-lightest text-sm">
					<td class="p-2 pr-3">
						<a href="/campaign/lists/{{ $list->id }}">
							{{ $list->name }}
						</a>
					</td>
					<!-- <td class="">
						$list->voterParticipants()->count() Participants
					</td> -->
					<td class="py-2 text-right">
						@php
							$listcount = $list->count()
						@endphp
						@if ($listcount == null)
							Error w/ List
						@else
							{{ number_format($listcount) }} Voters
						@endif
					</td>				
				</tr>
			@endforeach
		</table>
		@else

			<div class="text-grey-dark p-2">
				No lists built yet
			</div>

		@endif


		<div class="border-b-4 mt-8 text-xl pb-1 mt-4">

			<a href="/{{ Auth::user()->team->app_type }}/donations/">
				<button class="ml-4 px-2 py-1 text-xs mt-1 float-right">
					See All
				</button>
			</a>

			Recent <b>Contributions</b>
		</div>

		@if(!$donations_recent->first())
			<div class="text-grey-dark p-2">None</div>
		@endif

		@foreach($donations_recent as $donation)

			<div class="flex text-sm text-grey-darker {{ (!$loop->last) ? 'border-b' : '' }}">
				<div class="flex-1 py-2">
					{{ $donation->date }}
				</div>
				<div class="flex-1 py-2">
					{{ $donation->full_name }}
				</div>	
				<div class="flex-1 py-2">
					{{ $donation->city }}
				</div>		
				<div class="flex-1 py-2 truncate text-right">
					${{ number_format($donation->amount,2,'.',',') }}
				</div>
			</div>

		@endforeach

	</div>


	<div class="w-1/3">

		@include('campaign.dashboard.duplicates', [$data = new \App\ParticipantDuplicateViewModel])
		@if(1==2 && Auth::user()->permissions->developer)

			@php
				$nonmatching = Auth::user()->team->participantsWithNonmatchingCityCodes();
			@endphp

			@if ($nonmatching->first())
				<div>
					<div class="border-b-4 text-xl pb-1 mt-4 text-red">

						<a href="/{{ Auth::user()->team->app_type }}/nonmatching/">
							<button class="ml-4 px-2 py-1 text-xs mt-1 float-right">
								See All
							</button>
						</a>

						Non-Matching <b>Cities</b>
					</div>
					<div class="py-2 text-grey-dark">
						 (DEV DRAFT)
					</div>
					@foreach($nonmatching as $participant)
						<div>
							{{ $participant->full_name }}
						</div>
					@endforeach

				</div>
			@endif

		@endif

		<div>
			<div class="border-b-4 text-xl pb-1 mt-4">

				<a href="/{{ Auth::user()->team->app_type }}/participants">
					<button class="ml-4 px-2 py-1 text-xs mt-1 float-right">
						See All
					</button>
				</a>

				New <b>Participants</b>
			</div>

			@if(!\App\Participant::thisTeam()->first())
				<div class="text-grey-dark p-2">None</div>
			@endif

			@foreach(\App\Participant::thisTeam()->latest()->take(10)->get() as $participant)

				<div class="flex text-sm text-grey-darker {{ (!$loop->last) ? 'border-b' : '' }}">
					<div class="flex-1 p-1 whitespace-no-wrap w-24">
						{{ $participant->updated_at->format('n/j/y g:ia') }}
					</div>		
					<div class="flex-1 p-1">
						<a href="/campaign/participants/{{ $participant->id }}">
							{{ $participant->full_name }}
						</a>
					</div>
				</div>

			@endforeach

		</div>



		<div>

			<div class="border-b-4 text-xl pb-1 mt-4">

				<a href="/{{ Auth::user()->team->app_type }}/voters/">
					<button class="ml-4 px-2 py-1 text-xs mt-1 float-right">
						See All
					</button>
				</a>

				Recent <b>Support</b>
			</div>

			@if(!$support_recent->first())
				<div class="text-grey-dark p-2">None</div>
			@endif

			@foreach($support_recent as $pivot)

				<div class="flex text-sm text-grey-darker {{ (!$loop->last) ? 'border-b' : '' }}">
					<div class="flex-1 p-1 whitespace-no-wrap w-24">
						{{ $pivot->updated_at->format('n/j/y g:ia') }}
					</div>		
					<div class="flex-1 p-1">
						<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $pivot->participant->id }}">
							{{ $pivot->participant->full_name }}
						</a>
					</div>
					<div class="flex-1 p-1">
						{{ SupportNumberToEnglish($pivot->support) }}
					</div>	
				</div>

			@endforeach

		</div>


		<div>

			@if($campaign_current)

				<div class="opacity-0">
					<div class="border-b-2 border-blue-dark text-blue-dark text-lg pb-1">
						Other Stats
					</div>

					<table class="w-full">
						<tr class="border-b hover:bg-orange-lightest cursor-pointer">
							<td class="p-2">Doors knocked</td>
							<td class="p-2">0</td>
							<td class="p-2 w-10"><button class="text-orange-dark text-sm rounded-lg px-2 py-1">List</button></td>
						</tr>
						<tr class="border-b hover:bg-orange-lightest cursor-pointer">
							<td class="p-2">Calls made</td>
							<td class="p-2">0</td>
							<td class="p-2 w-10"><button class="text-orange-dark text-sm rounded-lg px-2 py-1">List</button></td>
						</tr>
						<tr class="border-b hover:bg-orange-lightest cursor-pointer">
							<td class="p-2">Lawnsigns</td>
							<td class="p-2">0</td>
							<td class="p-2 w-10"><button class="text-orange-dark text-sm rounded-lg px-2 py-1">List</button></td>
						</tr>
						<tr class="border-b hover:bg-orange-lightest cursor-pointer">
							<td class="p-2">Volunteers</td>
							<td class="p-2">0</td>
							<td class="p-2 w-10"><button class="text-orange-dark text-sm rounded-lg px-2 py-1">List</button></td>
						</tr>
					</table>
				</div>

			@endif
		</div>

	</div>

</div>

<br /><br />
@endsection


@section('javascript')
	
	@livewireScripts

	@include('shared-features.calendar.javascript')

	<script type="text/javascript">
		


		$(document).ready(function() {



			$("#search").keyup(function(){
				getSearchData(this.value);

			});
			function getSearchData(v) {
				var mode = 'dashboard';
				// alert('/campaign/'+mode+'_search/'+v);
				$.get('/campaign/'+mode+'_search/'+v, function(response) {
					$('#list').replaceWith(response);
				});
			}

			$("#search").focus();

			$("#search").focusout(function(){
				window.setTimeout(function() { $('#list').replaceWith('<div id="list"></div>'); }, 300);
			});
		});

	</script>

@endsection