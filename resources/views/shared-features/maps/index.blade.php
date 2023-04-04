@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Maps')
@endsection

@section('style')

    

@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a>
    > &nbsp;<b>Maps</b>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full font-bold">
    Maps
  </div>

  @include('shared-features.maps.links')

</div>
<div class="">

  <div class="flex pt-8 mt-8 border-blue pb-2">

		<div class="w-1/6 mt-1 text-grey-dark text-lg font-bold">
			Activity
		</div>
		<div class="w-1/2 mt-1 text-grey-dark text-base">
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/red-dot.png" />
			Open Case
			
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
			Resolved Case
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" />
			Contact
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png" />
			Group
			</div>

		<div class="w-1/3 text-sm text-grey-dark">

			<div id="timeframe-all" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
				All Time
			</div>
			<div id="timeframe-year" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
				12 Months
			</div>
			<div id="timeframe-month" class="map-timeframe cursor-pointer bg-blue text-white px-2 py-1 float-right ml-2">
				30 Days
			</div>
			<div id="timeframe-week" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
				7 Days
			</div>

		</div>
	</div>

	<div id="map" class="w-full border-2 border-t-0" style="height: 600px;"></div>

</div>


@endsection

@section('javascript')

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc"></script>

	@include('shared-features.maps.map-javascript')


@endsection