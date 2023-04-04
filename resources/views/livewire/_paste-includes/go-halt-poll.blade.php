<div class="py-2 flex bg-grey-lighter px-2 border-b-4 mt-2">

	<div class="flex-shrink font-bold text-blue pt-1">
		Count: {{ number_format($lines) }}
	</div>

	<div class="flex-grow font-bold text-black pt-1 px-2">
		Delimiter: {{ $delimiter }}
	</div>

	@if($count > 0)
		<div class="flex-grow text-grey-darkest text-sm py-2">{{ number_format($count) }} processed in {{ number_format($elapsed/60, 2) }} mins
		@if($time_remaining)
			/ {{ number_format($time_remaining/60, 2) }} mins remaining
		@endif
		</div>

	@endif

	<div class="flex-shrink">

	    @if($lines > 0 && !$process && is_numeric($map['voter_id']))

			<button class="rounded-lg bg-green text-white font-bold px-4 py-2"
				 wire:click="start()">
					Go
			</button>
			
		@endif

		@if($process)
			<div wire:poll.keep-alive>
		    	
		    	<span class="font-bold text-black">Processing</span>

				<button class="rounded-lg bg-grey-light text-black font-bold px-4 py-2"
						wire:click="halt()">Halt</button>

			</div>
		@endif

	</div>

</div>