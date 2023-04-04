<div id="events-by-date" class="font-sans border-2 border-t-0">

	@if(
		(!isset($contacts)) ||
		(!isset($cases)) ||
		($contacts->count() <= 0 && 
		$cases->count() <= 0)
		)

		<div class="px-2 pb-2 text-grey-dark text-center text-sm font-light">
			Nothing to show for {{ $date->format('F jS') }}
		</div>

	@else

	<div class="font-light text-center mb-2 py-2 border-t-2 border-b-2 bg-grey-lightest text-black">
		<!-- {{ $date->format('l, F jS, Y') }} -->
		{{ $date->format('l, F j') }}
	</div>
	<div class="pl-2 text-sm text-grey-dark">


		<table class="table">
			

			@isset($contacts)
				@if ($contacts->first())


				@include('shared-features.calendar.calendar-week-minigraph', ['week' => new \App\CalendarWeekViewModel($date)])

				<tr><td><div class="text-center text-grey-darkest text-sm uppercase tracking-wide font-bold">Notes</div></td></tr>

				<tr>
					<td>
						@foreach ($contacts as $contact)
							<div class="mb-2 text-grey-darker pb-2 mr-2 {{ (!$loop->last) ? 'border-dashed border-b' : '' }}">
								<a href="{{ Auth::user()->team->app_type }}/contacts/{{ $contact->id }}">
									@if ($contact->subject)
										{{ $contact->subject }}
									@endif
								</a>
									
								@if($contact->case)
									<a class="text-blue hover:text-black" href="{{ Auth::user()->team->app_type }}/cases/{{ $contact->case->id }}">
									<i class="far fa-folder-open mr-2"></i>
					        		<span class="font-bold">{{ $contact->case->subject }}</span>
					        		</a>
					        	@else
					        		<i class="far fa-user mr-2 text-blue"></i>
					        		@foreach($contact->people as $person)
					        			<a class="font-bold text-blue hover:text-black" href="{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">{{ $person->full_name }}</a>{{ (!$loop->last) ? ', ' : '' }}
					        		@endforeach
					        	@endif

								<div class="text-grey-dark">{{ substr($contact->notes, 0, 150) }}</div>
								
							</div>
						@endforeach
					</td>
				</tr>
				@endif
			@endisset

			@isset($cases)
				@if ($cases->count() > 0)
				<tr><td><div class="text-center text-grey-darkest text-sm uppercase tracking-wide font-bold">Cases</div></td></tr>

				<tr>
					<td>
							@foreach ($cases as $case)
								<div class="pb-2 pt-2 mr-2 {{ (!$loop->last) ? 'border-dashed border-b' : '' }}">
										<a class="text-blue hover:text-black" href="{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">
										<i class="far fa-folder-open mr-2"></i>
						        		<span>{{ $case->subject }}</span>
						        		</a>
						        		<div class="text-grey-dark">{{ substr($case->notes,0,100) }}</div>
								</div>
							@endforeach
					</td>
				</tr>
				@endif
			@endisset
		</table>
	</div>

	@endif
</div>