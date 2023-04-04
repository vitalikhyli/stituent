<div>

	<!----------------------------------------------------/ /-------------------------------------->

	<div wire:click="toggleOpen('cities');"
		 class="cursor-pointer font-medium border-b-2 bg-green-lightest px-3 py-1 mb-1 hover:shadow-lg hover:border-grey-darker {{ (!$cities->first()) ? 'opacity-50' : '' }}">
		 @if(!$is_open['cities'])
		 	<i class="fas fa-plus-circle mr-1 text-grey-darker"></i>
		 @else
		 	<i class="fas fa-minus-circle mr-1 text-grey-darker"></i>
		 @endif
		Cities & Towns

		 @if(!empty($selected_cities))
			 <span class="float-right text-green-dark">
			 	<i class="fas fa-check-circle text-green-dark"></i>
			 	{{ count($selected_cities) }}
			 </span>
		 @endisset

	</div>

	<div class="{{ (!$is_open['cities']) ? 'hidden' : '' }}  border-dashed border ">

 		<div class="py-1 whitespace-no-wrap flex p-2">

			<input type="text"
				   class="border p-2 rounded-lg w-3/4"
				   wire:model.debounce="lookup_city"
				   wire:key="lookup_city"
				   placeholder="Lookup City"
				   />

			<button wire:click="clearLookup('city')"
					class="rounded-lg bg-grey-lighter text-gray-darker p-2 border ml-1">
				Clear
			</button>

		</div>

		@if(!empty($selected_cities))
			<div class="bg-grey-lightest p-2 border-b-2 mx-2">
				
			    @foreach($selected_cities_chosen as $city)

					<div class="mt-1 ml-2 flex border-grey-lighter w-full"
						 wire:key="city_div-chosen_{{ $city->id }}">

						<label for="selected_cities_{{ $city->id }}" class="font-normal truncate">

							<input id="selected_cities_{{ $city->id }}" 
								   type="checkbox" 
								   wire:model.debounce="selected_cities" 
								   wire:key="city-chosen_{{ $city->id }}" 
								   multiple="multiple" 
								   value="{{ $city->code }}" />
							{{ $city->name }}

						</label>
					</div>
						
					

					

				@endforeach
			</div>

			<div class="py-1 whitespace-no-wrap flex p-2">

				<input type="text"
					   class="border p-2 rounded-lg w-full"
					   wire:model.debounce="precincts"
					   wire:key="precincts"
					   placeholder="Precincts (separate with space)"
					   />

			</div>
		@endif


		

	    @foreach($cities as $city)

			<div class="mt-1 ml-2 flex border-grey-lighter w-full"
				 wire:key="city_div_{{ $city->id }}">

				<label for="selected_cities_{{ $city->id }}" class="font-normal truncate">

					<input id="selected_cities_{{ $city->id }}" 
						   type="checkbox" 
						   wire:model.debounce="selected_cities" 
						   wire:key="city_{{ $city->id }}" 
						   multiple="multiple" 
						   value="{{ $city->code }}" />
					{{ $city->name }}

				</label>

			</div>

		@endforeach

	</div>

	<!----------------------------------------------------/ /-------------------------------------->

	<div wire:click="toggleOpen('zips');"
		 class="cursor-pointer font-medium border-b-2 bg-green-lightest px-3 py-1 mb-1 hover:shadow-lg hover:border-grey-darker">
		 @if(!$is_open['zips'])
		 	<i class="fas fa-plus-circle mr-1 text-grey-darker"></i>
		 @else
		 	<i class="fas fa-minus-circle mr-1 text-grey-darker"></i>
		 @endif
		 Zip Codes

		 @if(!empty($selected_zips))
			 <span class="float-right text-green-dark">
			 	<i class="fas fa-check-circle text-green-dark"></i>
			 	{{ count($selected_zips) }}
			 </span>
		 @endisset

	</div>

	<div class="{{ (!$is_open['zips']) ? 'hidden' : '' }}  border-dashed border ">

 		<div class="py-1 whitespace-no-wrap flex p-2">

			<input type="text"
				   class="border p-2 rounded-lg w-3/4"
				   wire:model.debounce="lookup_zip"
				   wire:key="lookup_zip"
				   placeholder="Lookup Zip"
				   />

			<button wire:click="clearLookup('zip')"
					class="rounded-lg bg-grey-lighter text-gray-darker p-2 border ml-1">
				Clear
			</button>

		</div>

		@if(!empty($selected_zips))
			<div class="bg-grey-lightest p-2 border-b-2 mx-2">

			    @foreach($zips_chosen as $zip)

					<div class="mt-1 ml-2 flex border-grey-lighter w-full"
						 wire:key="zip_div-chosen_{{ $zip }}">

						<label for="selected_zips_{{ $zip }}" class="font-normal truncate">

							<input id="selected_zips_{{ $zip }}" 
								   type="checkbox" 
								   wire:model.debounce="selected_zips" 
								   wire:key="zip-chosen_{{ $zip }}"
								   multiple="multiple" 
								   value="{{ $zip }}" />
							{{ $zip }}

						</label>

					</div>

				@endforeach

			</div>
		@endif

	    @foreach($zips as $zip)

			<div class="mt-1 ml-2 flex border-grey-lighter w-full"
				 wire:key="zip_div_{{ $zip }}">

				<label for="selected_zips_{{ $zip }}" class="font-normal truncate">

					<input id="selected_zips_{{ $zip }}" 
						   type="checkbox" 
						   wire:model.debounce="selected_zips" 
						   wire:key="zip_{{ $zip }}"
						   multiple="multiple" 
						   value="{{ $zip }}" />
					{{ $zip }}

				</label>

			</div>

		@endforeach
	</div>


</div>
