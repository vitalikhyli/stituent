@extends('campaign.base')

@section('title')
    Campaigns
@endsection

@section('main')

<div class="w-full">

	<div class="text-3xl font-bold border-b-4 pb-2">
		{{ $campaigns->count() }} {{ Str::plural('Campaign', $campaigns->count()) }}
		<span class="text-blue">*</span>
	</div>

	<div class="flex">
		<div class="w-2/3">
			<div class="text-center m-8">
				<form action="/{{ Auth::user()->team->app_type }}/campaigns" method="post">
					@csrf

					<input required type="text" name="name" placeholder="New Campaign Name" class="border border p-2 rounded-full text-lg mr-2" />

					<button type="submit" class="rounded-full mt-2 bg-blue text-white py-2 px-4 text-base tracking-wide uppercase hover:bg-blue-dark">
							Start New Campaign
					</button>
				</form>
			</div>
		</div>
		<div class="w-1/3 text-grey-dark">
			
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Create a <span class="font-bold text-black">Campaign</span> based on which office you are currently running for. Every campaign starts fresh with no assumed voter support or volunteers.
			</div>
		</div>
	</div>


	<div class="table w-full text-sm">

			<div class="table-row text-sm">

				<div class="table-cell p-2 border-b bg-grey-lighter">
					
				</div>

				<div class="table-cell p-2 border-b bg-grey-lighter">
					Name
				</div>

				<div class="table-cell p-2 border-b bg-grey-lighter">
					Election Day
				</div>

				<div class="table-cell p-2 border-b bg-grey-lighter">
					
				</div>

				<div class="table-cell p-2 border-b bg-grey-lighter">
					Current?
				</div>

			</div>

		@foreach($campaigns as $campaign)

			<div class="table-row {{ ($campaign->current) ? 'bg-blue-lightest' : '' }}">

				<div class="table-cell p-2 border-b">
					<a href="/{{ Auth::user()->team->app_type }}/campaigns/{{ $campaign->id }}/edit">
						<button class="text-xs rounded-lg bg-blue text-white px-2 py-1 uppercase">
							Edit
						</button>
					</a>
				</div>

				<div class="table-cell p-2 border-b">
					{{ $campaign->name }}
				</div>

				<div class="table-cell p-2 border-b">
					{{ \Carbon\Carbon::parse($campaign->election_day)->format('l F j, Y') }}
				</div>

				<div class="table-cell p-2 border-b text-grey-dark">
					{{ \Carbon\Carbon::parse($campaign->election_day)->diffForHumans() }}
				</div>

				<div class="table-cell p-2 border-b text-blue">
					@if($campaign->current)
						<i class="fas fa-check-circle ml-2"></i> Current Campaign
					@endif
				</div>

			</div>
		@endforeach


	</div>



</div>

<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {


	});

</script>
@endsection