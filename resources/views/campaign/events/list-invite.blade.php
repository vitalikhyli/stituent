@foreach($participants as $participant)

	@if(!$participant->invited)

		<div data-id="{{ $participant->id }}" data-first_name="{{ $participant->first_name }}" data-last_name="{{ $participant->last_name }}" data-street="{{ $participant->address_line_street }}" data-city="{{ $participant->address_city }}" data-state="{{ $participant->address_state }}" data-zip="{{ $participant->address_zip }}" class="participant-select text-right flex text-grey-lightest p-1 border-b border-blue-light cursor-pointer px-2 {{ ($participant->invited) ? 'opacity-25' : '' }} hover:bg-blue-dark">

			
			<button type="button" class="rounded-lg bg-blue-700 px-2 py-1 bg-grey-lightest text-grey-dark text-xs uppercase mr-2">
				Invite
			</button>
		
			<div class="">
				{{ $participant->full_name }}
			</div>

			<div class=" text-right text-xs flex-grow pt-1">
				{{ $participant->full_address }}
			</div>

		</div>

	@else

		<div data-id="{{ $participant->id }}" class="text-right flex text-grey-lightest p-1 border-b border-blue-light cursor-pointer px-2 opacity-50 bg-grey-darker hover:bg-grey-darkest">
			
			<button type="button" class="rounded-lg bg-blue-700 px-2 py-1 bg-grey-darker text-white text-grey-dark text-xs uppercase mr-2">
				Invited
			</button>

			<div class="">
				{{ $participant->full_name }}
			</div>

			<div class=" text-right text-xs flex-grow pt-1">
				{{ $participant->full_address }}
			</div>

		</div>

	@endif

@endforeach