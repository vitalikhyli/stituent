<div wire:key="email_link_{{ $thecall->id }}_{{ base64_encode($email) }}"
	 class="flex w-full">
			
	<div class="px-2 flex-grow text-left">
		{{ $email }}
	</div>

	<div class="flex-shrink text-right" wire:key="email_link_div_{{ $thecall->id }}_{{ base64_encode($email) }}">

		@if($thecall->people->first())

			@if($thecall->people()->where('primary_email', $email)->exists())

				<span class="text-blue font-bold">
					{{ $thecall->people()->where('primary_email', $email)->first()->full_name }} <i class="fas fa-check-circle"></i>
				</span>

			@elseif($thecall->people()
							 ->whereNull('primary_email')
							  ->first())

				<form wire:submit.prevent="linkEmail()">

					<select wire:model="person_id"
					wire:key="person_id_{{ $thecall->id }}_{{ base64_encode($email) }}">

							<option value="">
								-- None --
							</option>

						@foreach($thecall->people()
								 ->whereNull('primary_email')
								 ->get() as $theperson)

							<option value="{{ $theperson->id }}">
								{{ $theperson->full_name }}
							</option>

						@endforeach

					</select>

					{{ $person_id }}
				
					<button type="submit"
							class="rounded-lg text-xs uppercase bg-blue text-white px-2 py-1 {{ ($person_id) ? 'visible' : 'hidden' }}">Link</button>

				</form>
		<button class="rounded-lg bg-blue  text-white px-2 py-1" wire:click="$set('person_id', 45678)">this</button>
			@else

				<span class="text-grey">
					No options
				</span>

			@endif

		@endif

	</div>

</div>