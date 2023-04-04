<div class="mb-4">
    <div class="flex">
    	<div class="w-1/3 p-2 font-bold">
    		Add Group:
    	</div>
    	<div class="w-2/3 p-2 flex border-b mb-4">
    		<div class="cursor-pointer" wire:click="toggleExisting()">
	    		<input name="existing" type="radio"
	    			@if ($existing)
	    				checked="checked"
	    			@endif
	    			 /> Existing
	    	</div>
    		<div class="cursor-pointer pl-8" wire:click="toggleExisting()">
	    		<input name="existing" type="radio" 
	    			@if (!$existing)
	    				checked="checked"
	    			@endif
	    			/> New
	    	</div>
    	</div>
    </div>
    <div class="flex">
    	<div class="w-1/3">

    	</div>
    	<div class="w-2/3">
    		@if ($existing)

    			@if (!$existing_group)
    				<input type="text" class="p-2 w-full border mb-1" wire:model="existing_filter" placeholder="Filter groups"/>
    			@endif

	    		<select wire:model="existing_group_id" class="border w-full p-1">
	                <option class="">- Select Group -</option>
	                @foreach (Auth::user()->categories as $category)

	                    <optgroup label="{{ $category->name }}">
	                        @foreach ($category->groups->sortBy('name') as $group)
	                            @if ($group->archived_at)
	                                @continue
	                            @endif

	                            @if ($existing_filter)
		                            @if (stripos('a'.$group->name, $existing_filter) < 1)
		                            	@continue
		                            @endif
	                            @endif

	                            <option value="{{ $group->id }}">
	                                {{ $group->name }} ({{ $group->people_count }})
	                            </option>
	                            

	                        @endforeach
	                    </optgroup>
	                    
	                @endforeach
	            </select>

	            @if ($existing_group)
	            	<div class="mt-4 border-b-2 uppercase text-sm text-gray-400">
	            		{{ $group_person->name }} group info
	            	</div>
	            	@if ($existing_group->category->has_position)
	            		<div class="w-full flex">
	            			<div class="w-1/3 p-2 text-sm text-gray-400">
	            				Position:
	            			</div>
	            			<div class="w-2/3 p-2">
	            				<label class="block"><input type="radio" wire:model="existing_group_position" value="Supports">
	            					Supports
	            				</label>
	            				<label class="block"><input type="radio" wire:model="existing_group_position" value="Concerned">
	            					Concerned
	            				</label>
	            				<label class="block"><input type="radio" wire:model="existing_group_position" value="Opposed">
	            					Opposed
	            				</label>
	            				<label class="block"><input type="radio" wire:model="existing_group_position" value="Undecided">
	            					Undecided
	            				</label>
	            			</div>
	            		</div>
	            	@endif

	            	@if ($existing_group->category->has_title)
	            		<div class="w-full flex">
	            			<div class="w-1/3 p-2 text-sm text-gray-400">
	            				Title:
	            			</div>
	            			<div class="w-2/3 p-2">
	            				<input type="text" class="border-2 p-2 w-full" wire:model="existing_group_title" placeholder="i.e. President"/>
	            			</div>
	            		</div>
	            	@endif

	            	@if ($existing_group->category->has_notes)
	            		<div class="w-full flex">
	            			<div class="w-1/3 p-2 text-sm text-gray-400">
	            				Notes:
	            			</div>
	            			<div class="w-2/3 p-2">
	            				<textarea class="border-2 p-2" rows="4" wire:model="existing_group_notes" placeholder="Notes"></textarea>
	            			</div>
	            		</div>
	            	@endif
	            	
            	@endif

            	@if ($existing_group)
	            	<button wire:click="addToGroup()" class="rounded-full mt-2 bg-blue-400 text-white py-2 px-4 hover:shadow hover:bg-blue-500">
	            		Add {{ $group_person->name }} to Group
	           	@else
	           		<!-- <button disabled class="bg-gray-300 text-white py-2 px-4 rounded-full mt-2">
	           			Create Group and Add {{ $group_person->name }}
	           		</button> -->
	            @endif
            @else

            	<div class="uppercase text-sm text-gray-400">
            		Category
            	</div>
            	<div class="flex w-full items-center">

		        	<select wire:model="new_group_category_id" class="border w-full p-1">
		                <option value="">- Select Category -</option>
		                @foreach (Auth::user()->categories()->whereNull('parent_id')->get()->sortBy('name') as $category)
		                	
	                        <option value="{{ $category->id }}">
	                           	{{ $category->name }} ({{ $category->groups()->notArchived()->count() }})
	                        </option>

	                        @foreach ($category->subCategories()->sortBy('name') as $subcat)
	                        	<option value="{{ $subcat->id }}">
		                           	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;• {{ $subcat->name }} 
		                           	({{ $subcat->groups()->count() }})
		                        </option>
	                        @endforeach
	                        
		                @endforeach
		            </select>
			        
			    </div>

	            @if ($new_group_category)

		            <div class="mt-4 uppercase text-sm text-gray-400">
	            		New Group Name
	            	</div>

	            	<input class="border-2 p-2 w-full" wire:model="new_group_name" placeholder="New group name" type="text" />


	            	@if ($new_group_name)
		            	<div class="mt-4 border-b-2 uppercase text-sm text-gray-400">
		            		{{ $group_person->name }} group info
		            	</div>
		            	@if ($new_group_category->has_position)
		            		<div class="w-full flex">
		            			<div class="w-1/3 p-2 text-sm text-gray-400">
		            				Position:
		            			</div>
		            			<div class="w-2/3 p-2">
		            				<label class="block"><input type="radio" wire:model="new_group_position" value="Supports">
		            					Supports
		            				</label>
		            				<label class="block"><input type="radio" wire:model="new_group_position" value="Concerned">
		            					Concerned
		            				</label>
		            				<label class="block"><input type="radio" wire:model="new_group_position" value="Opposed">
		            					Opposed
		            				</label>
		            				<label class="block"><input type="radio" wire:model="new_group_position" value="Undecided">
		            					Undecided
		            				</label>
		            			</div>
		            		</div>
		            	@endif

		            	@if ($new_group_category->has_title)
		            		<div class="w-full flex">
		            			<div class="w-1/3 p-2 text-sm text-gray-400">
		            				Title:
		            			</div>
		            			<div class="w-2/3 p-2">
		            				<input type="text" class="border-2 p-2 w-full" wire:model="new_group_title" placeholder="i.e. President"/>
		            			</div>
		            		</div>
		            	@endif

		            	@if ($new_group_category->has_notes)
		            		<div class="w-full flex">
		            			<div class="w-1/3 p-2 text-sm text-gray-400">
		            				Notes:
		            			</div>
		            			<div class="w-2/3 p-2">
		            				<textarea class="border-2 p-2" rows="4" wire:model="new_group_notes" placeholder="Notes"></textarea>
		            			</div>
		            		</div>
		            	@endif

		            	
	            	@endif

	            	@if ($new_group_name)
		            	<button wire:click="createGroup()" class="rounded-full mt-2 bg-blue-400 text-white py-2 px-4 hover:shadow hover:bg-blue-500">
		            		Create Group and add {{ $group_person->name }}
		           	@else
		           		<!-- <button disabled class="bg-gray-300 text-white py-2 px-4 rounded-full mt-2">
		           			Create Group and Add {{ $group_person->name }}
		           		</button> -->
		            @endif
	            @endif

	            
            @endif
    	</div>
    </div>
</div>
