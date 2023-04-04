<form id="advanced_form" action="/{{ Auth::user()->team->app_type }}/lists/" method="POST">
@csrf

	<div class=" text-xl pb-2">

		<div id="saved_lists" class="float-right text-base">
			
			<div class="flex items-center whitespace-no-wrap"> 



				<div id="toggle_save" class="flex-1 mx-1 text-blue-dark cursor-pointer text-left">
					Save as...
				</div>
				<div id="save_form_div" class="hidden flex-1 text-blue-dark cursor-pointer text-left">
					
					<input type="text" name="save_query_as" placeholder="Save Query as..." class="border border-grey rounded-lg p-1 px-2 text-black" />

				</div>

			
			</div>

			
		</div>


	</div>

	<div class="text-sm py-2 px-4 bg-orange-lightest text-blue-darker mb-2 inline-block w-full rounded-b-lg shadow">

		<!-- Start form minimize interior -->
		<div id="advanced_form_interior" class="{{ (Auth::user()->getMemory('campaign_participant_form', 'max') == 'min') ? 'hidden' : '' }}">

	    <input type="hidden" name="mode_advanced" value="mode_advanced" />

	    <label for="scope_1" class="font-normal">
	    	<input {{ checkedIfValueIs('both', $_GET, 'scope', true) }} type="radio" name="scope" value="both" id="scope_1" /> Include Voters Not in my Database
	    </label>
	    <label for="scope_0" class="font-normal ml-2">
	    	<input {{ checkedIfValueIs('participants_only', $_GET, 'scope') }} type="radio" name="scope" value="participants_only" id="scope_0" /> My Participants Only
	    </label>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				Support
			</div>
			<select name="support-equate" class="">
				<option {{ selectedIfValueIs('eq', $_GET, 'support-equate') }} value="eq">
					is
				</option>
				<option {{ selectedIfValueIs('lt', $_GET, 'support-equate') }} value="lt">
					Better than
				</option>
				<option {{ selectedIfValueIs('gt', $_GET, 'support-equate') }} value="gt">
					Worse than
				</option>
			</select>
			<select name="support" class="ml-2">
				<option {{ selectedIfValueIs('', $_GET, 'support') }} value="">
					-- Ignore --
				</option>
				<option {{ selectedIfValueIs('any', $_GET, 'support') }} value="any">
					Anything
				</option>
				<option {{ selectedIfValueIs(1, $_GET, 'support') }} value="1">
					1 - Yes
				</option>
				<option {{ selectedIfValueIs(2, $_GET, 'support') }} value="2">
					2 - Lean Yes
				</option>
				<option {{ selectedIfValueIs(3, $_GET, 'support') }} value="3">
					3 - Undecided
				</option>
				<option {{ selectedIfValueIs(4, $_GET, 'support') }} value="4">
					4 - Lean No
				</option>
				<option {{ selectedIfValueIs(5, $_GET, 'support') }} value="5">
					5 - No
				</option>
			</select>
		</div>


		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				Participation
			</div>
			<div>

				@foreach(['L' => 'Local (any town)',
						  'SP' => 'State Primaries',
						  'SG' => 'State General',
						  ]
						  as $code => $jurisdiction)

				<div class="p-1 flex">

					<div class="w-32">{{ $jurisdiction }}</div>

					<div class="mx-1 text-grey-darker uppercase">Voted in</div>

					<select name="participation_{{ $code }}_equate" class="ml-2">
						@foreach(['' => '--',
								  '>' => 'More than',
								  '=' => 'Exactly',
								  '<' => 'Fewer than']
								  as $key => $val)
							<option {{ selectedIfValueIs($val, $_GET, 'participation_'.$code.'_equate') }} value="{{ $val }}">
								{{ $val }}
							</option>
						@endforeach
					</select>

					<select name="participation_{{ $code }}_num" class="ml-2">
						@foreach(['', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $num)
							<option {{ selectedIfValueIs($num, $_GET, 'participation_'.$code.'_num') }} value="{{ $num }}">
								{{ $num }}
							</option>
						@endforeach
					</select>

					<div class="mx-2 text-grey-darker uppercase">elections since</div>

					<select name="participation_{{ $code }}_year" class="">
						@for($year = \Carbon\Carbon::now()->format("Y"); 
							  $year >= \Carbon\Carbon::now()->subYears(20)->format("Y");
							  $year--)
							<option {{ selectedIfValueIs($year, $_GET, 'participation_'.$code.'_year') }} value="{{ $year }}">
								{{ $year }}
							</option>
						@endfor
					</select>


				</div>

			@endforeach


			</div>
		</div>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				street name
			</div>
			<div class="mr-2">
				<input name="street" id="street" autocomplete="off" size="30" type="text" class="border border-grey rounded-lg p-1 px-2 text-black" value="{{ (isset($_GET['street'])) ? $_GET['street'] : '' }}" placeholder="Street" />
			</div>
		</div>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				city
			</div>
			<div class="mr-2">
				<input name="city" id="city" autocomplete="off" size="30" type="text" class="border border-grey rounded-lg p-1 px-2 text-black" value="{{ (isset($_GET['city'])) ? $_GET['city'] : '' }}" placeholder="City" />
			</div>
		</div>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				districts
			</div>
			<div class="mr-2">

				<select class="slim-select" query_form="true" id="slim-select-congress-district" multiple="multiple" name="congress_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'F') as $district)
						<option {{ selectedIfInArray($district->id, $_GET, 'congress_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>
				<select class="slim-select" query_form="true" id="slim-select-senate-district" multiple="multiple" name="senate_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'S') as $district)
						<option {{ selectedIfInArray($district->id, $_GET, 'senate_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>
				<select class="slim-select" query_form="true" id="slim-select-house-district" multiple="multiple" name="house_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'H') as $district)
						<option {{ selectedIfInArray($district->id, $_GET, 'house_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>

			</div>
		</div>





		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				ward / precinct
			</div>
			<div class="mr-2">
				<input name="ward" id="ward" autocomplete="off" size="5" type="text" class="border border-grey rounded-lg p-1 px-2 text-black" value="{{ (isset($_GET['ward'])) ? $_GET['ward'] : '' }}" placeholder="Ward" />

				<input name="precinct" id="precinct" autocomplete="off" size="5" type="text" class="border border-grey rounded-lg p-1 px-2 text-black" value="{{ (isset($_GET['precinct'])) ? $_GET['precinct'] : '' }}" placeholder="Pct" />
			</div>
		</div>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				Party
			</div>
			<select name="party" class="">
				<option {{ selectedIfValueIs('', $_GET, 'party') }} value="">
					-- Any --
				</option>
				<option {{ selectedIfValueIs('d', $_GET, 'party') }} value="d">
					D
				</option>
				<option {{ selectedIfValueIs('r', $_GET, 'party') }} value="r">
					R
				</option>
				<option {{ selectedIfValueIs('u', $_GET, 'party') }} value="u">
					U
				</option>
			</select>
		</div>

		<div class="flex items-center py-2 border-t">
			<div class="mr-2 w-32 uppercase text-sm">
				Gender
			</div>
			<select name="gender" class="">
				<option {{ selectedIfValueIs('', $_GET, 'gender') }} value="">
					-- Any --
				</option>
				<option {{ selectedIfValueIs('m', $_GET, 'gender') }} value="m">
					M
				</option>
				<option {{ selectedIfValueIs('f', $_GET, 'gender') }} value="f">
					F
				</option>
				<option {{ selectedIfValueIs('x', $_GET, 'gender') }} value="x">
					X
				</option>
			</select>
		</div>

		</div>
		<!-- end form minimize interior -->

		<div class="flex items-center py-1 w-full">
			<div class="flex-1 mr-4 text-blue-dark cursor-pointer text-left">
				<span id="close" class="toggle_minimize {{ (Auth::user()->getMemory('campaign_participant_form') == 'min') ? 'hidden' : '' }}"><i class="fas fa-minus-circle mr-2"></i> Minimize</span>
				<span id="open" class="toggle_minimize {{ (Auth::user()->getMemory('campaign_participant_form') == 'max') ? 'hidden' : '' }}"><i class="fas fa-plus-circle mr-2"></i> Re-Open</span>
			</div>
			<div class="toggle_search flex-grow mr-4 text-blue-dark cursor-pointer text-right">
				Go back to Basic Search
			</div>
			<button type="submit" class="float-right flex-initial rounded-lg bg-blue text-white px-2 py-1 my-1 border">
				Search
			</button>
		</div>
	
	</div>

</form>