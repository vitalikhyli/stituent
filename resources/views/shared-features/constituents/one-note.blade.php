<div class="group cursor-pointer pt-1 text-grey-dark">

	<div class="flex w-full items-top">


		<div class="w-1/6 font-bold text-sm">

			{{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}

			<div class="text-grey-dark text-xs">
				@if(substr($thecontact->date,-8) != '00:00:00')
					{{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
				@endif
			</div>

		</div>


		<div class="flex-1 flex-initial w-5/6 px-2">
				

			@if(!$thecontact->private)

				@if($thecontact->case)

					<div class="float-right text-right">
						<a data-toggle="tooltip" data-placement="top" title="Go to Case: {{ $thecontact->case->subject }}" href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecontact->case_id }}" class="border shadow text-blue text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1"><i class="fas fa-folder-open"></i></a>
					</div>

				@else

					<div class="float-right text-right">
						<a data-toggle="tooltip" data-placement="top" title="Contact to Case" href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecontact->id }}/convert_to_case/{{ $person->id }}" class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1"><i class="fas fa-plus"></i> <i class="fas fa-folder-open"></i></a>
					</div>

				@endif

			@else
				<div data-toggle="tooltip" data-placement="top" title="Note is private" class="float-right text-right border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1"">
					<i class="fas fa-lock"></i>
				</div>
			@endif

            <div class="float-right w-12 ml-2">
                <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/contacts/{{ $thecontact->id }}/edit">
                <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                    Edit
                </button>
                </a>
            </div>

            
			

			@if($thecontact->type || $thecontact->type)

				<span class="font-bold uppercase text-sm">
					@if($thecontact->type)
						<span class="text-blue">
			            	{{ $thecontact->type }}
			            </span>
			        @endif

					@if($thecontact->subject)
						<span class="">
			            	{{ $thecontact->subject }}
			            </span>
			        @endif
		    	</span>
		    	
	        @endif


			<div class="text-xs">
				{!! nl2br($thecontact->notesRegex) !!}
			</div>
			@if($thecontact->user)
				<div class="mr-1 text-xs italic font-bold">
					Logged by {{ $thecontact->user->first_name }} {{ substr($thecontact->user->last_name, 0, 1) }}.
				</div>
			@endif

			<div class="w-full flex text-left mt-2">

				<div class="flex-1 flex-initial leading-loose">
					@if($thecontact->people->count() > 1)
						@foreach($thecontact->people as $theperson)

							@if(!$theperson->is_household)

								<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}" class="text-grey-dark">
									<span class="{{ ($thecontact->resolved) ? 'line-through bg-grey-lightest' : 'bg-grey-lighter' }}  hover:bg-blue hover:text-white rounded-lg mr-2 px-1 py-1 text-sm whitespace-no-wrap">
										@if($theperson->is_household)
											{{ $theperson->addressNoCity }}
										@else
											{{ $theperson->full_name }}
										@endif
									</span>
								</a>

							@else

								<a href="/{{ Auth::user()->team->app_type }}/households/{{ $theperson->id }}" class="text-grey-dark">
									<span class="{{ ($thecontact->resolved) ? 'line-through bg-grey-lightest' : 'bg-grey-lighter' }}  hover:bg-blue hover:text-white rounded-lg mr-2 px-1 py-1 text-sm whitespace-no-wrap">
										<i class="fa fa-home mr-1"></i>{{ $theperson->addressNoCity }}
									</span>
								</a>

							@endif

						@endforeach
					@endif
				</div>
			</div>

			

			@if($thecontact->followup)
				<div id="followup_{{ $thecontact->id }}" class="{{ ($thecontact->followup_done) ? 'bg-grey-lighter text-grey' : 'bg-orange-lightest' }} p-2 border-b-2 mb-2 w-full text-sm uppercase">

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



	</div>


	


</div>


