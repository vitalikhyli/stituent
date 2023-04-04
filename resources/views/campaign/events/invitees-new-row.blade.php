<div class="table-row">

	<div class="bg-orange-lightest border-b-2 border-orange-light table-cell p-1 border-b  text-grey text-right pr-2">
		<!-- New -->
	</div>

	<div class="bg-orange-lightest border-b-2 border-orange-light table-cell p-1 border-b  text-grey text-center pr-2">
		
		<a title="Remove Person" data-toggle="tooltip" data-placement="top" href="/{{ Auth::user()->team->app_type }}/events/{{ $event->id }}/remove_invitation/{{ $participant->id }}"><i class="fas fa-times text-red opacity-75"></i></a>

	</div>

	<div class="bg-orange-lightest border-b-2 border-orange-light table-cell p-1 border-b text-blue">
		{{ $participant->full_name }}
	</div>

	<div class="bg-orange-lightest border-b-2 border-orange-light table-cell p-1 border-b text-blue">
		{{ $participant->city_state }}
	</div>

	<div data-id="{{ $participant->id }}" data-field="can_attend" class="edit-tof table-cell p-1 border-b border-orange-light bg-orange-lightest cursor-pointer text-grey">
		<div id="edit-tof-can_attend-{{ $participant->id }}">
			@if($participant->event($event->id)->can_attend)
				<i class="fas fa-check-circle text-blue"></i>
			@else
				--
			@endif
		</div>
	</div>

	<div data-id="{{ $participant->id }}" class="edit-guest table-cell p-1 border-b border-orange-light bg-orange-lightest text-grey-dark cursor-pointer">
		<span id="edit-guest-display-{{ $participant->id }}">
			{{ (!$participant->event($event->id)->guests) ? 0 : $participant->event($event->id)->guests }}
		</span>
		<input type="text" class="edit-guest-input hidden bg-orange-lightest border w-16 p-1" id="edit-guest-input-{{ $participant->id }}" value="{{ (!$participant->event($event->id)->guests) ? 0 : $participant->event($event->id)->guests }}" />
	</div>

	<div data-id="{{ $participant->id }}" data-field="comped" class="edit-tof table-cell p-1 border-b border-orange-light bg-orange-lightest cursor-pointer text-grey">
		<div id="edit-tof-comped-{{ $participant->id }}">
			@if($participant->event($event->id)->comped)
				<i class="fas fa-check-circle text-blue"></i>
			@else
				--
			@endif
		</div>
	</div>

</div>