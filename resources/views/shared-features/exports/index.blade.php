@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Exports')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>Exports</b> 

@endsection

@section('style')

	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.css" rel="stylesheet"></link>

	<style>

		/* The switch - the box around the slider */
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 50px;
		  height: 25px;
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
		  height: 17px;
		  width: 17px;
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

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		 @lang('Export') @lang('Constituents')

	</div>

	<div class="flex float-right">

	</div>

</div>

<form class="px-2" id="constituents-list-form" action="{{ (isset($thesearch)) ? '/'.Auth::user()->team->app_type.'/exports' : '' }}" method="GET" autocomplete="off" autocomplete="false">

	<div class="flex w-full">

		<div class="w-1/3">

			<div id="find-constituents" style="min-height:600px; transition: .3s;" class="mt-4 pb-4 shadow mr-4 bg-white border-l-2  border-r-2 border-b-2 border-blue-light">

				@include('shared-features.exports.query-form')

			</div>

			<div id="saved-searches" style="transition: .3s;" class="mt-4 shadow mr-4 bg-white border-2 border-blue-light">

				@if (Auth::user()->searches()->first())

					<div class="bg-blue-light text-white py-2 px-2 font-bold flex">

						<div class="flex-grow py-1">
							My Saved Searches
						</div>

<!-- 						<a href="/{{ Auth::user()->team->app_type }}/constituents/searches/">
							<div class="w-18 font-normal t bg-grey-lightest text-sm px-2 py-1 rounded-lg">
								See All
							</div>
						</a> -->
					

						
					</div>
				
					<div class="p-2">
						@foreach (Auth::user()->searches->sortByDesc('updated_at') as $search)
							<a href="/{{ Auth::user()->team->app_type }}/exports/{{ $search->id }}">
								<div class="saved-search flex w-full {{ (!$loop->last) ? 'border-b' : '' }} text-sm py-1 cursor-pointer" data-id="{{ $search->id }}">
									<div class="flex-1 ">
										{{ $search->name }}
									</div>
									<div class="uppercase text-xs flex-1 text-right w-8">
										Use
									</div>
								</div>
							</a>
						@endforeach
					</div>

				@else

					<div class="text-grey-dark italic p-4 text-center">You have not saved any searches yet.</div>

				@endif
				

			</div>


		
		</div>

		<div class="w-2/3 pl-4">

			<div class="mt-4 w-full">

				<div id="constituents-list" form="{{ base64_encode(serialize($input)) }}">

					@if($total_count <= 0)
						<div class="text-center">

							@isset($search_value)
								<div class="p-2 text-grey-dark">
									Search found nobody
								</div>
							@endisset

						</div>
					@endif

					<div class="font-bold w-full">
						<span class="font-bold text-3xl pl-2">{{ number_format($total_count) }}</span> 
						@if($total_count == 1)
							@lang('Constituent')
						@else
							@lang('Constituents')
						@endif
					</div>

					@include('shared-features.exports.fields-form')


				</form>

				</div>

		@if(isset($input['fields']))
		<div class="w-full mt-2">

			<form action="/{{ Auth::user()->team->app_type }}/exports/download" method="post">

				<input type="hidden" name="search_form" value="{{ base64_encode(serialize($input)) }}" />

				<div class="p-2 border-b">

					<div class="font-semibold mb-2">File Name:</div>
					@isset ($thesearch)
						<input type="text" class="rounded-lg border border-grey-darker px-4 py-2 w-full" name="file_name" value="{{ str_replace(' ', '-', $thesearch->name.'-'.\Carbon\Carbon::now()->format("Y-m-d").'.csv') }}">
					@else
						<input type="text" class="rounded-lg border border-grey-darker px-4 py-2 w-full" name="file_name" value="{{ str_replace(' ', '-', 'Constituent-Export-'.\Carbon\Carbon::now()->format("Y-m-d").'.csv') }}">
					@endisset

				</div>

				<div class="p-4 text-center bg-grey-lightest">

					@csrf

					<button type="submit" method="post" formaction="/{{ Auth::user()->team->app_type }}/exports/download" class="px-4 py-2 bg-blue hover:bg-blue-dark text-white rounded-lg shadow">
						Start Export
					</button>

				</div>

			</form>

		</div>
		@endif


			</div>

		</div>
	</div>





@endsection

@section('javascript')


<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script>

<script type="text/javascript">

	function updateList() {

		// var fields_array = [];
		// fields = $('.field').each(function () {
		// 	if ($(this).prop("checked")) {
		// 		field_name = $(this).prop("value");
		// 		fields_array.push(field_name);
		// 	}
		// });
		// $('#constituents-list-form-fields').val(fields_array);

		$('#constituents-list').css('opacity', '0.5');
		$('#find-constituents').css('opacity', '0.5');
		$('#constituents-list-form').submit();
		
		return;
	}



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
		new SlimSelect({
		  select: '#slim-select-has_received_emails',
		  placeholder: 'Has Received Emails'
		})
		$(".slim-select").css('opacity', 100);
	}

	createSlimSelects();

	$(document).ready(function() {

		$('[query_form="true"]').keyup(delay(function (e) {
			updateList();
		}, 1000));

		$(document).on('change', '[query_form="true"]', function() {
			updateList();
	  	});

		$("#householding").click(function() {

			if(this.checked) {

				householding_fields = ['full_name', 'mailing_address', 'full_address','address_number', 'address_fraction', 'address_street', 'address_apt','address_city','address_state','address_zip'];

				$('.field-selector input:checkbox').not(this).prop('checked', false);

				$.each(householding_fields, function( key, value ) {
				  $('#field_'+value).prop('checked', true);
				});

			}
	    	
	    });

		$("#all_fields").click(function() {
	    	$('.field-selector input:checkbox').not(this).prop('checked', this.checked);
	    });

		$('#find-constituents form').fadeIn();
		
		// Update Button

		$("input, select").not("#save-search input").on('change keyup paste select', function(){
			$("#update-search").removeClass('hidden');
		});

		$("#slim-select-municipality, #slim-select-zip, #slim-select-party").on('change', function(){
			$("#update-search").removeClass('hidden');
		});

		$("#update-search").click(function() {
	       $("input[name='search_name']").removeAttr('required');
	    });

	  	// MODAL

		$("#search_name").keyup(function(event) {
		    if (event.keyCode === 13) {
		        $("#save_search_button").click();
		    }
		});

		$('#save-search').on('shown.bs.modal', function () {
    		$('#search_name').focus();
		})  


	});


</script>
@endsection
