<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
	Support
</div>

<div class="">

	<div class="">

		@foreach($campaigns as $campaign)
			<div class="flex {{ (!$campaign->current) ? 'opacity-50' : '' }} {{ (!$loop->last) ? 'border-b border-dashed' : '' }} pb-4">
				
				<div class="w-1/2 pt-2 text-base text-grey-darkest pl-2">

					<div class="font-bold">
						{{ \Carbon\Carbon::parse($campaign->election_day)->format('n.j.y') }}
					</div>

					<div class="text-lg">
						{{ $campaign->name }}
					</div>

					@if($campaign->current)
						<button class="mt-1 rounded-lg bg-blue uppercase text-xs text-white px-2 py-1 mr-2" data-toggle="tooltip" data-placement="top" title="This is the Current Campaign">
							<i class="fas fa-flag-usa"></i> Current Campaign
						</button>
					@endif

				</div>

				<div class="flex-grow p-2 text-base text-right">
					@if($participant->support($campaign))
						
						<span class="font-bold">{{ SupportNumberToEnglish($participant->support($campaign)) }}</span>
					
					@else
						<span class="text-grey-dark">No support recorded</span>
					@endif
				</div>

			</div>


			@if(1 == 1)
			@if($campaign->volunteer($participant))
				<div class="pb-2 flex flex-wrap {{ (!$campaign->current) ? 'opacity-50' : '' }}">

					<!-- <div>Volunteer:</div> -->

					@foreach($campaign->volunteer($participant) as $type => $value)

						<div class="uppercase text-sm text-blue pr-4 pt-2 whitespace-no-wrap">
							
							<i class="fas fa-check-circle mr-2 text-blue"></i> {{ str_replace('-', ' ', $type) }}

						</div>

					@endforeach

				</div>
			@endif
			@endif



		@endforeach


	</div>

	

</div>

<br clear="all" />