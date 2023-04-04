<div class="group cursor-pointer py-2 pb-4 {{ (!$thecontact->followup) ? 'border-b' : '' }}">

	<div class="flex pb-1 w-full">

		<div class="flex-1 flex-initial px-2 w-1/6 text-sm font-medium text-right">
			<div>
				{{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}
			</div>
			<div class="text-grey-dark text-sm">
				@if(substr($thecontact->date,-8) != '00:00:00')
					{{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
				@endif
			</div>
			<div class="text-sm text-grey">
				@if ($thecontact->private)
					<i class="fa fa-lock text-blue mr-1" alt="This note is private."></i>
				@endif 
				@if ($thecontact->user)
					{{ $thecontact->user->first_name }} {{ substr($thecontact->user->last_name, 0, 1) }}.
				@endif
			</div>
		</div>

		<div class="flex-1 flex-initial px-2 w-5/6">

            <div class="float-right w-12 ml-2">
                <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/contacts/{{ $thecontact->id }}/edit">
                <button type="button"
                		class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                    Edit
                </button>
                </a>             
            </div>

			@if($thecontact->type || $thecontact->subject)

				<span class="font-bold uppercase text-sm">
					@if($thecontact->type)
						<span class="text-blue">
			            	{{ $thecontact->type }}
			            </span>
			        @endif

					@if($thecontact->type && $thecontact->subject)
						<span class="text-blue">
			            	> 
			            </span>
					@endif

					@if($thecontact->subject)
						<span class="">
			            	{{ $thecontact->subject }} >
			            </span>
			        @endif
		    	</span>
		    	
	        @endif

			{!! nl2br($thecontact->notes) !!}

			<div class="flex text-sm mt-2">


				<div class="" wire:key="connector_contact_{{ $thecontact->id }}">

					@livewire('connector', [
											'class' => 'App\Person',
											'model' => $thecontact,
											'show_linked' => true,
											'search_goes_at_the_end' => true
											])

				</div>

			</div>

		</div>

	</div>



	@if($thecontact->followup)
		<div id="followup_{{ $thecontact->id }}" class="{{ ($thecontact->followup_done) ? 'bg-grey-lighter text-grey' : 'bg-orange-lightest' }} p-2 border-b-2 border-red mt-2 w-full text-sm uppercase">

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

