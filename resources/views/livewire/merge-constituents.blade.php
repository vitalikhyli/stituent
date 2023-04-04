<div>
    <div class="text-4xl font-bold border-b-4">
    	Merge Constituents
    </div>

    <div class="flex">
    	<div class="w-1/6">
    	</div>
    	<div class="w-5/6">

		    <div class="flex mt-4">
		    	<div class="w-1/3">

		    		<div class="text-lg font-bold mt-4 text-grey-dark mb-2">
		    			
		    				<i wire:click="switch()" class="fa fa-arrows-alt-h mr-8 mt-1 cursor-pointer text-xl float-right
		    					@if ($constituent_one && $constituent_two)
		    						block
		    					@else
		    						hidden
		    					@endif
		    					"></i>
		    			
		    			Primary Record
		    			<div class="text-sm">
		    				(This will be kept & expanded)
		    			</div>
		    		</div>

		    		@if ($constituent_one)
						<div class="text-grey-darkest cursor-pointer text-lg">
							<b>{{ $constituent_one->name }}</b>
							@if ($constituent_one->is_person) 
								<i class="fa fa-check"></i>
							@endif
							<div class="text-sm text-gray-400">
								{{ $constituent_one->full_address }}
							</div>
							<div class="text-sm text-grey hover:text-red" wire:click="removeOne()">
								Remove
							</div>
						</div>
		    		@else
				    	<input wire:model="lookup_one" type="text" class="text-xl border-2 p-2" placeholder="Constituent 1 Lookup" />
				    	<div class="">

					    	@foreach ($constituents_one as $constituent)

								<div wire:click="selectOne('{{ $constituent->id }}')" class="text-grey-darkest mt-2 hover:bg-gray-100 cursor-pointer">
									{{ $constituent->name }}
									@if ($constituent->is_person) 
										<i class="fa fa-check"></i>
									@endif
									<div class="text-sm text-gray-400">
										{{ $constituent->full_address }}
									</div>
								</div>

					    	@endforeach
					    </div>
				    @endif
			    </div>
			    <div class="w-1/3">

			    	<div class="text-lg font-bold mt-4 text-grey-dark mb-2">
		    			Merged Record
		    			<div class="text-sm">
		    				(This will be removed)
		    			</div>
		    		</div>

			    	@if ($constituent_two)
						<div class="text-grey-darkest cursor-pointer text-lg">
							<b>{{ $constituent_two->name }}</b>
							@if ($constituent_two->is_person) 
								<i class="fa fa-check"></i>
							@endif
							<div class="text-sm text-gray-400">
								{{ $constituent_two->full_address }}
							</div>
							<div class="text-sm text-grey hover:text-red" wire:click="removeTwo()">
								Remove
							</div>
						</div>

		    		@else
				    	<input wire:model="lookup_two" type="text" class="text-xl border-2 p-2" placeholder="Constituent 2 Lookup" />
				    	<div class="">

					    	@foreach ($constituents_two as $constituent)

								<div wire:click="selectTwo('{{ $constituent->id }}')" class="text-grey-darkest mt-2 hover:bg-gray-100 cursor-pointer">
									{{ $constituent->name }}
									@if ($constituent->is_person) 
										<i class="fa fa-check"></i>
									@endif
									<div class="text-sm text-gray-400">
										{{ $constituent->full_address }}
									</div>
								</div>

					    	@endforeach
					    </div>
				    @endif
			    </div>
			    
		    </div>
		</div>
	</div>

	@if ($constituent_one && $constituent_two)
	
	<div class="text-grey-darkest text-sm">

		<div class="flex">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		
			    	</div>
			    	<div class="w-1/3 p-1">
			    		
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r border-t font-bold text-lg text-grey-darker p-2">
			    		Combined Record
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		ID
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		@if ($constituent_one->is_person)
			    			{{ $constituent_one->id }}
			    		@endif
			    	</div>
			    	<div class="w-1/3 p-1">
			    		@if ($constituent_two->is_person)
			    			{{ $constituent_two->id }}
			    		@endif
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $combined['id'] }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		State ID
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->voter_id }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->voter_id }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		&nbsp; {{ $combined['voter_id'] }}
			    	</div>
			    </div>
			</div>
		</div>


		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Title
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->title }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->title }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		<input wire:model="combined.title" type="text" />
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Name
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->name }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->name }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		<div class="flex">
			    			<div class="w-1/2">
			    				<input wire:model="combined.first_name" type="text" />
			    			</div>
			    			<div class="w-1/2">
			    				<input wire:model="combined.last_name" type="text" />
			    			</div>
			    		</div>
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Address
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->full_address }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->full_address }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		&nbsp; {{ $combined['full_address'] }} 
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Email
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->email }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->email }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $constituent_one->email }} &nbsp;{{ $constituent_two->email }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Contacts
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->contacts()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->contacts()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $constituent_one->contacts()->count() + $constituent_two->contacts()->count() }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Groups
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->groups()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->groups()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $constituent_one->groups()->count() + $constituent_two->groups()->count() }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Cases
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->cases()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->cases()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $constituent_one->cases()->count() + $constituent_two->cases()->count() }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Bulk Emails
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_one->bulkEmails()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1">
			    		{{ $constituent_two->bulkEmails()->count() }}
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		{{ $constituent_one->bulkEmails()->count() + $constituent_two->bulkEmails()->count() }}
			    	</div>
			    </div>
			</div>
		</div>

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		Elections
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		@foreach ($constituent_one->elections_pretty as $ep)

							{{ $ep['date'] }} {{ $ep['type'] }}<br>

			    		@endforeach
			    	</div>
			    	<div class="w-1/3 p-1">
			    		@foreach ($constituent_two->elections_pretty as $ep)

							{{ $ep['date'] }} {{ $ep['type'] }}<br>

			    		@endforeach
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-l border-r">
			    		&nbsp;
			    	</div>
			    </div>
			</div>
		</div>

	

		<div class="flex border-t">
	    	<div class="w-1/6 text-grey-dark p-1 uppercase text-xs font-bold">
	    		
	    	</div>
	    	<div class="w-5/6">

			    <div class="flex">
			    	<div class="w-1/3 p-1">
			    		
			    	</div>
			    	<div class="w-1/3 p-1">
			    		
			    	</div>
			    	<div class="w-1/3 p-1 bg-blue-lightest border-2">

			    		@if ($merge_log)

			    			<div class="text-red-600">
			    				The record for <b>{{ $constituent_two->name }}</b> will be archived.<br><br>
			    				
			    				The following items will be <b>added</b> to the record of {{ $constituent_one->name }}:
			    			</div>

			    			@foreach ($merge_log as $section => $pivot)
			    				<div class="font-bold border-b mt-2 uppercase">
			    					{{ $section }}
			    				</div>
			    				<ul class="list-decimal pl-5 text-gray-400">
				    				@foreach ($pivot as $pivot_id => $items) 
					    				
					    				<li>{{ $items['name'] }}</li>
					    				
					    			@endforeach
					    		</ul>
			    			@endforeach

			    			<div wire:click="confirmMerge()" class="cursor-pointer px-3 py-2 bg-blue-400 hover:bg-blue-500 transition text-white text-center uppercase">
				    			Confirm Merge
				    		</div>
			    			
			    		@else

			    			<div wire:click="merge()" class="cursor-pointer px-3 py-2 bg-blue-400 hover:bg-blue-500 transition text-white text-center uppercase">
				    			Merge
				    		</div>
			    		@endif

			    		
			    	</div>
			    </div>
			</div>
		</div>
		
	</div>
	
	@endif
    
</div>
