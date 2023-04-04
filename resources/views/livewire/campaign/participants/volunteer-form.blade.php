<div>

	<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium py-1 mt-1 rounded-t-lg">
		Volunteer
	</div>

	<label for="is_volunteer" class="font-normal py-1 text-sm">

		<input id="is_volunteer"
			   name="is_volunteer"
			   type="checkbox"
			   wire:model="current" />

		Current Volunteer

	</label>

	@if(!$current)

		<div class="opacity-50 flex flex-wrap">

			@foreach($volunteer_options as $option)

				<div class="w-1/4">

					<span class="capitalize text-sm text-blue whitespace-no-wrap pr-2">
						
						<label for="volunteer_{{ $option }}" class="font-normal">

							<input wire:model="types"
								   value="{{ $option }}"
								   id="volunteer_{{ $option }}" name="volunteer_{{ $option }}" type="checkbox"
								   disabled />

							<span class="ml-1">
								{{ str_replace('_', ' ', str_replace('-', ' ', $option)) }}
							</span>

						</label>
						
					</span>

				</div>

			@endforeach

		</div>


	@else

		<div class="flex flex-wrap border-l-2 border-blue pl-3">

			@foreach($volunteer_options as $option)

				<div class="w-1/4">

					<span class="capitalize text-sm text-blue whitespace-no-wrap pr-2">
						
						<label for="volunteer_{{ $option }}" class="font-normal">

							<input wire:model="types"
								   value="{{ $option }}"
								   id="volunteer_{{ $option }}" name="volunteer_{{ $option }}" type="checkbox"
								    />

							<span class="ml-1">
								{{ str_replace('_', ' ', str_replace('-', ' ', $option)) }}
							</span>

						</label>
					</span>

				</div>

			@endforeach

		</div>

	@endif


</div>
