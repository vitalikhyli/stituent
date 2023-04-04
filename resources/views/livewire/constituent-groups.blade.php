<div class="p-1" wire:loading.class="opacity-50">

	<div class="flex">
		<div class="w-1/2">
			<div class="">
				<a href="/{{ $person->team->app_type }}/constituents/{{ $person->id }}" class="hover:font-bold">
					<i class="fas fa-link mr-1 text-grey"></i><i class="fas fa-user mr-2 text-grey-dark"></i> {{ $person->full_name }}
				</a>
			</div>
		</div>

		<div class="w-1/2">
			<select wire:model="new_group" class="border w-72">
				<option class="">- Add Group -</option>
				@foreach (Auth::user()->categories as $category)
					<optgroup label="{{ $category->name }}">
						@foreach ($category->groups->sortBy('name') as $group)
							<option value="{{ $group->id }}">
								{{ $group->name }}
							</option>
						@endforeach
					</optgroup>
				@endforeach
			</select>
		</div>
	</div>



    @foreach ($person->groups()->get() as $group)
    	<div class="ml-6">
    	 	<i class="fa fa-tag text-blue-400"></i>
    	 	{{ $group->name }}
    	 </div>
    @endforeach

</div>
