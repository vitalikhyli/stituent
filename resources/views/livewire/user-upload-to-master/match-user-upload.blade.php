<div>

	@if($upload->matched_count != $upload->count)


		<div class="text-3xl font-bold pb-2">
			Matching File with CF Database...
		</div>

		<div>
            <div wire:poll.1000ms class="flex w-5/6 items-center">

            <div class="w-4/5 rounded-full h-4 border">
                <div class="bg-blue-light h-4 rounded-full" style="width: {{ round(($upload->matched_count/$upload->count) * 100) }}%;"></div>
            </div>
            <div class="w-1/5 pl-6 text-blue-light font-bold text-2xl">
                {{ round(($upload->matched_count/$upload->count) *100) }}%
            </div>

        </div>

	    <div class="flex">
	        @foreach(str_split(str_pad($upload->matched_count, 7,'0',STR_PAD_LEFT)) as $key => $char)
	            <div class="font-mono font-bold {{ ($key >= 7- strlen($upload->matched_count)) ? 'text-blue' : 'text-grey' }} border-t-4 border-l border-grey-dark px-2 py-2 bg-white shadow-lg">{{ $char }}</div>
	        @endforeach
	        <div class="text-lg font-bold p-2">Records</div>
	    </div>


	@else


	<div class="text-3xl font-bold border-b-4 border-blue pb-2">
		{{ $upload->name }}


	</div>
	<div class="w-full flex">

	<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
		<a href="/{{ Auth::user()->team->app_type }}/useruploads/{{ $upload->id }}/import" class="text-white hover:text-white">
			<div>
				<i class="fas fa-check-circle mr-1"></i> Import
			</div>
		</a>
	</div>

	<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
		<div>
			<i class="fas fa-check-circle mr-1"></i> Match
		</div>
	</div>

	<div class="cursor-pointer bg-white text-blue p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
		Integrate
	</div>

	</div>

	<div class="text-xl font-bold border-b-2 pb-1 mt-6">
		Review Matches ({{ $upload->matched_lines->count() }})


		<button wire:click="toggleReview" class="{{ ($upload->matched_lines->first()) ? '' : 'hidden' }} rounded-lg bg-blue float-right text-sm font-normal text-white px-2 py-1">{{ ($show_review) ? 'Hide' : 'Show' }}</button>


	</div>

	@if(!$upload->matched_lines->first())

		<div class="bg-blue-lightest p-3 border-b text-sm">
			No matches found.
		</div>		

	@else

	<div class="{{ ($show_review) ? '' : 'hidden' }}">

		<div class="bg-blue-lightest p-3 border-b text-sm w-full">
			Found the following matches. Click unmatch if not correct.

			@if($paginate_offset + 5 < $upload->matched_lines->count())
				<div wire:click="setPaginateOffset('next', 5)" class="cursor-pointer border rounded-lg bg-white float-right px-4 ml-1">
					<i class="fas fa-angle-right"></i>
				</div>
			@else
				<div class="opacity-25 cursor-pointer border rounded-lg bg-white float-right px-4 ml-1">
					<i class="fas fa-angle-right"></i>
				</div>
			@endif

			
			@if($paginate_offset - 5 >= 0)
				<div wire:click="setPaginateOffset('prev', 5)" class="cursor-pointer border rounded-lg bg-white float-right px-4 ml-1">
					<i class="fas fa-angle-left"></i>
				</div>
			@else
				<!-- <div class="opacity-25 cursor-pointer border rounded-lg bg-white float-right px-4 ml-1">
					<i class="fas fa-angle-left"></i>
				</div> -->
			@endif

		</div>

		<div class="table w-full">

			<div class="table-row bg-blue  text-white w-full border-b py-1 text-sm">

				<div class="table-cell border-b py-1 w-6 text-grey-dark pl-1">
					
				</div>

				<div class="table-cell border-b p-1">
					<span class="">Your File</span>
				</div>

				<div class="table-cell border-b p-1">
					<span class="">Community Fluency</span>
				</div>

				<div class="table-cell w-24 text-right border-b p-1">
					
				</div>

			</div>

			@foreach($upload->matched_lines()->skip($paginate_offset)->take(5)->get() as $match)

				<div class="table-row w-full border-b py-1 text-sm">

					<div class="table-cell border-b py-1 w-6 text-grey-dark p-1 w-4">
						{{ $loop->iteration + $paginate_offset }}.
					</div>

					<div class="table-cell border-b p-1 text-black w-64">
						{{ implode('|', $match->data) }}
					</div>


					<div class="table-cell border-b p-1 uppercase w-3/5">
						@if($match->voter)
							<span class="text-red">
								{{ $match->voter_id }} - 
								{{ $match->voter->full_name }}
							</span>
							<span class="text-red">
								{{ $match->voter->full_address}}
							</span>
						@endif

						@if(Auth::user()->team->app_type == 'campaign')
							@if($match->participant)
								<div class="py-1">
									<span class="text-blue">
										{{ $match->participant->full_name }}
									</span>
									<span class="text-blue">
										{{ $match->participant->full_address }}
									</span>
								</div>
							@endif
						@endif

						@if(Auth::user()->team->app_type != 'campaign')
							<div class="py-1">
								@if($match->person)
									<span class="text-blue">
										{{ $match->person->full_name }}
									</span>
									<span class="text-blue">
										{{ $match->person->full_address }}
									</span>
								@endif
							</div>
						@endif
					</div>

					<div class="table-cell w-24 text-right border-b p-1">
						<button wire:click="removeMatch('{{ $match->id }}')" class="rounded-lg bg-grey-lighter text-grey-darker px-2 py-1 text-xs whitespace-no-wrap">
							do not match
						</button>
					</div>

				</div>

			@endforeach

		</div>

	</div>

	@endif


		<div class="text-xl font-bold border-b-2 pb-1 mt-6">
			Handling Non-Matches
		</div>

		<div class="bg-blue-lightest p-3 border-b text-sm">
			When no CF Record or Voter is found, create a new record...
		</div>

		<div class="text-sm py-2">
			<div>
				<label for="new-rules-1" class="font-normal">
					<input wire:model="new_rules" value="" id="new-rules-1" type="radio" class="mr-2" />
					<span class="ml-2"><b>Never</b> - Do not create a new CF Record</span>
				</label>
			</div>
			<div>
				<label for="new-rules-2" class="font-normal">
					<input wire:model="new_rules" value="voters_only" id="new-rules-2" type="radio" class="mr-2" /> 

						<span class="ml-2"><b>Voters</b> - Create if voter record found</span>
				</label>
			</div>
			<div>
				<label for="new-rules-2" class="font-normal">
					<input wire:model="new_rules" value="always" id="new-rules-3" type="radio" class="mr-2" /> 

						<span class="ml-2"><b>Always</b> - Create a new CF Record</span>
				</label>
			</div>
			<div class="{{ (!$upload->hasMatchedColumn('primary_email')) ? 'opacity-50' : '' }}">
				<label for="new-rules-3" class="font-normal">
					<input wire:model="new_rules" value="if-email" id="new-rules-3" type="radio" class="mr-2" {{ (!$upload->hasMatchedColumn('primary_email')) ? 'disabled' : '' }} />
						<span class="ml-2">Only if the upload record has a <b>primary email</b></span>
				</label>
			</div>
<!-- 			<div class="{{ (!$upload->hasMatchedColumn('primary_email')) ? 'opacity-50' : '' }}">
				<label for="new-rules-4" class="font-normal">
					<input wire:model="new_rules" value="if-email-or-voter-email" id="new-rules-4" type="radio" class="mr-2" {{ (!$upload->hasMatchedColumn('primary_email')) ? 'disabled' : '' }} />
						<span class="ml-2">Only if the upload record has a <b>primary email</b> OR a match in the <b>Voter File has an email</b></span>
				</label>
			</div> -->
			@if(!$upload->hasMatchedColumn('primary_email'))
				<span class="text-blue">* Some options required the primary_email field to be matched.</span>
			@endif

		</div>



		<div class="text-xl font-bold border-b-2 pb-1 mt-6">
			Updating Matches
		</div>

		<div class="bg-blue-lightest p-3 border-b text-sm">
			When a match is found, how should the data be updated for existing Voters and CF Records?
		</div>

		<div class="text-sm">

			<div class="py-1">
				<label class="font-normal text-blue">
					<input type="checkbox" checked disabled/> Never update any field if the User Upload field is blank
				</label>
			</div>

			<div class="flex border-b py-2">
				<div class="w-1/4 pl-2">
				</div>

				<div class="w-1/4 pl-2 flex">

					<div wire:click="setReviewOffset('prev')" class="cursor-pointer border rounded-lg bg-white px-4 mr-1">
						<i class="fas fa-angle-left"></i>
					</div>

					<div wire:click="setReviewOffset('next')" class="cursor-pointer border rounded-lg bg-white px-4 ml-1">
						<i class="fas fa-angle-right"></i>
					</div>

				</div>

				<div class="w-full">
				</div>

			</div>

			@foreach(collect($upload->column_map)->sortKeys() as $column => $rules)

				@if(!$column)
					@continue
				@endif

				<div class="flex border-b py-2">
					<div class="w-1/4 pl-2">
						{{ $column }}
					</div>

					<div class="w-1/4 pl-2 text-blue">
						{{ $upload->lines()->skip($review_offset)->first()->data[array_search($column, $upload->columns)] }}
					</div>

					<div class="w-full">
						

						@foreach($rules as $rule_id => $rule)
							<div class="flex w-full mb-2 
								@if($rule['action'] == 'match')
									border border-blue p-2 
									@if($rule['qual'])
										bg-blue-lighter
									@endif
								@endif
								">								

								<div class="px-2 w-1/4">
									<select wire:model="column_map.{{ $column }}.{{ $rule_id }}.action" class="text-black" class>
										<option value="">--</option>
										<option value="replace">Update + Replace</option>
										<option value="if-empty">Update if CF field = empty</option>
										<option disabled>----------</option>
										
										<option value="email-primary">Email - Set as Primary</option>
										<option value="email-work">Email - Set as Work</option>
										<option value="email-add">Email - Add Other</option>
										
										<option disabled>----------</option>
										
										<option value="phone-primary">Phone - Set as Primary</option>
										<option value="phone-work">Phone - Set as Work</option>
										<option value="phone-add">Phone - Add Other</option>

										<option disabled>----------</option>
										@if(Auth::user()->team->app_type == 'campaign')
											<option value="tag">Tag As</option>
										@endif
										@if(Auth::user()->team->app_type == 'office')
											<option value="group">Add to Group</option>
										@endif
									</select>
								</div>


								<div class="w-1/4 whitespace-no-wrap mx-4 pl-4">
									@if(
										($rule['action'] == 'replace') ||
										($rule['action'] == 'if-empty')
										)

										<i class="fas fa-pen-square text-blue mr-1"></i>

										<select wire:model="column_map.{{ $column }}.{{ $rule_id }}.qual" class="text-black">
											<option value="">-- Choose Field --</option>
											@foreach($updatable_fields as $db_field => $fieldname)
												<option value="{{ $db_field }}">
													{{ $fieldname }}
												</option>
											@endforeach
										</select>

									@endif


									@if($rule['action'] == 'tag')

										<i class="fas fa-users text-green mr-1"></i>

										<select wire:model="column_map.{{ $column }}.{{ $rule_id }}.qual" class="text-black">
											<option value="">-- Choose tag --</option>
											@foreach(App\Tag::thisTeam()->orderBy('name')->get() as $tag)
												<option value="{{ $tag->id }}">
													{{ $tag->name }}
												</option>
											@endforeach
										</select>

									@endif

									@if($rule['action'] == 'group')

										<i class="fas fa-users text-green mr-1"></i>

										<select wire:model="column_map.{{ $column }}.{{ $rule_id }}.qual" class="text-black">
											<option value="">-- Choose group --</option>
											@foreach(App\Group::thisTeam()->whereNull('archived_at')->orderBy('name')->get() as $tag)
												<option value="{{ $tag->id }}">
													{{ $tag->name }}
												</option>
											@endforeach
										</select>

									@endif

									@if($rule['action'] == 'email-add')

										<div class="px-2">

											Email Type:
											<input size="20" wire:model="column_map.{{ $column }}.{{ $rule_id }}.qual" placeholder="Work, Mobile, School, etc." class="border p-1" />

										</div>

									@endif

									@if($rule['action'] == 'phone-add')

										<div class="px-2">

											Phone Type:
											<input size="20" wire:model="column_map.{{ $column }}.{{ $rule_id }}.qual" placeholder="Work, Mobile, School, etc." class="border p-1" />

										</div>

									@endif
								</div>



								<div class="mx-2">

									@if(
										($rule['action'] == 'tag' && $rule['qual']) ||
										($rule['action'] == 'group' && $rule['qual'])
										)

										<select wire:model="column_map.{{ $column }}.{{ $rule_id }}.if" class="text-black">
											<option value="">-- Condition --</option>
											<option value="eq">If field is</option>
											<option value="not-eq">If field is not</option>
											<option value="blank">If field is blank</option>
											<option value="not-blank">If field is not blank</option>
											<option value="gt">If field ></option>
											<option value="lt">If field <</option>
										</select>

										@if(
											($rule['if'] == 'eq') ||
											($rule['if'] == 'not-eq') ||
											($rule['if'] == 'gt') ||
											($rule['if'] == 'lt')
											)

											<input size="10" wire:model="column_map.{{ $column }}.{{ $rule_id }}.if-qual" placeholder="Type here" class="border p-1 mx-2" />


										@endif

									@endif



								</div>



								

							<div class="flex-grow text-right">


								<div class="inline w-6">
									@if(!$loop->first)
										<button wire:click="deleteRule('{{ $column }}', {{ $rule_id }})" class="rounded-lg bg-red-lightest text-grey-dark px-2 py-1 mx-1">
											-
										</button>
									@endif
								</div>

								<div class="inline w-6">
									@if($loop->last)
										
										<button wire:click="addRule('{{ $column }}')" class="rounded-lg bg-blue-lightest text-grey-dark px-2 py-1 mx-1">
												+
										</button>
										
									@endif
								</div>




							</div>



						</div>

					@endforeach


					</div>
				</div>

			@endforeach

		</div>

		<div class="flex border-b py-2">
			<div class="w-1/4 pl-2">
			</div>

			<div class="w-1/4 pl-2 flex">

				<div wire:click="setReviewOffset('prev')" class="cursor-pointer border rounded-lg bg-white px-4 mr-1">
					<i class="fas fa-angle-left"></i>
				</div>

				<div wire:click="setReviewOffset('next')" class="cursor-pointer border rounded-lg bg-white px-4 ml-1">
					<i class="fas fa-angle-right"></i>
				</div>

			</div>

			<div class="w-full">
			</div>

		</div>




		<div class="text-center p-8">

			<a href="/{{ Auth::user()->team->app_type }}/useruploads/{{ $upload->id }}/integrate">
				<button class="rounded-full bg-blue text-xl text-white px-8 py-2">
					Next Step: Integrate with Database
				
				</button>
			</a>

		</div>


	@endif
</div>