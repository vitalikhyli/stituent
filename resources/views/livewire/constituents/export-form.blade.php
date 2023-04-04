<div>
    
	<div class="px-4 p-2 font-medium flex">

		<div class="p-1">
			{{ $field_count }} {{ Illuminate\Support\Str::plural('Field', $field_count) }} Selected.
		</div>

		<div class="ml-1 p-1">
		@if($field_count < $total_fields)
			<button wire:click="selectAll(true)"
					class="rounded-lg bg-blue text-white px-3 py-1 text-xs">
				All
			</button>
		@else
			<button class="opacity-25 rounded-lg bg-blue text-white px-3 py-1 text-xs">
				All
			</button>
		@endif
		</div>

		<div class="ml-1 p-1">
		@if($field_count > 0)
			<button wire:click="selectAll(false)"
					class="rounded-lg bg-blue text-white px-3 py-1 text-xs">
				None
			</button>
		@else
			<button class="opacity-25 rounded-lg bg-blue text-white px-3 py-1 text-xs">
				None
			</button>
		@endif
		</div>
	</div>

	<div class="flex mt-2 ml-2">

		@foreach($fields->chunk($fields->count() - floor($fields->count()/2)) as $chunk)

			<div class="w-1/2">
					
				@foreach($chunk as $key => $field)

				<div class="border-b py-1">

					<label for="{{ $field[2] }}"
						x-on:click="switchOn = !switchOn"
						class="w-full font-normal flex cursor-pointer">

						<input type="checkbox"
							   wire:model="export_fields.{{ $field[2] }}"
							   id="{{ $field[2] }}"
							   class="hidden" />
							

						<div class="text-center rounded-full {{ ($export_fields[$field[2]]) ? 'bg-blue border-blue' : 'bg-grey-light' }} cursor-pointer border-2 w-16 mx-1" style="height:24px;">
							@if($export_fields[$field[2]])
								<div class="float-right rounded-full px-2 py-1 bg-white border-blue border-2 cursor-pointer" style="width:20px; height:20px;">
									&nbsp;
								</div>	
							@else
								<div class="float-left rounded-full px-2 py-1 bg-white border-2 cursor-pointer" style="width:20px; height:20px;">
									&nbsp;
								</div>
							@endif
						</div>
						
						<div class="px-2">
							{{ $field[1] }}
						</div>

					</label>
				</div>


				@endforeach


			</div>

		@endforeach

	</div>

	<div class="mt-2">
	
		<label for="householding">

		  <input type="checkbox"
		  		 name="householding"
		  		 id="householding"
		  		 wire:model="householding" />

			<span class="ml-2 pb-1 pr-2 font-semibold cursor-pointer text-sm">
				Group by Household
			</span>

		</label>


		<label for="include_voter_phones">

		  <input type="checkbox"
		  		 name="include_voter_phones"
		  		 id="include_voter_phones"
		  		 wire:model="include_voter_phones" />

			<span class="ml-2 pb-1 pr-2 font-semibold cursor-pointer text-sm">
				Include Phones from Voter File
			</span>

		</label>


		@if(!$householding)		

			<div class="pb-1 pr-2 font-semibold cursor-pointer text-sm text-black">
				Include Group Information: 
				<span class="text-sm text-grey-darker">
					Such as Position, Title and Notes

					<select name="include_groups" 
							id="include_groups" 
							wire:model="include_groups">
						<option value=""> -- Do not Include -- </option>
						<option value="specific">...for the groups you selected</option>
						<option value="all">...for ALL your groups</option>
					</select>

				</span>
			</div>

		@endif
		



	</div>

	<div class="p-6 flex">

		<div class="flex-grow"></div>

		<div class="flex bg-grey-lighter p-4 border-b w-3/4">

			<div class="mr-2 flex-grow">
				<input type="text"
					   wire:model="filename"
					   placeholder="YourFileName.csv"
					   class="p-2 border w-full font-medium" />
			</div>


			<button wire:click="triggerDownload()"
					wire:loading.class="opacity-25 transition ease-in-out"
					class="rounded-lg bg-blue text-white px-4 py-2 text-xl">
				Download CSV Now
			</button>

		</div>

		<div class="flex-grow"></div>

	</div>


</div>
