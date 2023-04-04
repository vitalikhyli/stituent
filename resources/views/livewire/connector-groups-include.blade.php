<div class="relative" >

	<div class="absolute top-0 z-10 w-64 px-4 py-4 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">

		<div class="font-bold truncate text-lg">
			<i class="fas fa-user-circle text-3xl mr-2 float-left"></i>
			{{ $link->full_name }}
		</div>

    	<div class="text-sm">
    		@foreach($link->team->categories->sortBy('name') as $category)

    			@if(!$category->groups->first())
    				@continue
    			@endif

    			<div class="py-1 mb-1 font-medium mt-2 text-grey-lighter border-b border-grey-dark">
    				{{ $category->name }}
    			</div>
    			<div class="border-grey-darker ml-2">

    				@foreach($category->groups()->whereNull('archived_at')->orderBy('name')->get() as $group)
	        			<div class="py-1">
	        				<label for="groupPerson_{{ $link->id }}_{{ $group->id }}_on" class="font-normal">
		        				<input type="checkbox"
		        					   id="groupPerson_{{ $link->id }}_{{ $group->id }}_on"
		        					   wire:click="toggleGroupMembership({{ $link->id }}, {{ $group->id }})"
									   {!! ($link->memberOfGroup($group->id)) ? 'checked' : '' !!}
		        					   >
		        					   <span class="ml-1">{{ $group->name }}</span>
		        					   
	        				</label>

	        				@if($link->memberOfGroup($group->id))

	        					@if($category->has_position)

        						<div class="ml-4">

			        				<label for="groupPerson_{{ $link->id }}_{{ $group->id }}_supports" class="font-normal ml-2">
				        				<input type="checkbox"
				        					   name="groupPerson_{{ $link->id }}_{{ $group->id }}_position"
				        					   value="Supports"
				        					   id="groupPerson_{{ $link->id }}_{{ $group->id }}_supports"
				        					   wire:click="setGroupSupport({{ $link->id }}, {{ $group->id }}, 'Supports')"
				        					   {!! ($link->groupSupport($group->id) == 'Supports') ? 'checked' : '' !!}
				        					   >
				        					   <span class="ml-1">Supports</span>
			        				</label>

			        				<label for="groupPerson_{{ $link->id }}_{{ $group->id }}_opposed" class="font-normal ml-2">
				        				<input type="checkbox"
				        					   name="groupPerson_{{ $link->id }}_{{ $group->id }}_position"
				        					   value="Opposed"
				        					   id="groupPerson_{{ $link->id }}_{{ $group->id }}_opposed"
		        					   		   wire:click="setGroupSupport({{ $link->id }}, {{ $group->id }}, 'Opposed')"
				        					   {!! ($link->groupSupport($group->id) == 'Opposed') ? 'checked' : '' !!}
				        					   >
				        					   <span class="ml-1">Opposed</span>
			        				</label>

        						</div>

        						@endif

								@if(1==2 && $category->has_notes)

        						<div class="ml-4">
									<input type="text"
			        					   id="groupPerson_{{ $link->id }}_{{ $group->id }}_opposed"
			        					   wire:model="groupPerson.{{ $link->id }}.{{ $group->id }}.notes"
			        					   placeholder="Notes"
			        					   class="p-1 rounded">
        						</div>

	        					@endif

	        				@endif

	        			</div>
	        		@endforeach
    			</div>
    		@endforeach
    	</div>

	</div>

</div>