<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">

	<form method="post" action="/{{ Auth::user()->team->app_type }}/lists/export">

		@csrf

		<div class="modal-dialog rounded-t-lg" role="document">
			<div class="modal-content rounded-t-lg">

				<div class="modal-header bg-blue text-white rounded-t-lg">
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>

				    Choose Export Options

				</div>

				<div class="modal-body">

				  <div class="text-left font-bold py-2 text-sm text-grey-darker">

				  	<input class="hidden" name="modal_export_list_id" value="REPLACE_ME" id="modal_export_list_id" />

				  	<div class="flex">
				  		<div class="w-1/2">
				  	
					  		@foreach(['name', 
					  				  'party',
					  				  'age/gender',
					  				  'best_address',
					  				  ]
					  				  as $field)
					  			<div class="{{ (!$loop->last) ? 'border-b' : '' }}">
								  	<label for="modal_export-{{ $loop->iteration }}" class="font-normal uppercase">
								  		<input type="checkbox" id="modal_export-{{ $loop->iteration }}" checked name="modal_export_fields[]" value="{{ $field }}" /> &nbsp; {{ str_replace('_', ' ', $field) }}
								  	</label>
							    </div>
							@endforeach
						</div>
						<div class="w-1/2">
				  	
					  		@foreach([
					  				  'voter_file_name',
					  				  'voter_file_address',
					  				  'mailing_address',
					  				  'ward/precinct',
					  				  ]
					  				  as $field)
					  			<div class="{{ (!$loop->last) ? 'border-b' : '' }}">
								  	<label for="modal_export-{{ $loop->iteration+4 }}" class="font-normal uppercase">
								  		<input type="checkbox" id="modal_export-{{ $loop->iteration+4 }}" checked name="modal_export_fields[]" value="{{ $field }}" /> &nbsp; {{ str_replace('_', ' ', $field) }}
								  	</label>
							    </div>
							@endforeach
					</div>

				  </div>

				  <div x-data="{ householded : 'false' }"
				  	   class="border-t-4 pt-2">

				  	<div class="pb-2">Grouping Options:</div>

				  	<div class="px-2 flex">

				  		<div class="w-1/2">
							<label for="group_by_household_no" class="font-normal uppercase">
								<input id="group_by_household_no" type="radio" value="false" name="group_by_household" checked x-model="householded" />
						  		<span class="p-1">Individual</span>
						  	</label>
						</div>

						<div class="w-1/2">
						  	<label for="group_by_household_yes" class="font-normal uppercase">
						  		<input id="group_by_household_yes" type="radio" value="true" name="group_by_household" x-model="householded" />
						  		<span class="p-1">Group by Household</span>
						  	</label>
						</div>

				  	</div>

					<!-- <div class="py-2">Address to Use:</div>

					<div class="px-2">

			  			<div>
							<label for="override_participant" class="font-normal uppercase">
						  		<input id="override_participant" type="checkbox" name="override_volunteers" checked />
						  		<span class="p-1">Use Addresses You Entered Instead of Voter File Address</span>
						  	</label>
					    </div>

			  			<div>
							<label for="override_mailing" class="font-normal uppercase">
						  		<input id="override_mailing" type="checkbox" name="override_volunteers" />
						  		<span class="p-1">Use Mailing Address Instead of Residential Address</span>
						  	</label>
					    </div>

				    </div> -->


					<div class="py-2">Include:</div>

					<div class="px-2">

					 	<div class="flex">

							<label for="include_phones" class="block font-normal uppercase">
						  		<input id="include_phones" type="checkbox" name="include_phones" />
						  		<span class="p-1">Phones</span>
						  	</label>

							<label for="phones_columns" class="ml-2 block font-normal uppercase">
						  		<input id="phones_columns" type="checkbox" name="phones_columns" />
						  		<span class="p-1">Give each its own column</span>
						  	</label>

						</div>

					  	<label for="include_emails" class="block font-normal uppercase">
					  		<input id="include_emails" type="checkbox" name="include_emails" />
					  		<span class="p-1">Emails</span>
					  	</label>

						<label for="include_support" class="block font-normal uppercase">
					  		<input id="include_support" type="checkbox" name="include_support" />
					  		<span class="p-1">Support</span>
					  	</label>
				 
					 	<div class="flex">

							<label for="include_volunteers" class="block font-normal uppercase">
						  		<input id="include_volunteers" type="checkbox" name="include_volunteers" />
						  		<span class="p-1">Volunteer Tags</span>
						  	</label>

							<label for="volunteers_columns" class="ml-2 block font-normal uppercase">
						  		<input id="volunteers_columns" type="checkbox" name="volunteers_columns" />
						  		<span class="p-1">Give each its own column</span>
						  	</label>

						</div>

					  	<label for="include_tags" class="block font-normal uppercase">
					  		<input id="include_tags" type="checkbox" name="include_tags" />
					  		<span class="p-1">Tags</span>
					  	</label>

					  	<label for="include_lists" class="block font-normal uppercase">
					  		<input id="include_lists" type="checkbox" name="include_lists" />
					  		<span class="p-1">Lists</span>
					  	</label>
						 



				    </div>

				  </div>

				</div>

				<div class="modal-footer">

				  @if (!Auth::user()->permissions->export)
				  	<span class="pr-4">You do not have permissions to Export.</span>
				  @endif
				  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				  @if (Auth::user()->permissions->export)
					<button type="submit" id="modal_export_submit" class="btn btn-primary">Export</button>
				  @endif

				</div>

			</div>
			
		</div>

	</form>

</div>