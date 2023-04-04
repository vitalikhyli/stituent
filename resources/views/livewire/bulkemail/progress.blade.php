<div>

	@if($total > 0)

		<div>
			--
		</div>

	@elseif(!$done)

		<div {{ ($percentage < 100) ? 'wire:poll' : '' }} />

			<div class="bg-grey-lightest border">

	    		<div style="width:{{ $percentage }}%;" class="bg-blue p-1 text-xs text-white">
	    			{{ $percentage }}%
	    		</div>

	    	</div>

	    </div>

	@else

		<div>
			Done
		</div>

	@endif

</div>
