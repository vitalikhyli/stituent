<div class="mt-2">

	<div class="w-full mr-2">

		<div class="font-semibold p-2 bg-grey-lighter border-b-2 border-grey-dark mb-2 flex">

			<div class="flex-grow pt-2">
				Choose Fieldss
			</div>

			<div class="w-1/3 text-right pt-1">

				<label for="all_fields">
					<div class="ml-2 pb-1 pr-2 font-semibold cursor-pointer text-sm text-blue">
						Select All
					</div>
				</label>

				<label class="switch">
					<div>
						<input query_form="true" type="checkbox" id="all_fields" class="">
						<span class="slider round"></span>
					</div>
				</label>

			</div>

		</div>

		<div class="w-full flex">

			@foreach($fields->chunk($fields->count() - floor($fields->count()/2)) as $chunk)

				<div class="w-1/2">

					@foreach($chunk as $key => $thefield)
						<div class="field-selector border-b pt-1 flex w-full">
							<label class="switch">
								<div>
								<input query_form="true" type="checkbox" name="fields[]" id="field_{{ $thefield[2] }}" class="field" value="{{ $thefield[2] }}" {{ checkedIfInArray($thefield[2], $input, 'fields') }}>
								<span class="slider round"></span>
								</div>
							</label>
							<label for="field_{{ $thefield[2] }}">
								<div class="ml-2 pt-1 float-right font-normal cursor-pointer text-sm">
									{{ $thefield[1] }}
								</div>
							</label>
						</div>
					@endforeach

				</div>

			@endforeach



		</div>


		<div class="mt-2">
		
			<div class="pb-1 pr-2 font-semibold cursor-pointer text-sm text-black">
				Include Group Information
				<div class="text-sm text-grey-darker">
					Such as Position, Title and Notes

					<select name="include_groups" id="include_groups" query_form="true">
						<option {{ SelectedIfValueIs("", $input, "include_groups") }} value=""> -- Do not Include -- </option>
						<option {{ SelectedIfValueIs("specific", $input, "include_groups") }} value="specific">...for the groups you selected</option>
						<option {{ SelectedIfValueIs("all", $input, "include_groups") }} value="all">...for ALL your groups</option>
					</select>

				</div>
			</div>
			
		</div>


		<div class="font-semibold border-b-2 border-grey-dark mb-2 mt-6 flex">

			<div class="flex-grow pt-2">
				Options <span class="text-blue">- Beta</span>
			</div>

			<div class="w-1/2 text-right pt-1">

				<div>
					<label for="householding">
						<div class="ml-2 pb-1 pr-2 font-semibold cursor-pointer text-sm text-blue">
							Group by Household
						</div>
					</label>
					<label class="switch">
					  <input query_form="true" type="checkbox" name="householding" id="householding" {{ checkedIfValueIs("on", $input, "householding") }} >
					  <span class="slider round"></span>
					</label>
				</div>

				<div>
					<label for="include_voter_phones">
						<div class="ml-2 pb-1 pr-2 font-semibold cursor-pointer text-sm text-blue">
							Include Phones from Voter File
						</div>
					</label>
					<label class="switch">
					  <input query_form="true" type="checkbox" name="include_voter_phones" id="include_voter_phones" {{ checkedIfValueIs("on", $input, "include_voter_phones") }} >
					  <span class="slider round"></span>
					</label>
				</div>


			</div>

		</div>

			

	</div>

</div>