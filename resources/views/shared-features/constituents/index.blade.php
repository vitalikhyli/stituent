@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>@lang('Constituents')</b> 

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

@section('main')

<div class="flex border-b-4 pb-2 border-blue">

	<div class="text-2xl font-sans">
		 @lang('Constituents')
	</div>

	<div class="flex-grow text-right">

		 @if(Auth::user()->permissions->developer)
		 	<a href="?livewire=true">
		 		<button class="text-base text-red hover:border-b-2 py-2 border-red">
		 			<i class="fas fa-hand-point-right text-lg mr-1"></i><span class="font-medium">NEW!</span>
		 			Try a <u>Faster</u> Constituent Search <span class="text-xs -mt-4">(BETA)</span>
		 		</button>
		 	</a>
		 @endif

	</div>

</div>

<div class="flex w-full">
	<div class="w-2/3">

		@if($people instanceof \Illuminate\Pagination\LengthAwarePaginator)
			<div class="w-full flex text-grey-dark">
				<div class="w-1/4 text-left p-2">
					Showing <span class="font-bold text-black">{{ $people->firstItem() }}-{{ $people->lastItem() }}</span> of 
					<span class="font-bold text-black">{{ $people->total() }}</span>
				</div>
				<div class="w-1/2 text-center">
					{{ $people->links() }}
				</div>
				<div class="w-1/4 text-right p-2">
					Page <span class="text-black font-bold">{{ $people->currentPage() }}</span> of <span class="text-black font-bold">{{ $people->lastPage() }}</span>
				</div>
			</div>
		@endif

		<div class="mt-4 w-full">

			@include('shared-features.constituents.list')
		</div>

	</div>
	<div class="w-1/3">

		<div id="find-constituents" style="min-height:500px; transition: .3s;" class="mt-4 pb-4 shadow ml-4 bg-white border-l-2  border-r-2 border-b-2 border-blue-light">

			@include('shared-features.constituents.query-form-constituents')

		</div>

		<div id="saved-searches" style="transition: .3s;" class="mt-4 shadow ml-4 bg-white border-2 border-blue-light">

			@if (Auth::user()->searches()->count() > 0)

				<div class="bg-blue-light text-white py-2 px-2 font-bold flex">

					<div class="flex-grow py-1">
						My Saved Searches
					</div>

<!-- 					<a href="/{{ Auth::user()->team->app_type }}/constituents/searches/">
						<div class="w-18 font-normal t bg-grey-lightest text-sm px-2 py-1 rounded-lg">
							See All
						</div>
					</a> -->
				

					
				</div>
			
				<div class="p-2">
					@foreach (Auth::user()->searches->sortByDesc('updated_at') as $search)
						<a href="/{{ Auth::user()->team->app_type }}/constituents/searches/{{ $search->id }}">
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
</div>

@endsection

@section('javascript')


<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script>

<script type="text/javascript">

	function updateList() {
		// var mode = '{{ (isset($mode_all)) ? 'constituents_all' : 'constituents' }}';
		$('#constituents-list').css('opacity', '0.5');
		$('#find-constituents').css('opacity', '0.5');
		$('#constituents-list-form').submit();
		return;
		// var form = $('#constituents-list-form');
		// $.get(form.attr("action"), form.serialize(), function(response) {
		// 	$('#constituents-list').replaceWith(response);
		// });
	}

	createSlimSelects();

	function createSlimSelects() {
		new SlimSelect({
		  select: '#slim-select-municipality',
		  placeholder: 'Municipalities'
		})
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
		  select: '#slim-select-party',
		  placeholder: 'Parties'
		})
		new SlimSelect({
		  select: '#slim-select-zip',
		  placeholder: 'Zip Codes'
		})
		@foreach($categories as $cat)
			new SlimSelect({
			  select: '#slim-select-category-{{ $cat->id }}',
			  placeholder: '{{ addslashes($cat->name) }}'
			})
		@endforeach
		new SlimSelect({
		  select: '#slim-select-has_received_emails',
		  placeholder: 'Has Received Emails'
		})
		$(".slim-select").css('opacity', 100);
	}

	$(document).ready(function() {

		$('[query_form="true"]').keyup(delay(function (e) {
			updateList();
		}, 1000));

		$(document).on('change', '[query_form="true"]', function() {
			updateList();
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
