@extends('campaign.base')

@section('title')
    Campaign Events
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Events</b>
@endsection

@section('main')

<div class="text-3xl font-bold border-b-4 pb-2">
	Campaign Events
	<span class="text-blue">*</span>
</div>

<div class="flex">
	<div class="w-2/3">
		<div class="text-center m-8">
			<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark" data-toggle="modal" data-target="#new-event">
					Create a New Event
				</button>
		</div>
	</div>
	<div class="w-1/3 text-grey-dark">
		
		<div class="p-2">
			<span class="text-blue text-2xl font-bold">*</span> Create <span class="font-bold text-black">Campaign Events</span> to quickly organize your fundraisers, invites, and donations.
		</div>
	</div>
</div>

<div class="w-full mt-8">


	


	@if($events->first())

		<div class="border-b text-xl pb-2">

			{{ $events->count() }} {{ Str::plural('Event', $events->count()) }}
			
		</div>

		<div class="table w-full text-sm border-t">
			
				<div class="table-row text-sm uppercase">

					<div class="table-cell p-1 border-b bg-grey-lighter">
						<!-- Edit -->
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter">
						Date
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter">
						Name
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter">
						Notes
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter">
						Venue
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter text-right">
						# Attending
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter text-right">
						# Donations
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter text-right">
						<!-- <a href="?sort_by_raised=true"> -->
							Total
						<!-- </a> -->
					</div>

					<div class="table-cell p-1 border-b bg-grey-lighter text-right">
						Average
					</div>


				</div>

			@foreach($events as $event)

				<div class="table-row">

					<div class="table-cell p-1 border-b">

						<a href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/edit">
							<button class="text-xs rounded-lg bg-blue text-white px-2 py-1 uppercase">
								Edit
							</button>
						</a>

						<a href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/guests">
							<button class="text-xs rounded-lg bg-orange-dark text-white px-2 py-1 uppercase">
								Guests
							</button>
						</a>

					</div>

					<div class="table-cell p-1 border-b text-grey-dark">
						{{ $event->date_formatted }}
					</div>

					<div class="table-cell p-1 border-b text-grey-dark">
						<a class="font-bold text-blue-500" href="/campaign/events/{{ $event->id }}">
							{{ $event->name }}
						</a>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-center">
						{{ $event->campaignEventNotes()->count() }}
					</div>

					<div class="table-cell p-1 border-b text-grey-dark">
						{{ $event->full_venue }}
					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-right">
						{{ $event->total_attending }}
					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-right">
						<a href="/{{ Auth::user()->team->app_type }}/donations?event_id={{ $event->id }}">
							{{ $event->donations()->count() }}
						</a>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-right">
						<a href="/{{ Auth::user()->team->app_type }}/donations?event_id={{ $event->id }}">
							${{ number_format($event->donations()->sum('amount'),2,'.',',') }}
						</a>
					</div>

					<div class="table-cell p-1 border-b text-grey-dark text-right">
						<a href="/{{ Auth::user()->team->app_type }}/donations?event_id={{ $event->id }}">
							${{ number_format($event->donations()->average('amount'),2,'.',',') }}
						</a>
					</div>

				</div>



			@endforeach


		</div>

	@else 

		

	@endif


</div>

<!---------------------------- ADD EVENT MODAL ---------------------------->

	<div id="new-event" class="modal fade" role="dialog">

		<form id="new-event-form" action="/{{ Auth::user()->team->app_type }}/events/" method="POST">
         	@csrf

			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">

					<div class="modal-header bg-blue-dark text-white">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add an Event</h4>
					</div>

					<!-- Modal body-->

					<div class="p-1">
						
						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								Date
							</div>
							<div class="p-2">
								<input required name="date" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
							</div>
						</div>

						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								Name
							</div>
							<div class="p-2 w-3/4">
								<input required name="name" type="text" class="w-full border rounded-lg p-2" placeholder="Event Name" />
							</div>
						</div>

					</div>

					<!-- Modal footer -->

					<div class="modal-footer bg-grey-light text-white">

						<button type="button" class="btn btn-default" data-dismiss="modal">
							Close
						</button>

						<button type="submit" class="btn btn-primary">
							Save
						</button>

					</div>
				</div>

			</div>

		</form>
	</div>



    <script type="text/javascript">
    	$('.datepicker').datepicker(); //Need this here because modal
    </script>
		
	<!-------------------------- END MODALS -------------------------->


<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {

      $(document).on('click', ".fa-info-circle", function () {

       		id = $(this).data("id");

	 		$(".notes-div").each( function() {
	 			if ($(this).data("id") != id) {
	 				$(this).addClass('hidden');
	 			}
				
	 		});

			$("#notes-div-"+id).toggleClass('hidden');
	 		

      });


	});

</script>
@endsection