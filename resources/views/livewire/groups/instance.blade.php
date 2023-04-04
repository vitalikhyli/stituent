<div>

	<div class="flex py-2 border-b border-grey-light border-dashed text-sm cursor-pointer
				@if(isset($was_just_created))
					bg-blue-lightest
				@endif
				">

		<div class="w-6 whitespace-no-wrap truncate px-1">

		    

	    </div>

		<div class="w-1/4 whitespace-no-wrap truncate px-1">
	    	<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $instance->person->id }}"
	    	   class="text-blue">
	    		{{ $instance->person->full_name }}

			    <div class="whitespace-no-wrap truncate text-grey-dark text-xs">
				    {{ $instance->person->full_address }}
				</div>
			</a>
	    </div>

	    <div class="w-1/5 whitespace-no-wrap truncate px-1">
	    	<div wire:key="primary_email_{{ $instance->id }}">
			    <input type="text"
			    	   placeholder="Primary Email" 
			    	   class="focus:border px-2 py-1 border-grey w-full"
			    	   wire:dirty.class="bg-yellow-light"
			    	   wire:model.lazy="primary_email"
			    	   onkeypress="javascript: if(event.keyCode == 13) blur();"
			    	   />

			   	@if($just_changed['primary_email'] != null)
			   		<div class="pt-1 text-xs text-blue" wire:poll.5000ms>
			   			<i class="fas fa-check-circle"></i> Updated
			   		</div>
			   	@endif
			</div>

			<div wire:key="group_email_{{ $instance->id }}">

				<div class="{{ ($show_group_email) ? 'hidden' : '' }} mt-1 text-grey-dark text-xs"
					 wire:click="$toggle('show_group_email')">
					+ Group
				</div>

		    	<div class="{{ (!$show_group_email) ? 'hidden' : '' }} mt-1 w-full">
		    		<div class="flex">
			    		<div class="w-4 py-1 text-sm text-grey-dark">
			    			<i class="fas fa-tag mr-1"></i>
			    		</div>
			    		<div class="flex-shrink">
						    <input type="text"
						    	   placeholder="Group Email" 
						    	   class="border px-2 py-1 border-grey w-full"
						    	   wire:dirty.class="bg-yellow-light"
						    	   wire:model.lazy="group_email"
						    	   onkeypress="javascript: if(event.keyCode == 13) blur();"
						    	   />
						</div>
					</div>

				   	@if($just_changed['group_email'] != null)
				   		<div class="pt-1 text-xs text-blue" wire:poll.5000ms>
				   			<i class="fas fa-check-circle"></i> Updated
				   		</div>
				   	@endif
				</div>

			</div>

		</div>

		@if($instance->group->cat->has_position)

			<div wire:key="position_{{ $instance->id }}"
				 class="w-1/6 whitespace-no-wrap truncate px-1">

				<select wire:model.lazy="position"
						class="border border-grey text-sm px-2 py-1">
					<option value="">
						-- NONE --
					</option>
					@foreach(['Supports', 'Concerned', 'Opposed', 'Undecided'] as $option)
						<option value="{{ $option }}">
							{{ $option }}
						</option>
					@endforeach
				</select>

			   	@if($just_changed['position'] != null)
			   		<div class="pt-1 text-xs text-blue" wire:poll.5000ms>
			   			<i class="fas fa-check-circle"></i> Updated
			   		</div>
			   	@endif

			</div>

		@endif

		@if($instance->group->cat->has_title)

		    <div wire:key="title_{{ $instance->id }}"
		    	 class="w-1/5 whitespace-no-wrap truncate px-1">
			    <input type="text"
			    	   placeholder="Title" 
			    	   class="border-b px-2 py-1 border-grey"
			    	   wire:dirty.class="bg-yellow-light"
			    	   wire:model.lazy="title"
			    	   onkeypress="javascript: if(event.keyCode == 13) blur();"
			    	   />

			   	@if($just_changed['title'] != null)
			   		<div class="pt-1 text-xs text-blue" wire:poll.5000ms>
			   			<i class="fas fa-check-circle"></i> Updated
			   		</div>
			   	@endif

			</div>

		@endif


		    <div wire:key="notes_{{ $instance->id }}"
		    	 class="flex-grow whitespace-no-wrap truncate px-1">
			    <textarea placeholder="Notes" 
			    	   	  class="border px-2 py-1 border-grey w-full"
			    	   	  wire:dirty.class="bg-yellow-light"
			    	      wire:model.lazy="notes">
			    </textarea>

			   	@if($just_changed['notes'] != null)
			   		<div class="pt-1 text-xs text-blue" wire:poll.5000ms>
			   			<i class="fas fa-check-circle"></i> Updated
			   		</div>
			   	@endif

			</div>

	    <div class="w-24 whitespace-no-wrap truncate px-1 text-xs text-right">

          @if($instance->user_who)
            <div class="text-grey-dark">{{ $instance->user_who }}</div>
          @endif

            <div class="text-grey">
              {{ \Carbon\Carbon::parse($instance->user_when)->format("n/j/y") }}
            </div>

        </div>

        <div class="w-6">
	        <div class="whitespace-no-wrap truncate text-grey hover:text-red text-sm text-center" onclick="confirm('Are you sure you want to remove the person from this group?') || event.stopImmediatePropagation()"
			    	 wire:click="removePersonFinal('{{ $instance->person->id }}')">
				    <i class="fa fa-times-circle"></i>
				</div>
		</div>


	</div>

</div>
