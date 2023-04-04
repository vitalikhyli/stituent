<div>

    <div wire:loading class="w-18">
        <i class="fas fa-sync fa-spin text-2xl text-blue"></i>
    </div>

    <div wire:init="loadPosts" wire:loading.remove>

    	@if(empty($lists))

    		<div class="p-2 text-grey">
    			None
    		</div>

    	@else

			<ul class="list-disc text-sm -ml-4">
				@foreach($lists as $list)
					<li>
						<a href="/campaign/lists/{{ $list->id }}" class="text-blue">{{ $list->name }}</a>
					</li>
				@endforeach
			</ul>

		@endif

    </div>

</div>
