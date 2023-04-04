@extends('campaign.base')

@section('title')
    Edit Donation
@endsection

@section('style')


@endsection

@section('main')

<div class="w-full">



	<div class="border-b-4 border-blue text-xl pb-2">


	 <div class="float-right text-sm">
	      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2 -mt-2"/>
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Event
	      </button>
	  </div>

	  <div class="float-right text-sm">
	      <a href="/campaign/events/{{ $event->id }}" class="rounded-lg px-4 py-2 text-center ml-2"/>
	        <i class="mr-2"></i> View Event
	      </a>
	  </div>

		Edit Event

	</div>


<form action="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/update" method="post">

	@csrf

	<!-- Edit body-->

	<div class="p-1">
		
		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Date
			</div>
			<div class="p-2">
				<input autocomplete="off" name="date" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" value="{{ \Carbon\Carbon::parse($event->date)->format('m/d/Y') }}"  />
			</div>

			<div class="py-2">
				<input autocomplete="off" name="time" size="10" type="text" class="border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('g:i A') }}" value="{{ ($event->time) ? \Carbon\Carbon::parse($event->time)->format('g:i A') : '' }}"  />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Name
			</div>
			<div class="p-2 w-3/4">
				<input name="name" type="text" class="w-full border rounded-lg p-2" placeholder="Event Name" value="{{ $event->name }}" />
			</div>
		</div>

	</div>


	<!-- Edit Section -->

	<div class="p-1 border-t">

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Venue Name
			</div>
			<div class="p-2 w-3/4">
				<input name="venue_name" type="text" class="w-1/2 border rounded-lg p-2" placeholder="Name" value="{{ $event->venue_name }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Venue Street
			</div>
			<div class="p-2 w-3/4">
				<input name="venue_street" type="text" class="w-1/2 border rounded-lg p-2" placeholder="Street" value="{{ $event->venue_street }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Venue City
			</div>
			<div class="p-2 w-3/4">
				<input name="venue_city" type="text" class="w-1/3 border rounded-lg p-2" placeholder="City" value="{{ $event->venue_city }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Venue State
			</div>
			<div class="p-2 w-3/4">
				<input name="venue_state" type="text" class="w-12 border rounded-lg p-2" placeholder="{{ Auth::user()->team->account->state }}" value="{{ $event->venue_state }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Venue Zip
			</div>
			<div class="p-2 w-3/4">
				<input name="venue_zip" type="text" class="w-16 border rounded-lg p-2" placeholder="Zip" value="{{ $event->venue_zip }}" />
			</div>
		</div>

	</div>



  <div class="text-right pt-2 text-sm mt-1 border-t-4 border-grey-dark">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      </div>
      
  </div>

</form>


<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this event?
          </div>
          <div class="text-left font-bold py-2 text-base">
            This will delete the event.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {


	});

</script>
@endsection