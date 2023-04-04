<div class="mt-1">

	@foreach($district_options as $district_option)

		<div wire:click="toggleOpen('{{ $district_option->code }}');"
			 class="cursor-pointer font-medium border-b-2 bg-red-lightest px-3 py-1 mb-1 hover:shadow-lg hover:border-grey-darker {{ (!$district_option->districts->first()) ? 'opacity-50' : '' }}">
			 @if(!$is_open[$district_option->code])
			 	<i class="fas fa-plus-circle mr-1 text-grey-darker"></i>
			 @else
			 	<i class="fas fa-minus-circle mr-1 text-grey-darker"></i>
			 @endif
			 {{ $district_option->english }}

			 @if($district_option->num_selected  > 0)
				 <span class="float-right text-red">
				 	<i class="fas fa-check-circle text-red"></i>
				 	{{ $district_option->num_selected }}
				 </span>
			 @endisset

		</div>

		<div class="{{ (!$is_open[$district_option->code]) ? 'hidden' : '' }}">

 			<div class="py-1 whitespace-no-wrap flex">

				<input type="text"
					   class="border p-2 rounded-lg w-3/4"
					   wire:model.debounce="lookup_{{ $district_option->code }}"
					   wire:key="search_{{ $district_option->code }}_{{ $loop->index }}"
					   placeholder="Lookup {{ $district_option->english }}"
					   id="search_{{ $district_option->code }}" />

				<button wire:click="clearSearch('{{ $district_option->code }}')"
						class="rounded-lg bg-grey-lighter text-gray-darker p-2 border ml-1">
					Clear
				</button>

			</div>

			@if($district_option->chosen->first())
				<div class="bg-grey-lightest p-2 border-b-2 mx-2">

				    @foreach($district_option->chosen as $district)

						<div class="mt-1 ml-2 flex border-grey-lighter w-full"
							 wire:key="district_div-chosen_{{ $district->id }}">

							<label for="selected_districts_{{ $district->id }}" class="font-normal truncate">

								<input id="selected_districts_{{ $district->id }}" 
									   type="checkbox" 
									   wire:model.debounce="selected_districts" 
									   wire:key="district-chosen_{{ $district->id }}"
									   multiple="multiple" 
									   value="{{ $district->id }}" />

								{{ $district->name }}

								<span class="text-xs truncate text-grey-dark ml-1">
									{{ $district->elected_official_name }}
								</span>

							</label>

						</div>

					@endforeach
				</div>
			@endif

			@foreach($district_option->districts as $district)

				<div class="mt-1 ml-2 flex border-grey-lighter w-full"
					 wire:key="district_div_{{ $district->id }}">

					<label for="selected_districts_{{ $district->id }}" class="font-normal truncate">

						<input id="selected_districts_{{ $district->id }}" 
							   type="checkbox" 
							   wire:model.debounce="selected_districts" 
							   wire:key="district_{{ $district->id }}"
							   multiple="multiple" 
							   value="{{ $district->id }}" />

						{{ $district->name }}

						<span class="text-xs truncate text-grey-dark ml-1">
							{{ $district->elected_official_name }}
						</span>

					</label>

				</div>

			@endforeach

		</div>

	@endforeach

	<script type="text/javascript">
	   // window.livewire.on('focus', function () {
	   //      $("#search_F").focus();
	   //  });
	</script>

</div>