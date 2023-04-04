<div>

	<div class="flex cursor-pointer"
		 wire:click="toggleActive()">

		<div class="rounded-full h-{{ $size }} w-{{ $size *2 }} flex {{ ($model->active) ? 'flex-row-reverse bg-blue' : 'bg-red' }}">
		  
			 <div class="rounded-full h-{{ $size }} w-{{ $size }} bg-white border"></div>
			
		</div>

		<div class="pl-1 font-medium
					@if($size >= 8)
						text-base
					@elseif($size >= 6)
						text-sm
					@else
						text-xs
					@endif
					">

			@if($model->active)
				<span class="text-blue">Active</span>
			@else
				<span class="text-red">Inactive</span>
			@endif

		</div>

	</div>

</div>
