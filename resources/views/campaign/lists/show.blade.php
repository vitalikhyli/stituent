@extends('campaign.base')

@section('title')
    {{ $list->name }}
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > <a href="/campaign/lists">Campaign Lists</a>
    > &nbsp;<b>{{ $list->name }}</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">

		<div class="float-right text-lg mt-4 text-blue">
			{{ $list->count() }} Voters
		</div>
		{{ $list->name }}
		<!-- <i class="fa fa-info-circle text-grey hover:text-blue transition cursor-pointer pl-2"></i> -->
		<!-- <span class="text-blue">*</span> -->
	</div>

	<!-- <div class="flex text-grey-dark w-full">
		<div class="w-2/3">

			<div class="text-center m-8 pt-4">
				<a href="/campaign/lists/new" class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark hover:text-white">
					Edit List
				</a>
			</div>

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> 

			</div>
		</div>
	</div> -->

	<div class="w-full my-12">
			<div class="flex mt-8 border-b-2 pb-2">

				<div class="w-1/2 mt-1 text-grey-dark text-base flex">
					
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" />
					Voter
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
					Participant
					
  				</div>

				<div class="w-1/2 text-sm text-grey-dark">

					<!-- <div id="timeframe-year" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
						12 Months
					</div>
					<div id="timeframe-month" class="map-timeframe cursor-pointer bg-blue text-white px-2 py-1 float-right ml-2">
						30 Days
					</div> -->

				</div>
			</div>

			<div id="map" class="w-full border-2 border-t-0" style="height: 400px;"></div>

		</div>

	<table class="table text-grey-dark">

		<tr>
			<th></th>
			<th>Name</th>
			<th>ID</th>
			<th></th>
		</tr>

		@foreach ($voters as $voter)

			<tr>
				<td class="text-grey w-4">{{ $loop->iteration }}.</td>
				<td>
					<a href="/campaign/voters/{{ $voter->id }}">
						{{ $voter->name }}
					</a>
				</td>
				<td>
					{{ $voter->id }}
				</td>
				<td>
					{{ $voter->full_address }}
				</td>

			</tr>

		@endforeach

	</table>


	@include('campaign.lists.export-modal')

@endsection


@section('javascript')


 	@include('campaign.lists.map')

 	@include('campaign.lists.export-modal-js')
 				
@endsection