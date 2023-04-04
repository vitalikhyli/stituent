<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-4">
	Support
</div>

<div class="pb-1">

	<div class="">

		@foreach($campaigns as $campaign)
			<div class="border-orange {{ ($campaign->current) ? 'bg-orange-lightest border-l-4' : 'opacity-50' }}">

				<div class="flex">
				
					<div class="w-3/4 py-2 text-sm uppercase text-grey-darkest px-2">
						{{ $campaign->election_day }} {{ $campaign->name }}

						@if($campaign->current)
							<button type="button" class="ml-1 rounded-lg bg-blue uppercase text-xs text-white px-2 py-1" data-toggle="tooltip" data-placement="top" title="This is the Current Campaign">
								<i class="fas fa-flag-usa"></i>
							</button>
						@endif

					</div>

					<div class="w-1/4 p-2 text-base text-right">
						<select name="support_{{ $campaign->id }}" class="w-full">
							@foreach([''=>'-- Support? --',
									  1=>'Yes',
									  2=>'Lean Yes',
									  3=>'Undecided',
									  4=>'Lean No',
									  5=>'No'] as $the_id => $english)
								<option value="{{ $the_id }}" {{ ($model->support($campaign) == $the_id) ? 'selected' : '' }}>
									{{ $english }}
								</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="px-2">
					<textarea name="notes_{{ $campaign->id }}"
							  class="w-full border-2"
							  placeholder="Notes">{{ $model->notes($campaign) }}</textarea>
				</div>


			</div>

			@if(1 == 1)
			@if($campaign->current)

				<div class="pb-2 pl-1 border-l-4 border-orange flex flex-wrap">

					@foreach($campaign->volunteer_options($model) as $option => $value)

						<div class="w-1/3">

							<input type="hidden" name="volunteer_{{ $campaign->id }}_{{ $option }}"  value="0" />

							<span class="capitalize text-sm text-blue whitespace-no-wrap p-2">
								
								<label for="volunteer_{{ $campaign->id }}_{{ $option }}" class="font-normal">

									<input id="volunteer_{{ $campaign->id }}_{{ $option }}" name="volunteer_{{ $campaign->id }}_{{ $option }}" type="checkbox" {!! ($value) ? 'checked' : '' !!} value="1" />

									{{ str_replace('-', ' ', $option) }}

								</label>
							</span>

						</div>

					@endforeach

				</div>

			@endif
			@endif


		@endforeach



	</div>
</div>

<br clear="all" />