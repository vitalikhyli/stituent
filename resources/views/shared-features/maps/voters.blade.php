@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Voter Maps')
@endsection

@section('style')

    	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.css" rel="stylesheet"></link>

	<style>

		/* The switch - the box around the slider */
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 60px;
		  height: 34px;
		}

		/* Hide default HTML checkbox */
		.switch input {
		  opacity: 0;
		  width: 0;
		  height: 0;
		}

		/* The slider */
		.slider {
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		.slider:before {
		  position: absolute;
		  content: "";
		  height: 26px;
		  width: 26px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		input:checked + .slider {
		  background-color: #2196F3;
		}

		input:focus + .slider {
		  box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round {
		  border-radius: 34px;
		}

		.slider.round:before {
		  border-radius: 50%;
		}

		.slim-select {
			opacity:0;
		}


	</style>

@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a>
    > <a href="/{{ Auth::user()->team->app_type }}/maps">Maps</a>
    > &nbsp;<b>Voters</b>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full font-bold">
    Voter Maps
  </div>

  @include('shared-features.maps.links')

	

</div>

<div id="find-constituents" class="">
  	@include('shared-features.maps.voters-query-form')
</div>

<div class="flex pt-8 mt-8 border-blue pb-2">

	<div class="w-1/6 mt-1 text-grey-dark text-lg font-bold">
		Voters
	</div>
	<div class="w-1/2 mt-1 text-grey-dark text-base">
		<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" />
		Voter File Only
		
		<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
		Linked

	</div>

	<div class="w-1/3 text-right text-sm text-grey-dark pt-2 pr-2">

		Showing <b>{{ min($limit, $total_count) }}</b> pins out of {{ number_format($total_count) }}

	</div>
</div>

<div id="map" class="w-full border-2 border-t-0" style="height: 600px;"></div>


@endsection

@section('javascript')

	<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc"></script>

	@include('shared-features.maps.map-voter-javascript')


	<script type="text/javascript">

		$(document).ready(function() {


			createSlimSelects();

			function createSlimSelects() {
				new SlimSelect({
				  select: '#slim-select-congress-district',
				  placeholder: 'Congressional Districts'
				})
				new SlimSelect({
				  select: '#slim-select-senate-district',
				  placeholder: 'Senate Districts'
				})
				new SlimSelect({
				  select: '#slim-select-house-district',
				  placeholder: 'House Districts'
				})
				new SlimSelect({
				  select: '#slim-select-municipality',
				  placeholder: 'Municipalities'
				})
				new SlimSelect({
				  select: '#slim-select-party',
				  placeholder: 'Parties'
				})
				new SlimSelect({
				  select: '#slim-select-zip',
				  placeholder: 'Zip Codes'
				})
				@foreach($categories as $cat)
					new SlimSelect({
					  select: '#slim-select-category-{!! $cat->id !!}',
					  placeholder: '{{ addslashes($cat->name) }}'
					})
				@endforeach
				$(".slim-select").css('opacity', 100);
			}

			$(document).ready(function() {


				$('#find-constituents form').fadeIn();
				
			  	// MODAL

				// $("#search_name").keyup(function(event) {
				//     if (event.keyCode === 13) {
				//         $("#save_search_button").click();
				//     }
				// });

				$('#save-search').on('shown.bs.modal', function () {
		    		$('#search_name').focus();
				})  

			});
		});

	</script>

@endsection