@extends('campaign.base')

@section('title')
    Contributions
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Contributions</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Contributions
		<span class="text-blue">*</span>
		<span class="text-xl text-grey-dark pl-2 float-right m-2">
			{{ $donations->count() }}
			 {{ Str::plural('Contribution', $donations->count()) }} / 
			${{ number_format($donations->sum('amount'),2,'.',',') }}
		</span>
		<!-- <span class="text-blue">*</span> -->
	</div>

	<div class="flex">
		<div class="w-2/3">
			<div class="text-center m-8">
				<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark" data-toggle="modal" data-target="#add-donation">
						Add a Contribution
				</button>
			</div>
		</div>
		<div class="w-1/3 text-grey-dark">
			
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Log your <span class="font-bold text-black">Contributions</span> to keep careful track of your campaign finances and link them directly to the voter files. 
				<br>
				<br>
				We auto-fill the voter information you need, error-check your donations, and prepare <b>OCPF</b> formatted exports for your campaign finance reporting. 
			</div>
		</div>
	</div>

<div class="w-full mt-8">


	@if(!$donations->first())

		<div class="text-grey-dark">
			No contributions to display yet.
		</div>

	@else

		<div class="text-xl">

			<div class="float-right">

				<a href="/{{ Auth::user()->team->app_type }}/donations/export{{ (request()->input('event_id')) ? '?event_id='.request()->input('event_id') : '' }}">
					<button class="rounded-lg bg-grey-lighter text-blue p-2 text-sm uppercase px-4">
						<i class="fas fa-file-csv mr-2"></i> Export
					</button>
				</a>

			</div>

		</div>


		@if(!$selected_event)

			<div class="text-base py-2 px-4 bg-blue-lighter text-blue-darker mb-2 inline-block w-full rounded-b-lg">

				<div class="float-left">
					<form action="/{{ Auth::user()->team->app_type }}/donations/filter" method="POST">
					    @csrf
						<div class="flex">
							<div class="mr-2">
								Filter from:
								<input autocomplete="off" name="donations_filter_start" size="14" type="text" class="datepicker border border-grey rounded-lg p-2 text-black" value="{{ Auth::user()->getMemory('donations_filter_start') }}" />
							</div>
							<div class="mr-2">
								to:
								<input autocomplete="off" name="donations_filter_end" size="14" type="text" class="datepicker border border-grey rounded-lg p-2 text-black" value="{{ Auth::user()->getMemory('donations_filter_end') }}" />
							</div>
							<button type="submit" class="rounded-lg bg-blue text-white px-2 py-1 my-1 border">
								Set Filter
							</button>
						</div>
					</form>
				</div>

				<div class="float-right">
					<form action="/{{ Auth::user()->team->app_type }}/donations/search" method="POST">
					    @csrf
						<div class="flex">
							<div class="mr-2">
								<input name="search" size="14" type="text" class="border border-grey rounded-lg p-2 text-black" value="{{ isset($search) ? $search : null }}" placeholder="Search" />
							</div>
							<button type="submit" class="rounded-lg bg-blue text-white px-2 py-1 my-1 border">
								Go
							</button>
						</div>
					</form>
				</div>

			</div>

		@else
			<div class="text-xl py-2">

				<span class="bg-grey-dark text-white rounded-lg px-2 py-1 mr-2 text-base">Event</span>

				{{ $selected_event->name }}

				<span class="text-base ml-2">
					<a href="/{{ Auth::user()->team->app_type }}/donations">
						<i class="fas fa-times-circle"></i>
						Show All Donations
					</a>
				</span>

			</div>
		@endif


		<div class="table w-full text-sm border-t">

			<div class="table-row text-sm uppercase">

				<div class="table-cell p-1 border-b bg-grey-lighter">
					<!-- Edit -->
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Date
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Event
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter text-right">
					Amount
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter text-right">
					Fee
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Name
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Occupation / Employer
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter text-center">
					Notes
				</div>

			</div>

			@foreach($donations as $donation)

				<div class="table-row">

					<div class="table-cell p-1 border-b">
						<a href="/{{ Auth::user()->team->app_type }}/donations/{{ $donation->id }}/edit">
						<button class="text-xs rounded-lg bg-blue text-white px-2 py-1 uppercase">
							Edit
						</button>
						</a>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark">
						{{ $donation->date_formatted }}
					</div>

					<div class="table-cell p-1 border-b">
						@if($donation->event)
							<a href="?event_id={{ $donation->event->id }}">
								{{ mb_strimwidth($donation->event->name, 0, 20, "...") }}
							</a>
						@endif
					</div>

					<div class="table-cell p-1 border-b text-right">
						{{ number_format($donation->amount,2,'.',',') }}
					</div>

					<div class="table-cell p-1 border-b text-right">
						<span class="text-red">{{ number_format($donation->fee,2,'.',',') }}</span>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark">
						<a href="/campaign/participants/{{ $donation->participant_id }}">
							{!! preg_replace("/".preg_quote($search)."/i", '<b class="bg-orange-lighter">$0</b>', $donation->full_name) !!}
						</a>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark">

						{!! preg_replace("/".preg_quote($search)."/i", '<b class="bg-orange-lighter">$0</b>', $donation->occupation_employer ) !!}

						<div id="notes-div-{{ $donation->id }}" data-id="{{ $donation->id }}" class="notes-div border bg-yellow-lighter text-black text-left w-full p-2 hidden z-50 absolute shadow mt-6" style="transition:2s;width:300px;">
							<span class="font-bold">Notes:</span> 

							{!! preg_replace("/".preg_quote($search)."/i", '<b class="bg-orange-lighter">$0</b>', $donation->notes ) !!}

						</div>

					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-center">
						@if($donation->notes)
							<i class="fas fa-info-circle text-blue-dark cursor-pointer" data-id="{{ $donation->id }}" ></i>
						@endif
					</div>



				</div>



			@endforeach


		</div>

	@endif


</div>

<!---------------------- ADD DONATION MODAL ---------------------->

	@include('campaign.donations.modal-add')
		
<!-------------------------- END MODALS -------------------------->


<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('/{{ Auth::user()->team->app_type }}/participants/lookup/donations/'+v, function(response) {

			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}

	$(document).ready(function() {

			$("#lookup").focusout(function(){
				window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
			});
			
			$("#lookup").keyup(function(){
				getSearchData(this.value);
			});


		   	$(document).on('click', ".participant-select", function () {
		   		$( "input[name='first_name']").val($(this).data("first_name"));
		   		$( "input[name='last_name']").val($(this).data("last_name"));
		   		$( "input[name='street']").val($(this).data("street"));
		   		$( "input[name='city']").val($(this).data("city"));
		   		$( "input[name='state']").val($(this).data("state"));
		   		$( "input[name='zip']").val($(this).data("zip"));
				$( "input[name='the_id']").val($(this).data("id"));
				$("#lookup").select();
		    });

		    $(document).on('click', ".fa-info-circle", function () {
		   		id = $(this).data("id");
		 		$(".notes-div").each( function() {
		 			if ($(this).data("id") != id) {
		 				$(this).addClass('hidden');
		 			}
				
		 		});
				$("#notes-div-"+id).toggleClass('hidden');
		    });

		   	$(document).on('click', "#add-donation-button", function () {
				$("#lookup").select();
		    });
	});

</script>
@endsection