<div class="flex text-grey-dark">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['congressional_districts'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Congressional Districts
					</div>
				@else
					Congressional Districts
				@endif
			</div>
			<div class="w-2/3" wire:ignore>
				<select id="select2-congressional-districts" wire-select-model="input.congressional_districts" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($congressional_districts as $district)
						<option value="{{ $district->id }}">{{ $district->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['senate_districts'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Senate Districts
					</div>
				@else
					Senate Districts
				@endif
				
			</div>
			<div class="w-2/3" wire:ignore>
				<select id="select2-senate-districts" wire-select-model="input.senate_districts" name="senate_districts" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($senate_districts as $district)
						<option value="{{ $district->id }}">{{ $district->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['house_districts'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						House Districts
					</div>
				@else
					House Districts
				@endif
			</div>
			<div class="w-2/3" wire:ignore>
				<select id="select2-house-districts" wire-select-model="input.house_districts" name="house_districts" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($house_districts as $district)
						<option value="{{ $district->id }}">{{ $district->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<!-- <div class="w-full border-b mt-3 mb-2 border-b-grey"></div> -->

		<div class="flex pt-1">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['zipcodes'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Zip Codes
					</div>
				@else
					Zip Codes
				@endif
			</div>
			<div class="w-2/3" wire:ignore>
				<select id="select2-zipcodes" wire-select-model="input.zipcodes" name="zipcodes" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($zipcodes as $zipcode)
						<option value="{{ $zipcode }}">{{ $zipcode }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['municipalities'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Cities & Towns
					</div>
				@else
					Cities & Towns

					<div class="text-xs italic py-2 pr-2 text-blue-light normal-case">
						Choose a municipality to narrow by ward, precinct and street
					</div>

				@endif
			</div>
			<div class="w-2/3" wire:ignore>
				<select id="select2-municipalities" wire-select-model="input.municipalities" name="municipalities" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($municipalities as $municipality)
						<option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<!-- <div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2">
				Neighborhoods
			</div>
			<div class="w-2/3">
				<select class="select2">
					<option value=""></option>
				</select>
			</div>
		</div> -->


		@if ($input['municipalities'])

			<div class="mt-2">

				@foreach ($municipalities as $municipality)
					@if (in_array($municipality->id, $input['municipalities']))

						<div class="flex text-sm">
							<div class="w-1/3 uppercase flex">
								<div class="w-2/5">
								</div>
								<div class="w-3/5 border-b">
									{{ $municipality->name }}
								</div>
							</div>
							<div class="w-2/3 flex border-b text-grey">
					
								<div class="w-1/6 text-sm">
									<label class="font-normal ml-2">
										<input wire:model="input.municipalities_narrow.{{ $municipality->id }}" checked="checked" type="radio" name="municipality_{{ $municipality->id }}" value="all"/> All
									</label>
								</div>
								<div class="w-5/6">
									<label class="font-normal">
										<input wire:model="input.municipalities_narrow.{{ $municipality->id }}" type="radio" name="municipality_{{ $municipality->id }}" value="narrow"/> Narrow by ward, precinct, street
									</label>
								</div>
							</div>
						</div>
						<div class="flex text-sm">
							<div class="w-1/3">
							</div>
							<div class="w-2/3 flex">

								<div class="w-1/6"></div>
								<div class="w-5/6 pt-1 pl-4">

									@isset ($input['municipalities_narrow'][$municipality->id])
										@if ($input['municipalities_narrow'][$municipality->id] == 'narrow')

											@foreach ($municipality->wards as $ward => $precincts)
												@if (!$ward)
													<div class="flex text-sm">
														<div class="w-1/4">
															<div><label class="font-normal">Precincts: </label></div>
														</div>
														<div class="w-3/4 flex flex-wrap pl-2">
															@foreach ($precincts as $precinct => $dud)
																<div class="w-1/4">
																	<label class="font-normal"><input type="checkbox" wire:model="input.wards.{{ $municipality->id }}_{{ $ward }}_{{ $precinct }}" value="{{ $municipality->id }}_{{ $ward }}_{{ $precinct }}"> {{ $precinct }}</label>
																</div>
															@endforeach
														</div>
													</div>
												@else 
													<div class="flex text-sm">
														<div class="w-1/4">
															<div><label class="font-normal">Ward {{ $ward }}:</label></div>
														</div>
														<div class="w-3/4 flex flex-wrap pl-2">
															@foreach ($precincts as $precinct => $dud)
																<div class="w-1/4"><label class="font-normal"><input type="checkbox" wire:model="input.wards.{{ $municipality->id }}_{{ $ward }}_{{ $precinct }}" value="{{ $municipality->id }}_{{ $ward }}_{{ $precinct }}"> {{ $ward }}-{{ $precinct }}</label></div>
															@endforeach
														</div>
													</div>
												@endif
											@endforeach

											@php
												$streets = $municipality->streetsByWardsPrecincts($input['wards']);
											@endphp

											
											<div class="flex border-t pt-2 pb-4">
												<div class="w-1/4">
													<div><label class="font-normal text-sm mt-1">
														<span class="font-bold text-blue-500">
															{{ $streets->count() }}
														</span> Streets: </label></div>
												</div>
												<div class="w-3/4 pl-2">

													@isset($input['streets'][$municipality->id])
														@foreach ($input['streets'][$municipality->id] as $street_slug => $street)



															<div class="flex items-center mb-2">
																<div class="text-blue font-bold w-3/5">
																	{{ $street['name'] }}
																	<i class="fa fa-times-circle text-grey hover:text-red cursor-pointer" wire:click="removeStreet({{ $municipality->id }}, '{{ $street_slug }}')"></i>
																</div>
																<div class="w-1/5">
																	
																	<input wire:model.debounce.500ms="input.streets.{{ $municipality->id }}.{{ $street_slug }}.from" type="text" class="bg-grey-lightest w-full h-6 p-1" placeholder="From" />
																</div>
																<div class="w-1/5 pl-2">
																	
																	<input wire:model.debounce.500ms="input.streets.{{ $municipality->id }}.{{ $street_slug }}.to" type="text" class="bg-grey-lightest w-full h-6 p-1" placeholder="To" />
																</div>
															</div>
																
															
														@endforeach

													@endisset

													<div>


														<select wire:model="input.new_streets.{{ $municipality->id }}" placeholder="New Street" class="form-control">
															<option value=""></option>
															@foreach ($streets as $street)
																<option value="{{ $street }}">
																	{{ $street }}
																</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>

										@endif
									@endisset
								</div>
							</div>
						</div>
					@endif
				@endforeach
				@foreach ($input['wards'] as $cwp => $on)
					@if ($on)
						<!-- {{ $cwp }} -->
					@endif
				@endforeach
			</div>
		@endif

	</div>
</div>