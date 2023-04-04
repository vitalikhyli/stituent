<textarea placeholder="Paste line or space separated Voter IDs here"
	  wire:load.class="opacity-50"
	  class="
	  @if($lines > 0)
	  	border-4 border-blue
	  @else
	  	border-2
	  @endif
	  p-2 w-full h-64 font-mono"
	  wire:model="textarea"></textarea>

	@if ($visible_chunk)

		<div class="w-full text-xs flex" wire:load.class="opacity-50">
			
			<div class="flex border-b m-2 overflow-x-auto">
				<div>
		    		@foreach ($visible_chunk[0] as $index => $val)
		    			<div class="h-4">
		    				<select class="border-l border-t border-r bg-gray-50 w-48" wire:change="setIndex({{ $index }}, $event.target.value)">
		    					<option value=""></option>
		    					<option 
		    						@if ($index === $map['voter_id'])
		    							selected 
		    						@endif
		    						value="voter_id">Voter ID</option>
		    					<option 
		    						@if ($index === $map['email'])
		    							selected 
		    						@endif
		    						value="email">Email</option>
		    					<option 
		    						@if ($index === $map['phone'])
		    							selected 
		    						@endif
		    						value="phone">Phone</option>
		    				</select>
		    			</div>
		    		@endforeach
		    	</div>
		    	<div class="overflow-x-auto flex">
		    		@foreach ($visible_chunk as $line_arr)
		    			<div>
		    				@foreach ($line_arr as $index => $val) 
		    					@if ($index === $map['voter_id'])
		    						<div class="border-l border-t px-1 h-4 bg-blue-100 font-bold">{{ $val }}</div>
		    					@elseif ($index === $map['phone'] || $index === $map['email'])
		    						<div class="border-l border-t px-1 h-4 bg-gray-100">{{ $val }}</div>
		    					@else
		    						<div class="border-l border-t px-1 h-4">{{ $val }}</div>
		    					@endif
		    				@endforeach
		    			</div>
		    		@endforeach
		    	</div>
			</div>
		</div>

	@endif