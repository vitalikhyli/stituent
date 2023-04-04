
<div class="md:flex font-normal md:border-t cursor-pointer">

	<div class="md:w-2/5 truncate md:border-r py-2">
		<span class="font-medium text-grey-darkest">
			{{ $voter->full_name }}
		</span>
		<div>{{ $voter->full_address }}</div>
		<div class="text-blue">{{ $voter->id }}</div>
	</div>

	@if($voter->is_participant)

		<div class="md:w-1/5 pl-4 p-2 truncate md:border-r text-sm">

			@if($voter->ago)
				<div>Updated</div>
				<div class="font-medium text-black">{{ $voter->ago }}</div>
				
			@else
				No history
			@endif

		</div>

		<div class="md:w-2/5 p-2">

			@if($voter->support)
				<div class="flex w-64">
					@for ($i=1; $i<6; $i++)
						<div class="w-1/5">
							@if($voter->support == $i)
								<div class="border-2 text-black rounded-full mx-auto w-8 h-8 pt-1 center cursor-pointer text-center bg-grey-lightest">
									{{ $i }}
								</div>
							@else
								<div class="border-2 border-transparent text-black rounded-full mx-auto w-8 h-8 pt-1 center cursor-pointer text-center">
									{{ $i }}
								</div>
							@endif
						</div>
						
					@endfor
				</div>
			@else

				Support not known

			@endif

			<div class="italic m-2 mt-4 text-left text-black" wire:key="notes_{{ $voter->id }}">
				{!! nl2br($voter->notes) !!}
			</div>

		</div>

	@endif

</div>

<div class="md:hidden border-b-4"></div>