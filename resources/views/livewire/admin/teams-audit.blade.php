<div>

   	<div class="text-xl border-b-4 border-red py-1 flex">

        <div class="flex-grow">
            Audit Teams

			- {{ $teams->count() }} / 

            <span class="text-grey-dark"><i class="fas fa-thumbs-down text-lg ml-1"></i> {{ $teams->where('fail', true)->count() }}</span>

        </div>

        <div class="flex text-sm">


	        <div class="p-2">

        		<select type="checkbox"
        			   id="app_type"
        			   wire:model="app_type">

        			   <option value="">Any app_type</option>

        			   @foreach($app_types as $type)

        			   		<option value="{{ $type }}">{{ $type }}</option>

        			   @endforeach

        		</select>

	        </div>


	        <div class="p-2">

	        	<label for="exclude_inactive" class="font-normal">

	        		<input type="checkbox"
	        			   id="exclude_inactive"
	        			   wire:model="exclude_inactive" />

	        		Excl. Inactive

	        	</label>

	        </div>


	        <div class="p-2">

	        	<label for="sort_date" class="font-normal">

	        		<input type="checkbox"
	        			   id="sort_date"
	        			   wire:model="sort_date" />

	        		Sort: Date

	        	</label>

	        </div>

	        <div class="p-2 mr-2">

	        	<label for="sort_problems" class="font-normal">

	        		<input type="checkbox"
	        			   id="sort_problems"
	        			   wire:model="sort_problems" />

	        		Sort: Problems

	        	</label>

	        </div>

	    </div>

        <div class="mr-2">
          <input type="text" 
          		 id="filter-input" 
          		 class="border-2 rounded-lg p-2 text-base" 
          		 placeholder="Filter Teams"
          		 wire:model.debounce="search" />
        </div>

    </div>


    <div class="font-mono text-grey-darker text-xs py-1  text-right">

    	Query and collection processing took {{ number_format($query_time, 3) }}

    </div>

    <!---------------------------------- Poor man's pagination ---------------------------------->
    <div class="py-2">

    	<button class="text-grey-darker px-2 py-1 border {{ ($page - 1 > 0) ? '' : 'hidden' }}"
    			wire:click="$set('page', {{ $page - 1 }})">
    		Prev
    	</button>

    	<button class="opacity-50 text-grey-darker px-2 py-1 border {{ ($page - 1 > 0) ? 'hidden' : '' }}">
    		Prev
	    </button>

	    {{ $take }} per page
    	<input type="text"
    		   class="text-grey-darker px-2 py-1 border w-12"
    		   wire:model="page" />

    	<button class="text-grey-darker px-2 py-1 border"
    			wire:click="$set('page', {{ $page + 1 }})">
    		Next
    	</button>

    </div>
	<!-------------------------------- //Poor man's pagination -------------------------------->

	<div class="flex border-b border-grey-lighter bg-blue text-white text-xs">

		<div class="truncate p-2 border-r border-white w-24">
    		Created
    	</div>

		<div class="truncate p-2 border-r border-white w-12">
    		ID
    	</div>

		<div class="truncate p-2 border-r border-white w-32">
	    	Account
	    </div>

		<div class="truncate p-2 border-r border-white flex-grow">
    		Team
    	</div>

		<div class="p-2 border-r border-white w-24 bg-blue-dark">
    		App Type
    	</div>

		<div class="p-2 border-r border-white w-12">
    		Has Slice
    	</div>

		<div class="p-2 border-r border-white w-12">
    		Table Exists in DB
    	</div>

		<div class="p-2 border-r border-white w-12">
    		Has at least 1 Admin
    	</div>

		<div class="p-2 border-r border-white w-12">
    		Has Billy Goat ID
    	</div>

		<div class="p-2 border-r border-white w-12">
    		(office) Has 3 Preset Groups
    	</div>

		<div class="p-2 border-r border-white w-12 bg-blue-dark">
    		Pass / Fail
    	</div>

    </div>

    @foreach($teams->groupBy('data_folder_id') as $state => $teams)

    	<div class="border-b-2 py-1 text-xl font-bold">
    		{{ $state }}
    	</div>

    @foreach($teams as $team)

    	<div class="flex border-b border-dashed" wire:key="{{ $team->id }}_row">

    		<div class="p-2 w-24 text-sm" wire:key="{{ $team->id }}_created_at">
	    		{{ \Carbon\Carbon::parse($team->created_at)->format('n/j/y') }}
	    	</div>

    		<div class="p-2 truncate w-12 text-grey-dark text-sm" wire:key="{{ $team->id }}_id">
	    		{{ $team->id }}
	    	</div>

			<div class="p-2 text-sm truncate w-32" wire:key="{{ $team->id }}_account">
		    	@if(!$team->account)
		    		<span class="text-grey-light">No Account?</span>
		    	@else
		    		{{ $team->account->name }}
		    		@if(!$team->account->active)
		    			<div class="text-red">Inactive</div>
		    		@endif
		    	@endif
		    </div>

    		<div class="p-2 truncate flex-grow text-blue" wire:key="{{ $team->id }}_name">
		    		
		    	<a href="/admin/accounts/{{ $team->account_id }}/teams/{{ $team->id }}/edit">
	    			{{ $team->name }}
	    		</a>

	    		@if(!$team->active)
	    			<div class="text-red">Inactive</div>
	    		@endif
	    	</div>

	    	<div class="p-2 truncate w-24 text-left border-r"
	    		 wire:key="{{ $team->id }}_app_type">
	    		<span class="text-sm">{{ $team->app_type }}</span>
	    	</div>

			<div class="p-2 border-white text-center w-12" wire:key="{{ $team->id }}_has_slice">
	    		@if(!$team->has_slice)
	    			<i class="fas fa-times text-grey-light"></i>
	    		@else
					<i class="fas fa-check-circle text-blue"></i>
	    		@endif
	    	</div>

			<div class="p-2 border-white text-center w-12" wire:key="{{ $team->id }}_table_exists">
	    		@if(!$team->table_exists)
	    			<i class="fas fa-times text-grey-light"></i>
	    		@else
					<i class="fas fa-check-circle text-blue"></i>
	    		@endif
	    	</div>

			<div class="p-2 border-white text-center w-12" wire:key="{{ $team->id }}_has_admin">

	    		@if($team->has_admin)

		    		<span wire:click="setTooltip('Admins', {{ $team->id }})"
		    			  class="rounded-full border bg-orange-lightest px-2 py-1 text-sm cursor-pointer">

		    			<i class="fas fa-check-circle text-blue"></i>

		    		</span>

	    		@else

					<span wire:click="setTooltip('Admins', {{ $team->id }})"
		   		    	 class="rounded-full border bg-orange-lightest px-2 py-1 text-sm cursor-pointer hover:bg-blue">

						<i class="fas fa-times text-grey-light"></i>

					</span>

				@endif


			 	@includeWhen(($tooltipAdmins == $team->id),
			 				 'livewire.admin.choose-admins',
			 				 ['team' => $team])


	    	</div>

			<div class="p-2 border-white text-center w-12" wire:key="{{ $team->id }}_has_admin">
	    		@if(!$team->has_billygoat_id)
	    			<i class="fas fa-times text-grey-light"></i>
	    		@else
					<i class="fas fa-check-circle text-blue"></i>
	    		@endif
	    	</div>

			<div class="p-2 border-white text-center w-12" 
				 wire:key="{{ $team->id }}_has_group_presets">

				@if($team->app_type != 'office')
					<span class="text-grey-light text-sm">n/a</span>
	    		@elseif($team->has_group_presets)
	    			<i class="fas fa-check-circle text-blue"></i>
	    		@else

		   		    <span wire:click="setTooltip('Groups', {{ $team->id }})"
		   		    	 class="rounded-full border bg-orange-lightest px-2 py-1 text-sm cursor-pointer hover:bg-blue">

						<i class="fas fa-times text-grey-light"></i>

					</span>

				 	<div class="relative {{ ($tooltipGroups == $team->id) ? '' : 'hidden' }}">

						<div class="absolute z-10 w-64 -ml-32 px-4 py-4 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">

							<div wire:click="setTooltip('Groups', null)"
								 class="border-b pb-1 mb-1 flex">
								<div class="font-bold">
								 	
								</div>
								<div class="flex-grow text-right">
									<i class="fas fa-times text-grey-lightest text-xl"></i>
								</div>
							</div>

							<div class="font-medium">
								Would you like to create these 3 group categories for team "{{ $team->name }}?"
							</div>

							<div class="text-left my-2 border-t py-2">
								<ul class="-ml-4">
									<li>Constituent Groups</li>

									<li>Issue Groups</li>

									<li>Legislation</li>

								</ul>
							</div>

							<button class="rounded-lg bg-blue text-white px-3 py-2 hover:bg-white hover:text-blue-dark"
									wire:click="createPresetGroupsForTeam({{ $team->id }})">
								Yes, Create Now
							</button>

						</div>

					</div>

	    		@endif

	    	</div>

			<div class="p-2 border-l text-center w-12" wire:key="{{ $team->id }}_fail">
	    		@if(!$team->fail)
					<i class="fas fa-thumbs-up text-blue"></i>
	    		@endif
	    	</div>

	    </div>

    @endforeach
    @endforeach

</div>
