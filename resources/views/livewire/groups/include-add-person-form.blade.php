<div class="px-4 py-2 bg-blue-lightest mb-6 border shadow-lg">

	<div class="flex">

		<div class="p-2 font-bold w-32 text-sm whitespace-no-wrap text-blue-dark text-right pt-3">
			Add:
		</div>

		<div class="p-2">
			<div class="text-lg font-bold">
				{{ (gettype($add_person) == 'object') ? $add_person->full_name : $add_person }}
			</div>
			<div class="text-base text-grey-dark">
				{{ (gettype($add_person) == 'object') ? $add_person->full_address : '' }}
			</div>
		</div>

	</div>

	@if($group->cat->has_title)

		<div class="flex">

			<div class="p-2 font-bold w-32 text-sm whitespace-no-wrap text-blue-dark text-right pt-3">
				Title:
			</div>

			<div class="p-2 flex-grow">

				<input type="text"
					   class="border p-2 w-full"
				   	   wire:model.debounce="add_person_title"
				   	   onkeypress="javascript: if(event.keyCode == 13) blur();"
				   	   />
						
			</div>

		</div>

	@elseif($group->cat->has_position)
		<div class="flex">

			<div class="p-2 font-bold w-32 text-sm whitespace-no-wrap text-blue-dark text-right pt-3">
				Position:
			</div>

			<div class="p-2">
				@foreach(['Supports', 'Concerned', 'Opposed', 'Undecided'] as $position)
					<label for="add-position-{{ $position }}" class="mr-2 font-normal">
						<input type="radio"
						   	   name="add-position"
						   	   id="add-position-{{ $position }}"
						   	   wire:model.debounce="add_person_position"
						   	   value="{{ $position }}"
						   	   />
							{{ $position }}
					</label>
				@endforeach
			</div>

		</div>

	@endif

	<div class="flex">

		<div class="p-2 font-bold w-32 text-sm whitespace-no-wrap text-blue-dark text-right pt-3">
			Primary Email:
		</div>

		<div class="p-2 flex-grow">

			<input type="text"
				   class="border p-2 w-full"
			   	   wire:model.debounce="add_person_primary_email"
			   	   onkeypress="javascript: if(event.keyCode == 13) blur();"
			   	   />
					
		</div>

	</div>

	<div class="flex">

		<div class="p-2 font-bold w-32 text-sm whitespace-no-wrap text-blue-dark text-right pt-3">
			Notes:
		</div>

		<div class="p-2 flex-grow">

			<textarea
				   class="border p-2 w-full"
			   	   wire:model.debounce="add_person_notes"
			   	   />
			</textarea> 
					
		</div>

	</div>

	<div class="border-t-2 border-blue text-right py-2">

		<button class="rounded-lg bg-grey-lighter text-gray-darkest px-2 py-1 border"
				wire:click="cancelAddPerson();">
			Cancel
		</button>

		<button class="rounded-lg bg-blue text-white px-2 py-1 border border-transparent"
				wire:click="addPersonFinal();">
			Add
		</button>

	</div>

</div>