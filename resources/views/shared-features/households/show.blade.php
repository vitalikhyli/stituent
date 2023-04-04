@extends(Auth::user()->team->app_type.'.base')

@section('title')
    	{{ $hh->full_address }}
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<!-- <a href="/{{ Auth::user()->team->app_type }}/households"> -->
		Households
	<!-- </a>  -->

    > &nbsp;<b>{{ mb_strimwidth($hh->full_address, 0, 30, "...") }}</b>

@endsection

@section('style')


@endsection

@section('main')

@include('elements.errors')


	<div class="text-2xl font-sans border-b-4 border-blue pb-2">

		<div class="float-right text-sm px-4 py-2 mt-1 font-bold hover:bg-grey-lightest rounded-lg">
			<a href="/{{ Auth::user()->team->app_type }}/households/{{ $hh->id }}/edit">
				Edit
			</a>
		</div>

		<i class="fas fa-home ml-1 mr-2"></i>
		<span class="mr-2">
			<b>{{ $hh->addressNoCity }}</b>
			<span class="text-grey-dark ml-1">| {{ $hh->address_city }},
			{{ $hh->address_state }} {{ $hh->address_zip }}</span>
		</span>

	</div>

	<div class="text-sm">

		<div class="flex border-b">

			<div class="p-2 bg-grey-lighter w-32">
				Created
			</div>

			<div class="p-2 text-grey-darker">
				{{ \Carbon\Carbon::parse($hh->created_at)->format('n/d/y') }}
			</div>

		</div>


		<div class="flex border-b">

			<div class="p-2 bg-grey-lighter w-32">
				Household ID
			</div>

			<div class="p-2 text-blue-dark">
				{!! $hh->householdIDPretty !!}
			</div>

		</div>


		<div class="flex border-b">

			<div class="p-2 bg-grey-lighter w-32">
				Lat/Long
			</div>

			<div class="p-2 text-grey-darker">
				@if($hh->address_lat && $hh->address_long)
					{{ $hh->address_lat }}, {{ $hh->address_long }}
				@else
					Processing
				@endif
			</div>

		</div>

	</div>

	<div class="flex mt-2">

		<div class="w-3/5 pr-4">

			<div class="mt-4">

				<div class="border-b-4 border-blue pb-1 text-xl text-black">
					Residents
					@if($residents->first())
						<span class="text-grey-dark">({{ $residents->count() }})</span>
					@endif
				</div>

				<div>

					@if(!$residents->first())
						<div class="py-1">
							None
						</div>
					@endif

					@foreach($residents as $resident)

						<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $resident->voter_id }}">

							<div class="flex py-1 border-b cursor-pointer">

								<div class="pt-1 pr-2 w-16 text-center">
									<i class="fas fa-user-circle text-grey-dark"></i>
								</div>

								<div class="p-1">

									<div class="">
										<span class="font-bold text-black">{{ $resident->full_name }}</span>
										{{ $resident->age }} {{ $resident->gender }}
									</div>

								</div>

							</div>

						</a>

					@endforeach

				</div>

			</div>

			<div class="mt-6">
				
				<div class="border-b-4 border-blue pb-1 text-xl text-black">
					Cases
					@if($residents->first())
						<span class="text-grey-dark">({{ $hh->cases->count() }})</span>
					@endif
				</div>

				<div>

					@if(!$hh->cases->first())
						<div class="py-1">
							None
						</div>
					@endif

					@foreach($hh->cases as $case)

						<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">

							<div class="flex py-1 border-b cursor-pointer">

								<div class="pt-1 pr-2 w-16 text-grey-dark text-sm text-right">
									{{ \Carbon\Carbon::parse($case->created_at)->format('n/d/y') }}
								</div>

								<div class="p-1">

									<div class="">
										<span class="font-bold text-black">{{ $case->type }}</span>
										{{ $case->subject }}
									</div>

									<div class="text-grey-dark">
										{{ mb_strimwidth($case->notes, 0, 200, '...') }}
									</div>

								</div>

							</div>

						</a>

					@endforeach

				</div>

			</div>


		</div>


		<div class="w-2/5 pl-4">

			<div class="mt-4 w-full bg-grey-lightest" id="map" style="height: 300px;">
				<!-- <img src="/images/map-sample.png" /> -->
			</div>


			<div class="mt-4 hidden">

				<div class="border-b-4 border-grey-light pb-1 text-xl text-blue">
					My Households
				</div>

				<div class="text-sm">

					@foreach($all_hh->groupBy('address_city') as $city => $locs)

						<div class="font-bold mt-2 text-base">
							{{ ucwords(strtolower($city)) }}
						</div>


						@foreach($locs as $loc)

							<div class="truncate py-1 border-b border-dashed hover:bg-orange-lightest">
								<a href="/{{ Auth::user()->team->app_type }}/households/{{ $loc->id }}"
								   class="text-grey-darker">
									<i class="fas fa-home mr-2 text-grey"></i> {{ $loc->full_address }}
								</a>
							</div>

						@endforeach

					@endforeach

				</div>

			</div>

		</div>

	</div>

	
<br />
<br />
@endsection

@section('javascript')

	<script
	    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc">
	</script>

	<script type="text/javascript">
		$(document).ready(function() {
			
			var map;
			var json = "/{{ Auth::user()->team->app_type }}/households/{{ $hh->id }}/map";
			var infowindow = new google.maps.InfoWindow();

			function initializeMap(json) {

			    var mapProp = {
			        mapTypeId: google.maps.MapTypeId.ROADMAP
			    };

			    map = new google.maps.Map(document.getElementById("map"), mapProp);

			    loadJson(json, map);

			}

			function loadJson(json, map) {
				
				$.getJSON(json, function(json1) {

					var southWest = new google.maps.LatLng(json1.bounds.min_lat, json1.bounds.min_lng);
					var northEast = new google.maps.LatLng(json1.bounds.max_lat, json1.bounds.max_lng);
					var bounds = new google.maps.LatLngBounds(southWest,northEast);
					map.fitBounds(bounds);
			    
				    $.each(json1.households, function (key, data) {

				        var latLng = new google.maps.LatLng(data.lat, data.lng);
				        let iconurl = "http://maps.google.com/mapfiles/ms/icons/";
  						iconurl += data.color + "-dot.png";

				        var marker = new google.maps.Marker({
				            position: latLng,
				            map: map,
				            // label: "" + data.contacts,
				            icon: {
						      url: iconurl
						    },
				            title: data.name
				        });

				        var details = "<div class='p-2'>";
				        details += data.address;
				        details += "</div>";

				        bindInfoWindow(marker, map, infowindow, details);

				    });
				});
			}

			function bindInfoWindow(marker, map, infowindow, strDescription) {
			    google.maps.event.addListener(marker, 'click', function () {
			        infowindow.setContent(strDescription);
			        infowindow.open(map, marker);
			    });
			}

			google.maps.event.addDomListener(window, 'load', initializeMap(json));

		});
	</script>

@endsection