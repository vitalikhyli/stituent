
	@if(Auth::user()->team->app_type == 'business')


		<div class="bg-green text-white py-2 px-4 mt-4 text-base font-bold">

			Marketing Options

		</div>

		<div class="px-2 py-4 border-b bg-green-lightest">

			<div class="text-sm mb-2 font-bold text-center">
				People connected to a SalesEntity of type:
			</div>

			<div class="text-center">
				<select query_form="true" name="sales_type" class="border">
					@foreach(\App\Models\Business\SalesEntity::where('team_id', Auth::user()->team->id)
															 ->get()
									    					 ->pluck('type')
									    					 ->unique() as $type)
						<option {{ selectedIfValueIs($type , $input, 'sales_type') }} value="{{ $type }}">{{ $type }}</option>
					@endforeach
				</select>
			</div>

			<div class="text-center p-2 text-sm">

				<label for="sales_clients" class="mr-1 font-normal">
					<input query_form="true" type="radio" id="sales_clients" name="sales_clients_or_prospects" value="clients" {{ checkedIfValueIs('clients' , $input, 'sales_clients_or_prospects', $default = true) }} /> Current Clients
				</label>
				|
				<label for="sales_prospects" class="ml-1 font-normal">
					<input query_form="true" type="radio" id="sales_prospects" name="sales_clients_or_prospects" value="prospects" {{ checkedIfValueIs('prospects' , $input, 'sales_clients_or_prospects', $default = false) }} /> Prospects
				</label>

			</div>

		</div>

	@endif


	@if(!isset($thesearch))
		<div class="bg-blue-light text-white mb-4 py-2 px-4 font-bold">
			Who Should Receive this Email?
		</div>
	@else
		<div class="bg-blue-darker text-white mb-4 py-2 px-4 font-bold">
			<i class="w-6 fas fa-search mr-2 text-center"></i>{{ $thesearch->name }}
		</div>
	@endif


	<input type="hidden" autocomplete="false" />
	<input type="username" style="position:absolute; top:-50px;" />

	@if(isset($thesearch))
		<input type="hidden" query_form="true" name="thesearch_id" value="{{ $thesearch->id }}" />
	@endif

	<div class="flex">
		<div class="pl-4">
			<label class="switch">
			  <input query_form="true" type="checkbox" name="master_email" {{ checkedIfValueIs("on", $input, 'master_email') }}>
			  <span class="slider round"></span>
			</label>
		</div>
		<div class="pl-3 pt-1 uppercase text-grey-dark">
			Master Email List
		</div>
	</div>

	<div class="flex items-center w-full">
		<div class="w-2/5 text-sm px-4">
			Age:
		</div>

		<div class="w-3/5 py-2 px-4 text-gray-dark">
			<select name="age" class="border rounded p-1 w-full" query_form="true">
				<option value="">- Select Age -</option>
				<option value="75">75 and Over</option>
				<option value="65">65 and Over</option>
			</select>
			<input type="hidden" name="age_operator" value=">" query_form="true" />
		</div>
	</div>

	<div class="py-1 px-4 text-gray-dark">
		<select class="slim-select" query_form="true" id="slim-select-municipality" multiple="multiple" name="municipalities[]">
			<option data-placeholder="true"></option>

			@php
				$city_codes = Auth::user()->team->people()->pluck('city_code')->unique();
				$municipalities = App\Municipality::whereIn('code', $city_codes)->orderBy('name')->get();
				
			@endphp

			@foreach ($municipalities as $municipality)
				<option {{ selectedIfInArray($municipality->id, $input, 'municipalities') }} value="{{ $municipality->id }}">
					{{ $municipality->name }}
				</option>
			@endforeach
		</select>
	</div>
	

	<div class="mx-4 my-2">
	</div>

	@include('shared-features.constituents.category-selects')


	

	<div class="text-sm px-4">
		Case Type:
	</div>

	<div class="py-2 px-4 text-gray-dark">
		<select name="case_type" class="border rounded p-1 w-full" query_form="true">
			<option value="">- Select Type -</option>
			@foreach($available_types as $thetype)
				@if (isset($input['case_type']))
					<option {{ ($input['case_type'] == $thetype) ? 'selected' : '' }} value="{{ $thetype }}">{{ $thetype }}</option>
				@else
					<option value="{{ $thetype }}">{{ $thetype }}</option>
				@endif
			@endforeach
		</select>
	
	</div>

	<div class="text-sm px-4">
		Previous Bulk Email Recipients:
	</div>

	<div class="py-2 px-4 text-gray-dark">
		<select id="slim-select-has_received_emails" multiple="multiple" name="has_received_emails[]" query_form="true" >
			<option data-placeholder="true"></option>
			@foreach ($completed as $email)
				<option {{ selectedIfInArray($email->id, $input, 'has_received_emails') }} value="{{ $email->id }}">
					{{ \Carbon\Carbon::parse($email->completed_at)->format("Y-m-d") }}
					 - {{ $email->subject }}
					 @if ($email->old_tracker_code)
					 	({{ $email->old_tracker_code }})
					 @endif
				</option>
			@endforeach
		</select>
	</div>



	<div class="mx-4 my-2 border-t-2">
	</div>
	

	<div class="text-sm px-4">
		<b>Don't</b> send if they have previously received:
	</div>

	<div class="py-2 px-4 text-gray-dark">

		<select id="slim-select-has_not_received_emails" multiple="multiple" name="has_not_received_emails[]" query_form="true">

			<option data-placeholder="true"></option>
			
			@foreach ($completed as $email)
				<option {{ selectedIfInArray($email->id, $input, 'has_not_received_emails') }} value="{{ $email->id }}">
					{{ \Carbon\Carbon::parse($email->completed_at)->format("Y-m-d") }}
					 - {{ $email->subject }}
					 @if ($email->old_tracker_code)
					 	({{ $email->old_tracker_code }})
					 @endif
				</option>
			@endforeach

		</select>

	</div>

	<div class="px-4 mt-2">

		<div class="uppercase text-grey-dark">
			Ignore By Tracker Code
		</div>

		<select id="previous-tracker-codes-form" class="form-control" name="ignore_tracker_code" query_form="true">
			<option value="">- Previous Tracker Codes -</option>
			@foreach ($previous_tracker_codes as $tracker_code)
				<option {{ selectedIfValueIs($tracker_code, $input, 'ignore_tracker_code') }} value="{{ $tracker_code }}">{{ $tracker_code }}</option>
			@endforeach
		</select>

		<div class="text-xs">
			(don't send if they have previously received an email using this tracker code)
		</div>

	</div>


