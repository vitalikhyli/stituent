<div>
    
    @if(!$person)

        @include('livewire.merge._person-lookup')

    @else

    	<div class="text-4xl font-bold pb-2 text-blue pb-4">
    		{{ $person->full_name }}

    		<div class="float-right text-red text-base cursor-pointer"
    			 wire:click="">
    			X
    		</div>
    	</div>

	    <div>
	    	@foreach($attributes as $field)

	    		@if(!is_array($person->$field))
	    			<div class="pb-4">
	    				<div class="text-blue text-xs uppercase">
	    					{{ $field }}
						</div>
						<div class="text-grey-darker">
							@if(!$person->$field)
								--
							@else
								{{ $person->$field }}
							@endif
						</div>
	    			</div>
	    		@endif

	    	@endforeach
	    </div>

    @endif

</div>
