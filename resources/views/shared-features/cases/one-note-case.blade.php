
<div class="flex w-full text-sm border-b">
	<div class="w-1/5 py-3 relative">

		<div class="px-2 font-medium text-right">
			<div class="text-sm font-bold">
				<div class="px-2 py-1 rounded-full 
					@if ($thecontact->user->team_id != Auth::user()->team->id)
						bg-orange-lightest
					@else 
						bg-blue-lighter text-black
					@endif
					ml-2 font-bold text-center"
					title="{{ $thecontact->user->team->name }}">
					{{ $thecontact->user->name }}
				</div>
			</div>

			<div class="text-grey font-normal">
	        	{{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}
				@if(substr($thecontact->date,-8) != '00:00:00')
					at {{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
				@endif
			</div>

			<div class="text-grey font-bold">
				{{ \Carbon\Carbon::parse($thecontact->date)->diffForHumans() }}
			</div>

			
			<div class="text-sm text-grey">
				@if ($thecontact->private)
					<i class="fa fa-lock text-blue mr-1" alt="This note is private."></i>
				@endif 
				<!-- @if ($thecontact->user)
					{{ $thecontact->user->first_name }} {{ substr($thecontact->user->last_name, 0, 1) }}.
				@endif -->
			</div>
		</div>
	</div>
	<div class="w-3/5 pl-6 py-3">
		<div class="float-right mr-2">
			
			@if ($thecontact->user->team_id == Auth::user()->team->id)
				<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/contacts/{{ $thecontact->id }}/edit">
		            <button type="button"
		            		class="text-blue-light text-xs hover:text-blue">
		                Edit
		            </button>
	            </a>
			@endif
                         
        </div>

        @if($thecontact->subject || $thecontact->type)

			<div class="font-medium text-black mb-2 -ml-1">

            	@if($thecontact->type)
					<span class="text-blue uppercase text-xs bg-grey-lightest border px-2 py-1 font-normal rounded-t">
		            	{{ $thecontact->type }}
		            </span>
		        @endif

				@if($thecontact->subject)
            		<span class="px-2 py-1 border-b">{{ $thecontact->subject }}</span>
            	@endif

            </div>

        @endif
        
		{!! nl2br($thecontact->notes) !!}

		@if($thecontact->followup)
			<div id="followup_{{ $thecontact->id }}" class="{{ ($thecontact->followup_done) ? 'bg-grey-lighter text-grey' : 'bg-red-lightest' }} p-2 border-red m-2 text-sm uppercase">

				<span id="followup_{{ $thecontact->id }}_done" class="{{ (!$thecontact->followup_done) ? 'hidden' : '' }}">
					Follow up done
				</span>

				<span id="followup_{{ $thecontact->id }}_pending" class="{{ ($thecontact->followup_done) ? 'hidden' : '' }}">
					<i class="fas fa-star"></i>

					Follow up

					@if($thecontact->followup_on)
						on {{ $thecontact->followup_on }}
					@endif
				</span>

				<label for="{{ $thecontact->id }}" class="float-right font-normal px-2">
					<input type="checkbox" data-id="{{ $thecontact->id }}" class="contact_followup" id="{{ $thecontact->id }}" name="{{ $thecontact->id }}" value="1" {{ ($thecontact->followup_done) ? 'checked' : '' }} /> Done
				</label>

			</div>
		@endif

		

	</div>
	<div class="w-1/5 border-l bg-grey-lightest p-3">
		<div class="flex text-sm">


			<div class="w-full" wire:key="connector_contact_{{ $thecontact->id }}">

				@if ($thecontact->user->team_id == Auth::user()->team->id)
					@livewire('connector', [
										'class' => 'App\Person',
										'model' => $thecontact,
										'show_linked' => true,
										'search_goes_at_the_end' => true,
										'details' => false
										])
					@foreach ($thecontact->people as $person)
						@if ($person->is_household)
							<div>{{ $person->name }}</div>
						@endif
					@endforeach
				@else
					@foreach ($thecontact->people as $person)
						<div>{{ $person->name }}</div>
					@endforeach
				@endif

			</div>

		</div>
	</div>
</div>
