@extends('campaign.base')

@section('title')
    Invitees
@endsection

@section('main')

<div class="w-full">


	<div class="border-b-4 border-blue text-xl pb-4">


		<a href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}">{{ $event->name }}</a>:
		{{ $invitees->count() }} {{ Str::plural('Invitee', $invitees->count()) }}
		/
		<span id="attendance">{{ $num_guests }}</span> Attending
		
		<a href="/{{ Auth::user()->team->app_type }}/events">
			<button type="button" class="btn btn-default float-right">
				See all Events
			</button>
		</a>

		<a href="/campaign/events/{{ $event->id }}">
			<button type="button" class="btn btn-default float-right mr-4">
				View Event
			</button>
		</a>

	</div>


	<div class="bg-blue mt-2 text-center py-2 shadow">

		<input name="lookup" id="lookup" type="text" class="w-3/4 border border-blue-dark rounded-lg p-2" placeholder="Voter Look Up" />

		<div class="hidden w-full" id="list">
		</div>

	</div>


	<div class="table w-full text-sm border-t" id="invite-table">

			<div class="table-row text-sm uppercase" id="invite-table-header">

				<div class="table-cell p-1 border-b bg-grey-lighter">
					<!-- Edit -->
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					<!-- Delete -->
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Name
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					City
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Can Attend?
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					# Guests
				</div>

				<div class="table-cell p-1 border-b bg-grey-lighter">
					Comped?
				</div>

			</div>

		@foreach($invitees as $guest)

			<div class="table-row" id="invite-table-row-{{ $loop->iteration }}">

				<div class="table-cell p-1 border-b text-grey text-right pr-2">
					{{ $loop->iteration }}.
				</div>


				<div class="table-cell p-1 border-b text-grey pr-2 text-center">
					<a title="Remove Person" data-toggle="tooltip" data-placement="top" href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/remove_invitation/{{ $guest->id }}"><i class="fas fa-times text-red opacity-75"></i></a>
				</div>

				<div class="table-cell p-1 border-b text-blue">
					<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $guest->id }}/edit">{{ $guest->full_name }}</a>
				</div>


				<div class="table-cell p-1 border-b text-grey-dark">
					{{ $guest->city_state }}
				</div>

				<div data-id="{{ $guest->id }}" data-field="can_attend" class="edit-tof table-cell p-1 border-b cursor-pointer text-grey">
					<div id="edit-tof-can_attend-{{ $guest->id }}">
						@if($guest->pivot->can_attend)
							<i class="fas fa-check-circle text-blue"></i>
						@else
							--
						@endif
					</div>
				</div>

				<div data-id="{{ $guest->id }}" class="edit-guest table-cell p-1 border-b text-grey-dark cursor-pointer">
					<span id="edit-guest-display-{{ $guest->id }}">
						{{ (!$guest->pivot->guests) ? 0 : $guest->pivot->guests }}
					</span>
					<input type="text" class="edit-guest-input hidden bg-orange-lightest border w-16 p-1" id="edit-guest-input-{{ $guest->id }}" value="{{ (!$guest->pivot->guests) ? 0 : $guest->pivot->guests }}" />
				</div>

				<div data-id="{{ $guest->id }}" data-field="comped" class="edit-tof table-cell p-1 border-b cursor-pointer text-grey">
					<div id="edit-tof-comped-{{ $guest->id }}">
						@if($guest->pivot->comped)
							<i class="fas fa-check-circle text-blue"></i>
						@else
							--
						@endif
					</div>
				</div>


			</div>



		@endforeach


	</div>


</div>


<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('/{{ Auth::user()->team->app_type }}/participants/lookup/invite/'+v+'/'+event_id, function(response) {

			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}

	function prependParticipant(id) {
		// alert('/{{ Auth::user()->team->app_type }}/events/'+event_id+'/add_invitation/'+id);
		$.get('/{{ Auth::user()->team->app_type }}/events/'+event_id+'/add_invitation/'+id, function(new_row) {
			if (new_row != '') {
				$("#invite-table-header").after(new_row);
			}
		});		
	}

	function updateGuestCount(id, new_data) {
		$.get('/{{ Auth::user()->team->app_type }}/events/'+event_id+'/guest_count/'+id+'/'+new_data, function(response) {

			response = response.trim();

			if (response == 'error' || response == '') {

				$('#edit-guest-input-'+id).removeClass('bg-orange-lightest');
				$('#edit-guest-input-'+id).addClass('bg-red-light text-white');
				$('#edit-guest-input-'+id).select();

			} else {

		    	$('#edit-guest-display-'+id).html(response);

		    	if($('#edit-guest-display-'+id).hasClass('hidden')) {
		   			$('#edit-guest-input-'+id).addClass('hidden');
		   			$('#edit-guest-display-'+id).removeClass('hidden')
		   		}

		   		updateTotalGuestCount();
			}
		});		
	}

	function updateTOF(id, field) {
		$.get('/{{ Auth::user()->team->app_type }}/events/'+event_id+'/tof/'+id+'/'+field, function(response) {

			response = response.trim();

			if (response == 'error' || response == '') {
				//
			} else if (response == 'true') {

		    	$('#edit-tof-'+field+'-'+id).html('<i class="fas fa-check-circle text-blue"></i>');
		    	updateTotalGuestCount();

			} else if (response == 'false') {

		    	$('#edit-tof-'+field+'-'+id).html('--');
		    	updateTotalGuestCount();
			}
		});		
	}

	function updateTotalGuestCount() {
		$.get('/{{ Auth::user()->team->app_type }}/events/'+event_id+'/total_guests', function(response) {

			response = response.trim();

			if (response == 'error' || response == '') {
				//
			} else {

		    	$('#attendance').html(response);
			}
		});		
	}

	$(document).ready(function() {

		event_id = {!! $event->id !!};
		
		$("#lookup").focusout(function(){
			window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
		});
		
		$("#lookup").keyup(function(){
			getSearchData(this.value);
		});

	   	$(document).on('click', ".participant-select", function () {
	   		prependParticipant($(this).data("id"));
			$("#lookup").select();
			$("#lookup").focus();
			getSearchData($("#lookup").value);
	    });

	    ////////////////// AJAX INLINE EDIT GUESTS

	    $(document).on('click', ".edit-guest", function () {
	   		id = $(this).data("id");

			$('#edit-guest-input-'+id).removeClass('bg-red-light text-white');
			$('#edit-guest-input-'+id).addClass('bg-orange-lightest');

	   		$('#edit-guest-input-'+id).toggleClass('hidden');
	   		$('#edit-guest-display-'+id).toggleClass('hidden');
	   		$('#edit-guest-input-'+id).select();
	    });

	    $(document).on('blur', ".edit-guest-input", function () {
	    	id = $(this).parent().data("id");
	    	new_data = $('#edit-guest-input-'+id).val();
	    	updateGuestCount(id, new_data);
	    });

		$('.edit-guest-input').keypress(function(event){
		    var keycode = (event.keyCode ? event.keyCode : event.which);
		    if(keycode == '13'){
		        $(this).blur();
		    }
		});

	    ////////////////// AJAX INLINE TRUE OR FALSE

	    $(document).on('click', ".edit-tof", function () {
	    	id = $(this).data("id");
	    	field = $(this).data("field");
	    	updateTOF(id, field);
	    });

	});

</script>
@endsection