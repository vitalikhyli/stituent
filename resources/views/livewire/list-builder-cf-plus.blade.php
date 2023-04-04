<div class="flex text-grey-dark text-sm">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['cf_plus']['cell_phones'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Has Cell Phone
					</div>
				@else
					Has Cell Phone
				@endif
			</div>
			<div class="w-2/3">
				<div class="px-2 w-3/4">

					<div class="relative uppercase text-sm pt-2">
						<label for="cell_phones" class="font-normal">
							<input type="checkbox"
								   id="cell_phones"
								   wire:model="input.cf_plus.cell_phones"
								   value="1" 
								    /> <span class="px-2">Yes</span>

						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['cf_plus']['ethnicities'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Ethnicities
					</div>
				@else
					Ethnicities
				@endif
			</div>
			<div class="w-2/3 flex">
				<div class="px-2 w-3/4" wire:ignore>
					<select wire-select-model="input.cf_plus.ethnicities" class="form-control w-full select2" multiple>
						<option value=""></option>
						@foreach ($ethnicities as $ethnicity) 
							<option value="{{ $ethnicity }}">
								{{ $ethnicity }}
							</option>
						@endforeach
					</select>
				</div>
			</div>
		</div>
		

	</div>
</div>