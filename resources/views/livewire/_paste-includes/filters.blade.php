@if (count($visible_chunk) > 0)

<div class="flex w-full">
	<div class="w-48 p-2 uppercase text-sm text-blue-300 font-bold">
		Filters
	</div>

    <div class="m-2 w-3/4">

    	<div class="flex items-center">
			<select wire:model="new_filter_column"  class="border">
				@foreach ($visible_chunk[0] as $index => $val)
					<option value="{{ $index }}">
						{{ $index }} ({{ $val }})
					</option>
				@endforeach
			</select>

			@if($new_filter_column)
				<select wire:model="new_filter_type"  class="border ml-2">
					<option value="">- Select -</option>
					<option value="matches">
						Matches
					</option>
				</select>
			@endif
			@if($new_filter_type)
				<input type="text"  class="border p-2 ml-2" wire:model="new_filter_value" placeholder="Value" />
			@endif
			@if($new_filter_value)
				<button class="border p-2 rounded-lg ml-2 bg-green-lightest" wire:click="addFilter()">
					Add Filter
				</button>
			@endif
		</div>

		@foreach ($filters as $f => $filter_arr)
			<div class="border-b py-2">

				<div wire:click="deleteFilter({{ $f }})" class="w-16 inline text-red mr-4 cursor-pointer">X</div>

				<i class="fas fa-filter w-10"></i>
				<div class="w-6 inline-block text-left">{{ $loop->iteration }}.</div>
				Column <span class="font-bold text-black pr-2">{{ $filter_arr['column'] }}</span>
				{{ $filter_arr['type'] }}
				<span class="font-bold text-black">"{{ $filter_arr['value'] }}"</span>	
			</div>
		@endforeach
		
	</div>
	
</div>

@endif