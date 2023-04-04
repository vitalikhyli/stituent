<div class="mt-6 text-xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	My Goals
</div>

@if(count($goals) <= 0 )


	<div class="py-2 text-grey-dark">
		None
	</div>

@else

	<div class="w-full">

		@foreach($goals as $key => $goal)
			<div class="flex w-full pt-2">
				<div class="w-16 p-1 border-r">
					{{ $goal['type'] }}{{ $goal['period'] }}
				</div>
				<div class="text-lg p-1 w-1/2 whitespace-no-wrap pl-2 flex mr-2">
					<div class="text-green w-1/2 text-right">${{ number_format($goal['have']) }}</div>
					<div class="text-grey text-sm px-2">/</div>
					<div class="text-green-dark font-bold w-1/2 text-right">${{ number_format($goal['need']) }}</div>
				</div>
				
				<div class="w-1/2 bg-green-lighter flex">
					@if($goal['percentage'] > 0)

						<div class="z-50 absolute text-white p-1 text-xs ml-1 mt-1">
							{{ $goal['percentage'] }}%
						</div>

						<div class="bg-green-dark rounded p-2 text-xs " style="width:{{ $goal['percentage'] }}%" />
						</div>
					@endif
				</div>
			</div>
		@endforeach

	</div>

@endif