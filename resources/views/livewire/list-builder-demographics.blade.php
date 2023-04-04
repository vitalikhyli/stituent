<div class="flex text-grey-dark text-sm">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['age'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Age
					</div>
				@else
					Age
				@endif
			</div>
			<div class="w-2/3">
				<div class="w-3/4 flex px-2">
					<div class="w-1/2 pr-1">
						<select wire:model="input.age_operator" class="form-control">

								<option selected="selected" value=""> ~ Select ~ </option>
								<option value="="> = Equal To</option>
								<option value=">"> &gt; Greater Than</option>
								<option value="<"> &lt; Less Than</option>
								<option value="RANGE">X-X Between</option>
								<option value="UNKNOWN">? Is Unknown</option>

						</select>
					</div>
					<div class="w-1/2 pl-1">
						@if ($input['age_operator'])
							<input type="text"
								   wire:model="input.age" 
								   
								   placeholder="@lang('Age / Range')" 
								   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black font-bold focus:border-2" />
						@else
							<div class="float-right mt-1">
								<i class="fa fa-arrow-left pt-2"></i> Choose an operator
							</div>
						@endif
						
					</div>
				</div>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['gender'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Gender
					</div>
				@else
					Gender
				@endif
			</div>
			<div class="w-2/3 flex">
				<div class="px-2 w-3/4">
					<select wire:model="input.gender" class="form-control w-full">
						<option value=""></option>
						<option value="F">F</option>
						<option value="M">M</option>
						<option value="X">X</option>
						<option value="BLANK">BLANK</option>
					</select>
				</div>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['parties'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Parties
					</div>
				@else
					Parties
				@endif
			</div>
			<div class="w-2/3 flex">
				<div class="px-2 w-3/4" wire:ignore>
					<select wire-select-model="input.parties" class="select2" multiple>
						<option value=""></option>
						<option value="U">
							Unenrolled
						</option>
						<option value="D">
							Democratic
						</option>
						<option value="R">
							Republican
						</option>
						<option value="L">
							Libertarian
						</option>
						<option value="O">
							Other
						</option>
						<option value="NONE">
							None
						</option>
					</select>
				</div>
			</div>
		</div>

		@if($input['weeks_registered'])
			<!-- Weeks Registered is Now Defunct -->
			<div class="flex pt-1">
				<div class="w-1/3 uppercase text-sm pt-2 relative">
					@if ($input['weeks_registered'])
						<div class="absolute pin-l -ml-6 text-base text-blue-light">
							<i class="fa fa-check-circle"></i>
						</div>
						<div class="font-bold text-blue">
							Weeks Registered
						</div>
					@else
						Weeks Registered
					@endif
				</div>
				<div class="w-2/3 flex">
					<div class="px-2 w-3/4">
						<select wire:model="input.weeks_registered" class="form-control w-full">
							<option value=""></option>
							@for($w = 1; $w <= 52; $w++)
								<option value="{{ $w }}">
									< {{ $w }} week{{ ($w > 1) ? 's' : '' }} (registered after {{ \Carbon\Carbon::today()->subWeeks($w)->format('n/j/y') }})
								</option>
							@endfor
						</select>
					</div>
				</div>
			</div>
		@endif

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['registered_from'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Registered On or After
					</div>
				@else
					Registered On or After
				@endif
			</div>
			<div class="w-2/3 flex">
				<div class="px-2 w-3/4">

					<select wire:model="input.registered_from" class="form-control w-full">
						<option value=""></option>
						@for($y = 1; $y <= 24; $y++)
							<option value="{{ \Carbon\Carbon::today()->firstOfMonth()->subMonths($y)->toDateString() }}">
								{{ \Carbon\Carbon::today()->firstOfMonth()->subMonths($y)->format('F 1, Y') }}
							</option>
						@endfor
					</select>

					<input type="text"
						   wire:model="input.registered_from"
						   placeholder="{{ \Carbon\Carbon::today()->firstOfMonth()->subMonths($y)->toDateString() }}"
						   class="hidden p-2 border border-grey rounded mt-1" />

				</div>
			</div>
		</div>

		<div class="flex">
		<div class="w-1/3 uppercase text-sm pt-2 relative">
				
			</div>
			<div class="w-2/3 flex">
				<div class="px-2 w-3/4" wire:ignore>

					<div class="relative uppercase text-sm pt-2">
						<label for="include_archived" class="font-normal">
							<input type="checkbox"
								   id="include_archived"
								   wire:model="input.include_archived"
								   value="1" 
								    /> <span class="px-2">Include Archived Voters?</span>

						</label>
					</div>
				</div>
			</div>

		</div>
		

	</div>
</div>