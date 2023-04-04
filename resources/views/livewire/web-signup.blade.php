<div class="flex border-b p-2 text-sm text-gray-500 w-full">
	<div class="w-1/5">
	    <b>{{ $websignup->name }}</b><br>
	    @if (isset($websignup->data['location']))
	    	<div class="uppercase text-xs">
	    		{{ $websignup->data['location'] }}
	    	</div>
	    @endif
	</div>
	<div class="w-2/5">
	    <div><b>{{ $websignup->email }}</b></div>
	    {{ $websignup->note }}
	</div>
	<div class="w-1/5 uppercase text-xs">
		<div class="text-gray-400">
			{{ $websignup->created_at->format('M d, g:ia') }}
		</div>
		@if ($websignup->data)
			@if (isset($websignup->data['volunteer']) && is_array($websignup->data['volunteer']))
				@foreach ($websignup->data['volunteer'] as $volunteer)
			    	<i class="fa fa-check-square text-blue-500"></i> {{ str_replace(['volunteer', '_'], ["", ' '],$volunteer) }}<br>
			    @endforeach
		    @endif
	    @endif
	</div>
	<div class="w-1/5">
	    
	    
	    @if (!$websignup->participant_id)
			<div wire:click="addAsVolunteer()" 
				 wire:loading.attr="disabled"
				 wire:loading.class="opacity-50"
				 class="text-center bg-blue-400 px-2 py-1 rounded-full text-white hover:bg-blue-500 cursor-pointer">
				Add as Volunteer
			</div>
		@else
			<i class="fa fa-check"></i>
			<a href="/campaign/participants/{{ $websignup->participant->id }}">
				{{ $websignup->participant->name }}
			</a>

		@endif

	</div>
</div>
