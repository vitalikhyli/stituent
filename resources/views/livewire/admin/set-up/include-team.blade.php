<div class="flex">

	<div class="text-2xl pt-6 pr-4 text-grey-dark font-bold w-48">
		<div class="border-r-4">
			2. Team
		</div>
	</div>

	<div class="p-6">

    	<button wire:click="teamModeNew()"
    			class="rounded-lg {{ ($team_mode == 'new') ? 'bg-blue text-white' : 'bg-grey text-grey-lightest' }} px-4 py-2">
    		New Team
    	</button>

    	@if($account->teams->first())

	    	<button wire:click="teamModeExisting()"
	    			class="rounded-lg {{ ($team_mode == 'existing') ? 'bg-blue text-white' : 'bg-grey text-grey-lightest' }} px-4 py-2">
	    		Existing Team
	    	</button>

    	@endif

        <div>

        	@if($team_mode == 'existing')

    			<div class="mt-2">

        			<div class="py-2">

        				Existing Team:

        				<select wire:model="team_id">

    						<option value="">
    							-- CHOOSE TEAM --
    						</option>
    						
        					@foreach($account->teams as $team_option)

        						<option value="{{ $team_option->id }}">
        							@if($team_option->data_folder_id)
        								{{ strtoupper($team_option->data_folder_id) }} | 
        							@else
        								-- | 
        							@endif
        							{{ $team_option->name }}
                                    ({{ $team_option->app_type }})
        						</option>

        					@endforeach

        				</select>

    				</div>

    			</div>

        	@endif

        </div>

        <div>

        	@if($team_mode == 'new')

        		<div class="mt-2">

        			<div class="py-2">

        				New Team:

        				<input type="text"
        					   wire:model="new_team_name"
        					   class="border p-2 mr-2 w-48" />

        				State:

        				<input type="text"
        					   wire:model="new_team_state"
        					   class="border p-2 mr-2 w-32" />

                        App:

                        <select wire:model="new_team_app_type">

                            <option value="">
                                -- CHOOSE --
                            </option>
                            
                            @foreach($available_app_types as $app_type_option)

                                <option value="{{ $app_type_option }}">
                                    {{ $app_type_option }}
                                </option>

                            @endforeach

                        </select>

        			</div>

                    <div>

            			@if($new_team_name || $new_team_state)

        	    			<div class="p-2 px-4 border-2 flex bg-grey-lightest">

        	    				<div class="py-1 text-xl">
        	    					<span class="font-bold">{{ $new_team_name }}</span>
        	    					@if($new_team_state)
        	    						in <span class="font-bold">{{ $new_team_state }}</span>
        	    					@endif
                                    @if($new_team_app_type)
                                        <span class="text-blue text-base">/ {{ $new_team_app_type }}</span>
                                    @endif
        	    				</div>

                                <div class="flex-grow text-right">
            						@if($new_team_name && $new_team_state && $new_team_app_type)
            		    				<div class="flex-grow text-right pt-2">
            						    	<button wire:click="createTeam()"
            						    			class="rounded-lg bg-blue text-white px-2 py-1 text-sm ml-2">
            						    		Save + Continue
            						    	</button>
            						    </div>
            						@endif
                                </div>

        	    			</div>

            			@endif

                    </div>

        		</div>

        	@endif

        </div>

        <div>
            
    	    @if($team)

    	    	<div class="mt-4 text-xl font-bold text-blue">

    	    		<i class="fas fa-check-circle"></i> {{ $team->name }}

    	    	</div>

                <div class="py-1">

                    @if($team->app_type == 'office' && !$team->hasPresetCats())

                        <div class="mt-4">

                            <button class="rounded-lg bg-blue text-white px-4 py-2"
                                    wire:click="addPresetCats()">
                                Add 3 Preset Office Group Categories
                            </button>

                        </div>

                    @elseif($team->app_type == 'office' && $team->hasPresetCats())

                        <div class="mt-4 text-xl font-bold text-blue">

                            <i class="fas fa-check-circle"></i> Has Preset Office Group Categories

                        </div>

                    @endif

                </div>

    	    @endif

        </div>


    </div>

</div>