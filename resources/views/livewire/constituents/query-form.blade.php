<div class="flex">

    

	<div class="w-3/4 pr-4">

		<div class="w-full">

			<div class="font-mono text-xs py-1 flex">

				<div class="py-1">

					Loaded in {{ number_format($time, 3) }} seconds

					@if(Auth::user()->permissions->developer  && 1 == 'tuna fish')
						/ loaded {{ $loaded_times }} times
					@endif

				</div>

				@if(!$untouched)
					<div class="flex-grow text-right">
						<button wire:click="$emit('clear_all')" class="bg-grey-lightest border text-xs py-1 px-2">
							<i class="fas fa-times text-red"></i> Clear Filters
						</button>
					</div>
				@endif

			</div>

			<div class="p-2 bg-blue-lighter flex text-right w-full">

				<div class="mx-1 text-left flex-grow">
					@if($total_count_people)
						<span class="text-blue text-xs">{{ number_format($total_count_people) }} linked </span> 
					@endif
					@if($total_count_people && $total_count_voters)
						<span class="text-blue text-xs">+</span>
					@endif
					@if($total_count_voters)
						<span class="text-blue text-xs">{{ number_format($total_count_voters) }} others </span>
					@endif
				</div>

				<div class="mx-1 text-right">
					<button class="rounded-full px-4 py-1 text-xs uppercase
									@if($export_mode)
										bg-blue text-white opacity-25
									@else
										bg-blue text-white
									@endif
									"
							wire:click="$toggle('export_mode')">
						<i class="fa fa-file-csv"></i> Export
					</button>
				</div>

				<div class="mx-1 text-right">
					<button class="rounded-full px-4 py-1 text-xs uppercase
									@if($export_mode)
										bg-blue text-white opacity-25
									@else
										bg-blue text-white
									@endif
									"
							wire:click="printLabels()">
						<i class="fa fa-envelope"></i> Labels
					</button>
				</div>

				<div class="mx-1 text-right">
					<button class="rounded-full px-4 py-1 text-xs uppercase
									@if($export_mode)
										bg-blue text-white opacity-25
									@else
										bg-blue text-white
									@endif
									"
							wire:click="printHouseholdLabels()">
						<i class="fa fa-envelope"></i> Household Labels
					</button>
				</div>

				<div class="mx-1 text-right">
					<button class="rounded-full px-4 py-1 text-xs uppercase
									@if(!$export_mode)
										bg-blue text-white opacity-25
									@else
										bg-blue text-white
									@endif
									"
							wire:click="$toggle('export_mode')">
						<i class="fa fa-user"></i> Display
					</button>
				</div>
			</div>


			<div class="border-b bg-grey-lighter uppercase p-2">

				<span class="font-bold text-xl pl-2 the_count" wire:loading.class="opacity-25 transition ease-in-out">
					{{ number_format($total_count) }}
				</span> 

				<span wire:loading.remove>
					@if($total_count == 1)
						@lang('Constituent')
					@else
						@lang('Constituents')
					@endif
				</span>

				<span wire:loading class="">
			        Loading...
			    </span>

			</div>
			
			
			@if($export_mode)

				@livewire('constituents.export-form', key('export-form'))

			@else

				@include('shared-features.constituents.livewire-list')

			@endif

		</div>

	</div>


	<div class="py-2 text-sm w-1/4">

		<!------------------------------------------------------------/ /---------------------->

		<div class="flex w-full mt-2">

			<div class="text-right text-xs flex-grow pt-2 pr-1">
				Everyone
			</div>

			<div wire:click="toggleLinked()" class="text-center mb-2 rounded-full {{ ($linked) ? 'bg-blue border-blue' : 'bg-grey-light' }} border-2 w-1/4 mx-1">
				@if($linked)
					<button class="float-right rounded-full h-8 w-8 px-2 py-1 bg-white border-blue border-2 cursor-pointer">
						&nbsp;
					</button>	
				@else
					<button class="float-left rounded-full h-8 w-8 px-2 py-1 bg-white border-2 cursor-pointer">
						&nbsp;
					</button>
				@endif
			</div>

			<div class="text-left text-xs flex-grow pt-2 pl-1">
				Linked Only
			</div>

		</div>

		<!------------------------------------------------------------/ /---------------------->

	  	<div class="py-1">

			<div class="flex w-full">
				<div class="w-3/6">
					<div class="">
						<input wire:model.debounce.500ms="first_name" type="text"
							   name="first_name" 
							   id="first_name" 
							   autocomplete="off"
							   placeholder="@lang('First Name')" 
							   class="text-input w-full appearance-none px-4 py-3 bg-grey-lighter border border-grey text-black focus:border-2 text-lg" />
					</div>
				</div>

				@if($first_name && $last_name)
					<div class="w-16" id="middle_name_div">
						<input wire:model.debounce.500ms="middle_name" type="text"
							   name="middle_name" 
							   id="middle_name" 
							   autocomplete="off"
							   placeholder="Middle" 
							   class="w-full appearance-none px-1 py-3 bg-grey-lighter border-t border-b border-r border-grey text-black focus:border-2 text-lg" />
					</div>
				@endif

				<div class="w-3/6">
					<div class="">
						<input wire:model.debounce.500ms="last_name" type="text"
							   name="last_name" 
							   id="last_name"
							   autocomplete="off"
							   placeholder="@lang('Last Name')" 
							   class="text-input w-full appearance-none px-4 py-3 bg-grey-lighter border-t border-b border-r border-grey text-black focus:border-2 text-lg" />
					</div>
				</div>
			</div>

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="py-1">

			<input 
			   wire:model.debounce.500ms="email" type="text"
			   name="email" 
			   autocomplete="off"
			   placeholder="@lang('Email')" 
			   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="py-1 text-center -mb-1">
			<label for="master_email" class="font-normal">
				<input type="checkbox"
					   wire:model="master_email"
					   id="master_email"
					   class="mr-2" />

				Master Email List
				
			</label>
		</div>

		<!------------------------------------------------------------/ /---------------------->

		@livewire('constituents.groups-form', key('groups'))


		<!------------------------------------------------------------/ /---------------------->

		<div class="py-1 text-sm text-center -mb-1">

			@foreach(['Dem' => 'D', 'Rep' => 'R', 'Ind' => 'U'] as $label => $party)
				<label for="{{ $party }}" class="font-normal whitespace-no-wrap">
					<div class="inline px-3 {{ (!$loop->last) ? 'border-r border-lightest' : '' }}">
						<input id="{{ $party }}" multiple="multiple" name="parties[]" type="checkbox" wire:model="parties" value="{{ $party }}" />
						<span class="ml-1">{{ $label }}</span>
					</div>
				</label>
			@endforeach

		</div>

		<!------------------------------------------------------------/ /---------------------->

		@livewire('constituents.districts-form', key('districts'))

		<!------------------------------------------------------------/ /---------------------->

		<div class="py-1 mt-2">

			<input 
			   wire:model.debounce.500ms="street" type="text"
			   name="email" 
			   autocomplete="off"
			   placeholder="@lang('Street Address')" 
			   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="mt-1">

			@livewire('constituents.municipalities-form', key('municipalities'))

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="flex items-center">
			<div class="">
				Age
			</div>
			<div class="px-2">
				<select wire:model="age_operator">

					@if(!isset($input['age_operator']))

						<option selected="selected" value=""><i>~ Function ~</i></option>
						<option value="=">Equal To</option>
						<option value=">">Greater Than</option>
						<option value="<">Less Than</option>
						<option value="RANGE">Between (X-X)</option>
						<option value="UNKNOWN">Is Unknown</option>

					@else

						<option {{ selectedIfValueIs("", $input, 'age_operator') }} value=""><i>~ Function ~</i></option>
						<option {{ selectedIfValueIs("=", $input, 'age_operator') }} value="=">Equal To</option>
						<option {{ selectedIfValueIs(">", $input, 'age_operator') }} value=">">Greater Than</option>
						<option {{ selectedIfValueIs("<", $input, 'age_operator') }} value="<">Less Than</option>
						<option {{ selectedIfValueIs("RANGE", $input, 'age_operator') }} value="RANGE">Between (X-X)</option>
						<option {{ selectedIfValueIs("UNKNOWN", $input, 'age_operator') }} value="UNKNOWN">Is Unknown</option>

					@endif

				</select>
			</div>
			<div class="w-1/2">
				<input 
			   wire:model="age" type="text"
			   placeholder="@lang('Age / Range')" 
			   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2 my-2" />
			</div>

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="mt-1">

			@livewire('constituents.emails-form', key('emails'))

		</div>

		<!------------------------------------------------------------/ /---------------------->


		<div class="flex py-2 items-center w-full border-t-2 mt-2">
			<div class="pr-4">
				<select query_form="true" wire:model="order_by">
					<option value="last_name">Sort By Last Name</option>
					<option value="dob">Sort By Age</option>
					<option value="household_id">Sort By Address</option>
				</select>
			</div>
			<div class="text-xs text-center">
				<div class="text-left">
					<label class="radio-inline" for="order_direction_asc">
						<input id="order_direction_asc" type="radio" wire:model="order_direction" value="asc">ASC
					</label>
				</div>
				<div class="text-left">
					<label class="radio-inline" for="order_direction_desc">
						<input id="order_direction_desc" type="radio" wire:model="order_direction" value="desc" >DESC
					</label>
				</div>
			</div>
		</div>

		<div class="py-1">

			<select wire:model="per_page" class="w-full">
				@foreach([100, 200, 400, 500, 600, 700, 800, 900, 1000] as $amount)
					<option value="{{ $amount }}">
					{{ $amount }} per page</option>
				@endforeach
			</select>

		</div>

		<!------------------------------------------------------------/ /---------------------->

		<div class="flex w-full mt-2">

			<div class="text-right text-xs w-32 pt-2 pr-1">
				Include Archived?
			</div>

			<div wire:click="toggleArchive()" class="text-center mb-2 rounded-full {{ (!$ignore_archived) ? 'bg-blue border-blue' : 'bg-grey-light' }} border-2 w-1/4 mx-1">
				@if(!$ignore_archived)
					<button class="float-right rounded-full h-8 w-8 px-2 py-1 bg-white border-blue border-2 cursor-pointer">
						&nbsp;
					</button>	
				@else
					<button class="float-left rounded-full h-8 w-8 px-2 py-1 bg-white border-2 cursor-pointer">
						&nbsp;
					</button>
				@endif
			</div>


		</div>

		<!------------------------------------------------------------/ /---------------------->

<!-- 		<div class="flex w-full mt-2">

			<div class="text-right text-xs w-32 pt-2 pr-1">
				Include Deceased?
			</div>

			<div wire:click="toggleDeceased()" class="text-center mb-2 rounded-full {{ (!$ignore_deceased) ? 'bg-blue border-blue' : 'bg-grey-light' }} border-2 w-1/4 mx-1">
				@if(!$ignore_deceased)
					<button class="float-right rounded-full h-8 w-8 px-2 py-1 bg-white border-blue border-2 cursor-pointer">
						&nbsp;
					</button>	
				@else
					<button class="float-left rounded-full h-8 w-8 px-2 py-1 bg-white border-2 cursor-pointer">
						&nbsp;
					</button>
				@endif
			</div>


		</div>
 -->

		<!------------------------------------------------------------/ /---------------------->

	</div>

</div>