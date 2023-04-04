@extends('campaign.ostrich.template')

@section('title')
  
  Dashboard

@endsection

@section('header')

	Dashboard

@endsection

@section('content')

<div class="md:flex p-2">

	<div class="md:w-2/3 pr-4">

		<div class="mb-2">

			<div class="font-bold py-1 border-b-4 mb-2 text-lg text-gray-800">
				My Walk Lists
			</div>

			<div class="pr-2 py-2 leading-normal text-gray-600">
				x
			</div>

		</div>

		<div class="mb-2">

			<div class="font-bold py-1 border-b-4 text-lg text-gray-800">
				My Call Lists
			</div>

			<div class="pr-2 py-2 leading-normal text-gray-600">
				@foreach(VolunteerSession()->opportunities()
										   ->activeAndNotExpired()
										   ->where('type', 'phonebank')
										   ->get() as $opp)

					<div class="py-2 border-b text-blue">
						{{ $opp->name }}
					</div>

				@endforeach
			</div>

		</div>

	</div>

	<div class="md:w-1/3">

		<div class="mb-2">

			<div class="font-bold py-1 border-b-4 mb-2 text-lg text-gray-800">
				About
			</div>

			<div class="pr-2 py-2 leading-normal text-gray-600">
				Ostrich is CampaignFluency's suite of voter contact tools. Run like an Ostrich!
			</div>

		</div>

	</div>

</div>


@endsection('content')