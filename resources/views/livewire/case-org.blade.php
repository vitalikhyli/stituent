<div>

	
	@foreach ($cases as $case)
		<div class="py-2">
			<div wire:click="removeCase('{{ $case->id }}')" class="float-right text-red-300 text-xs hover:text-red-500 cursor-pointer">
				Unlink Case
			</div>
			<a class="text-blue-500" href="/{{ Auth::user()->app_type }}/cases/{{ $case->id }}">
				{{ $loop->iteration }}.
				@if ($case->date)
					{{ $case->date->format('M j, Y') }} - 
				@endif
				@if ($case->type)
					<span class="text-xs border uppercase p-1 font-bold bg-blue-100">
						{{ $case->type }}
						@if ($case->subtype)
							/{{ $case->subtype }}
						@endif
					</span>
				@endif
				<span class="font-bold ml-2">{{ $case->name }}</span>
			</a>
			<div class="text-sm text-gray-500">
				Opened by {{ $case->user->name }}, {{ $case->contacts()->count() }} Contacts.
			</div>
		</div>
	@endforeach
	

    <div class="flex items-center mt-4">
    	<div class="w-1/2">
    		<select wire:model="link_case_id" class="border-2 p-2 rounded my-2 w-full">
    			<option value="">- Link an Existing Case -</option>
	    		@foreach ($allcases as $status => $tempcases)
	    			<optgroup label="{{ strtoupper($status) }}">
		    			@foreach ($tempcases as $case)
		    			<option value="{{ $case->id }}">
		    				{{ $case->created_at->format('M Y') }} - 
							{{ $case->name }}
							@if ($case->type)
								@if ($case->subtype)
									({{ $case->type }}/{{ $case->subtype }})
								@else
									({{ $case->type }})
			    				@endif
			    				
		    				@endif
		    			</option>
		    			@endforeach
		    		</optgroup>
	    		@endforeach
	    	</select>
    	</div>
		<div class="w-1/2 pl-4">

			
				<div class="p-4 border
							@if ($creating_new_case) 
								block
							@else
								hidden
							@endif
							">
					<input type="text" wire:model="new_case_name" class="border-2 p-2 w-full" placeholder="New Case Name"/>
					@if ($new_case_name)
						<div class="text-center cursor-pointer bg-blue-500 rounded mt-4 p-2 text-white" wire:key="rand(1,1909090)" wire:click="newCase()">Start Case</div>
					@else
						<div class="bg-gray-400 rounded mt-4 p-2 text-center text-white">Start Case</div>
					@endif
				</div>
			
				<div class="cursor-pointer 
							@if ($creating_new_case) 
								hidden
							@else
								block
							@endif
							hover:text-blue-500" wire:click="creatingCase()">
					Start New Case
				</div>

			
		</div>
	</div>

</div>
