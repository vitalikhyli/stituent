<!-- style="display:none;"  -->
<form class="px-2" id="constituents-list-form" style="display:none;" action="" method="GET" autocomplete="off" autocomplete="false">
	<input type="hidden" autocomplete="false" />
	<input type="username" style="position:absolute; top:-50px;" />

	@if(isset($thesearch))
		<input query_form="true" type="hidden" name="thesearch_id" value="{{ $thesearch->id }}" />
	@endif

	<div class="flex items-center mt-4">

		<div class="w-1/3">

			<div class="flex w-full items-center text-sm uppercase text-grey font-bold -mt-2">
				<div class="w-1/3 text-right">
					All People
				</div>
				<div class="w-1/3 text-center pt-2">
					<!-- https://www.w3schools.com/howto/howto_css_switch.asp -->
					<!-- Rounded switch -->
					<label class="switch">
					  <input query_form="true" type="checkbox" name="linked" {{ checkedIfValueIs("on", $input, 'linked') }}>
					  <span class="slider round"></span>
					</label>
				</div>
				<div class="w-1/3 text-left">
					Linked Only
				</div>
			</div>

				

			

			<div class="mx-4">
			</div>

			<div class="pb-2">

				@include('shared-features.constituents.category-selects')

			</div>

		</div>
		<div class="w-1/3">

			<!-- <div class="px-2">
				<input id="cfbar" 
					   type="text"
					   name="constituent_name" 
					   placeholder="&#61447;    @lang('Name Lookup')" 
					   style="font-family:Font Awesome\ 5 Free, Arial" 
					   class="text-input w-full appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
			</div> -->

			<div class="flex w-full">
				<div class="w-1/2">
					<div class="pl-2">
						<input query_form="true" type="text"
							   name="first_name" 
							   id="first_name" 
							   autocomplete="off"
							   value="{{ (isset($input['first_name'])) ? $input['first_name'] : '' }}"
							   placeholder="@lang('First Name')" 
							   class="text-input w-full appearance-none rounded-l-full px-4 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
					</div>
				</div>
				<div class="w-1/2">
					<div class="pr-2">
						<input query_form="true" type="text"
							   name="last_name" 
							   autocomplete="off"
							   value="{{ (isset($input['last_name'])) ? $input['last_name'] : '' }}"
							   placeholder="@lang('Last Name')" 
							   class="text-input w-full appearance-none rounded-r-full px-4 py-3 bg-grey-lighter border-t-2 border-b-2 border-r-2 border-grey text-black focus:border-2 text-lg" />
					</div>
				</div>
			</div>
			<!-- <div class="p-4 text-gray-dark">
				<input type="checkbox" name="with_archived" /> With Archived
			</div> -->

			<div class="mt-2 py-1 px-4 text-gray-dark">
				<select class="slim-select" query_form="true" id="slim-select-congress-district" multiple="multiple" name="congress_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'F') as $district)
						<option {{ selectedIfInArray($district->id, $input, 'congress_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>
			</div>
			<div class="py-1 px-4 text-gray-dark">
				<select class="slim-select" query_form="true" id="slim-select-senate-district" multiple="multiple" name="senate_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'S') as $district)
						<option {{ selectedIfInArray($district->id, $input, 'senate_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>
			</div>
			<div class="py-1 px-4 text-gray-dark">
				<select class="slim-select" query_form="true" id="slim-select-house-district" multiple="multiple" name="house_districts[]">
					<option data-placeholder="true"></option>
					@foreach ($districts->where('type', 'H') as $district)
						<option {{ selectedIfInArray($district->id, $input, 'house_districts') }} value="{{ $district->id }}">
							{{ $district->full_name }}
						</option>
					@endforeach
				</select>
			</div>

			<div class="py-1 px-4 text-gray-dark">
				<select class="slim-select" query_form="true" id="slim-select-zip" multiple="multiple" name="zips[]">
					<option data-placeholder="true"></option>
					@foreach ($zips as $zip)
						<option {{ selectedIfInArray($zip, $input, 'zips') }} value="{{ $zip }}">
							{{ $zip }}
						</option>
					@endforeach
				</select>
			</div>


			
		</div>
		<div class="w-1/3">

			<div class="py-1 px-4 text-gray-dark">
				<select class="slim-select" query_form="true" id="slim-select-municipality" multiple="multiple" name="municipalities[]">
					<option data-placeholder="true"></option>
					@foreach ($municipalities as $municipality)
						<option {{ selectedIfInArray($municipality->id, $input, 'municipalities') }} value="{{ $municipality->id }}">
							{{ $municipality->name }}
						</option>
					@endforeach
				</select>
			</div>


			@if (request('municipalities') || $municipalities->count() < 2)
				<div class="py-1 px-4 text-grey-dark">
					<input 
					   query_form="true" type="text"
					   name="street" 
					   autocomplete="off"
					   value="{{ (isset($input['street'])) ? $input['street'] : '' }}"
					   placeholder="@lang('Street Number And/Or Name')" 
					   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />
				</div>
			@else
				<div class="py-1 px-4 text-grey-dark">
					<input 
					   query_form="true" type="text"
					   name="street" 
					   disabled="disabled"
					   value="{{ (isset($input['street'])) ? $input['street'] : '' }}"
					   placeholder="@lang('Address -- Choose a Municipality first')" 
					   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />
				</div>
			@endif

			

			


			<div class="px-4 py-1 text-gray-dark cursor-pointer">
				<select class="slim-select" query_form="true" id="slim-select-party" multiple="multiple" name="parties[]">
					<option data-placeholder="true"></option>
					<option {{ selectedIfInArray("U", $input, 'parties') }} value="U">
						Unenrolled
					</option>
					<option {{ selectedIfInArray("D", $input, 'parties') }} value="D">
						Democratic
					</option>
					<option {{ selectedIfInArray("R", $input, 'parties') }} value="R">
						Republican
					</option>
					<option {{ selectedIfInArray("L", $input, 'parties') }} value="L">
						Libertarian
					</option>
					<option {{ selectedIfInArray("O", $input, 'parties') }} value="O">
						Other
					</option>
					<option {{ selectedIfInArray("", $input, 'parties') }} value="NONE">
						None
					</option>
				</select>
			</div>

			<div class="flex px-4 py-2 items-center">
				<div class="">
					Age
				</div>
				<div class="px-2">
					<select query_form="true" name="age_operator" class="form-control">

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
				   query_form="true" type="text"
				   name="age" 
				   value="{{ (isset($input['age'])) ? $input['age'] : '' }}"
				   placeholder="@lang('Age / Range')" 
				   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />
				</div>

			</div>

			@if (!request()->input('linked'))
			<div class="flex px-4 w-full items-center text-xs uppercase text-grey font-bold">
				<div class="w-1/2 text-left">
					With Archived? <i class="fa fa-archive text-sm"></i> 
				</div>
				<div class="w-1/2 flex items-center">
					<div class="w-1/5 text-right">
						Yes
					</div>
					<div class="w-3/5 text-center pt-2 mx-2">
						<!-- https://www.w3schools.com/howto/howto_css_switch.asp -->
						<!-- Rounded switch -->
						<label class="switch">
						  <input query_form="true" type="checkbox" name="ignore_archived" {{ checkedIfValueIs("on", $input, 'ignore_archived') }}>
						  <span class="slider round"></span>
						</label>
					</div>
					<div class="w-1/5 text-left">
						No
					</div>
				</div>
			</div>
			@endif

			@if(isset($thesearch))

		
				<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/constituents/searches/update" id="update-search" class="hidden flex-1 bg-blue px-2 py-1 text-xs mx-1 text-white">
					Save Changes
				</button>
				
				<a class="cursor-pointer" data-toggle="modal" data-target="#save-search">
					<div class="flex-1 bg-grey-lighter px-2 py-1 text-xs mx-1">
						Save As...
					</div>
				</a>

			@else

				<div class="w-full text-center">
					<a class="cursor-pointer bg-grey-lighter px-2 py-1 text-xs mx-1 rounded-full" data-toggle="modal" data-target="#save-search">

						Save Search
						
					</a>
				</div>

			@endif

			@if(isset($thesearch))
				<a href="/{{ Auth::user()->team->app_type }}/constituents/searches/{{ $thesearch->id }}/export" class="cursor-pointer">
					<div class="flex-1 bg-grey-lighter px-2 py-1 text-xs mx-1">
						Export
					</div>
				</a>
			@endif

			
		</div>
	</div>

	<div class="mt-2 border-t border-dashed pt-2 text-center">
		<!-- <div class="text-xs uppercase pb-1">My Search Options</div> -->

		<div class="px-4 py-2 text-center inline-flex">

			<input name="limit" 
				   type="text" 
				   class="px-2 border" 
				   value="{{ (isset($input['limit'])) ? $input['limit'] : '' }}"
				   placeholder="Max Pins (200)" />

			<button type="submit" class="rounded-full bg-blue text-white hover:bg-blue-dark px-4 py-2 mx-4">
				Update Map
			</button>

			
			

<!-- 			<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/constituents/searches/exportUnsaved" id="export-search" class="flex-1 bg-blue px-2 py-1 text-xs mx-1 text-white">
				Export
			</button> -->


		</div>
	</div>

	<!-- Modal -->
	<div id="save-search" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-sm">

	    <!-- Modal content-->
	    <div class="modal-content">


	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Save this Search</h4>
	      </div>

	      <div class="modal-body">

	        <div class="w-full">
	        	
			<input 
			   type="text"
			   name="search_name" 
			   id="search_name" 
			   placeholder="Search Name" 
			   class="text-input w-full appearance-none px-4 py-2 bg-grey-lighter border border-grey text-black focus:border-2" />
			</div>

	      </div>

	      <div class="modal-footer">
	        <button type="button" class="px-4 py-2 rounded-full border" data-dismiss="modal">Cancel</button>
	        <button id="save_search_button" formaction="/{{ Auth::user()->team->app_type }}/constituents/searches/save" class="px-4 py-2 rounded-full text-white bg-blue-light ml-2 hover:bg-blue" type="submit">Save</button>
	      </div>

	    </div>

	  </div>
	</div>

	<input type="hidden" id="constituents-list-form-fields" name="constituents-list-form-fields" value="{{ (isset($input['constituents-list-form-fields'])) ? $input['constituents-list-form-fields'] : '' }}" />
	
</form>