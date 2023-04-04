<div>

	<!------------------------------------/ HEADER /------------------------------------>

	<div x-data="{ tab: '' }">
		<div class="flex border-b-4 border-blue">

			<div class="flex-shrink">

				<div class="mt-2 text-2xl font-sans">
					Cases
					@if($cases->first())
						({{ $cases->total() }})
					@endif
				</div>

			</div>

			<div class="flex-grow text-right flex flex-row-reverse text-sm">

				<div class="py-2">
					<a href="/{{ Auth::user()->team->app_type }}/cases/new">
						<button class="rounded-lg bg-blue text-sm uppercase text-white px-4 py-2 hover:bg-blue-darker">
							Start New Case
						</button>
					</a>
				</div>


				@if($cases->first())

					<div class="py-2 mr-4">
					    
				    	<button class="rounded-lg text-sm uppercase text-blue px-4 py-2 hover:bg-grey-lighter"
								 @click="tab = 'report'">
				    		<i class="far fa-file-alt mr-2 text-base"></i> Cases Report
				    	</button>
					    
					</div>

					<div class="py-2 mr-2">
				    	<button class="rounded-lg text-sm uppercase text-blue px-4 py-2 hover:bg-grey-lighter"
				    			@click="tab = 'export'">
				    		<i class="fa fa-file-csv mr-2 text-base"></i> Export CSV
				    	</button>
					</div>


			    @endif

			</div>

		</div>

	    <div class="border-b-4 border-blue p-4 bg-orange-lightest"
	    	 x-show="tab === 'report'"
	    	 x-cloak>

	    	<div class="ml-16">

	    		@if(!$cases->first())

			    	<div class="font-medium p-2 text-lg">
			    		No cases selected for the report.
			    	</div>

			    	<button class="rounded-lg bg-grey-lighter text-black px-4 py-1 border text-sm"
			    			@click="tab = ''">
			    		Cancel
			    	</button>

	    		@else

			    	<div class="font-medium p-2 text-lg">
			    		Generate a Report for {{ $cases->count() }} Cases:

				    	<div class="text-sm font-normal text-grey-darker">
				    		Change the form settings to change which cases will be included.
				    	</div>

			    	</div>

			    	<div class="p-2 text-base">

			    		<label for="report_show_notes" class="font-normal">

			    			<input type="checkbox"
			    				   id="report_show_notes"
			    				   wire:model="reportShowNotes"
			    				   />

			    			<span class="ml-1">Show contact notes</span>

			    		</label>

			    	</div>

			    	<div class="p-2 text-base">

				    	<a href="/{{ Auth::user()->team->app_type }}/cases/report-serial/{{ $report_json }}" target="new">

					    	<button class="rounded-lg bg-blue text-white px-4 py-1 border text-sm border-transparent"
					    			wire:click=""
					    			@click="tab = ''">
					    		Create Report
					    	</button>

					    </a>

				    	<button class="rounded-lg bg-grey-lighter text-black px-4 py-1 border text-sm"
				    			@click="tab = ''">
				    		Cancel
				    	</button>

				    </div>

				@endif

			</div>

	    </div>

	    <div class="border-b-4 border-blue p-4 bg-orange-lightest"
	    	 x-show="tab === 'export'"
	    	 x-cloak>

	    	<div class="ml-16">

	    		@if(!$cases->first())

			    	<div class="font-medium p-2 text-lg">
			    		No cases to export.
			    	</div>

			    	<button class="rounded-lg bg-grey-lighter text-black px-4 py-1 border text-sm"
			    			@click="tab = ''">
			    		Cancel
			    	</button>

	    		@else

			    	<div class="font-medium p-2 text-lg">
			    		Export these {{ $cases->count() }} cases as a .csv file?

				    	<div class="text-sm font-normal text-grey-darker">
				    		Change the form settings to change which cases will be exported.
				    	</div>

			    	</div>


			    	<div class="flex-grow p-2 text-base">

				    	<button class="rounded-lg bg-blue text-white px-4 py-1 border text-sm border-transparent"
				    			wire:click="export()"
				    			@click="tab = ''">
				    		Yes, Export Now
				    	</button>

				    	<button class="rounded-lg bg-grey-lighter text-black px-4 py-1 border text-sm"
				    			@click="tab = ''">
				    		Cancel
				    	</button>

				    </div>

				@endif

			</div>

	    </div>

	</div>
				

	<!------------------------------------/ FILTER /------------------------------------>
	<div class="py-6 pb-8 bg-grey-lightest text-center"
		 x-data="{ datesOpen: false }">


		<div class="text-center">

			<div class="inline-block">
				<input id="search" 
					   autocomplete="off" 
					   type="text" 
					   placeholder="&#xf002; @lang('Search Cases')" style="font-family:Font Awesome\ 5 Free, Arial" 
					   class="px-4 py-3 bg-white border border-grey text-black rounded-lg"
					   wire:model.debounce="search" />

			</div>

			<div class="text-grey-dark p-2 inline-block hover:bg-grey-lighter rounded-lg cursor-pointer"
				 x-on:click.prevent="datesOpen = false; @this.call('restorePresets')">
				<i class="fas fa-redo-alt text-lg"></i>
				<span class="text-xs">Reset</span>
			</div>

		</div>

		<div class="text-center mt-4 w-full">

			<div class="mr-2 flex-shrink inline-block">

				<label for="team" class="font-normal mr-2">
					<input id="team"
						   type="radio"
						   value="team"
						   class=""
						   wire:model="owner" /> <span class="ml-1">Whole Team</span>
				</label>

				<label for="mine" class="font-normal mr-2">
					<input id="mine"
						   type="radio"
						   value="mine"
						   class=""
						   wire:model="owner" /> <span class="ml-1"><i class="fas fa-lock text-grey-dark text-sm mr-1"></i> Mine</span>
				</label>

			</div>

			<div class="mr-2 flex-shrink inline-block border-l pl-4">
				
				<label for="open" class="font-normal mr-2">
					<input id="open"
						   type="checkbox"
						   name="status[]"
						   value="open"
						   class=""
						   wire:model="status" /> <span class="ml-1">Open</span>
				</label>

				<label for="held" class="font-normal mr-2">
					<input id="held"
						   type="checkbox"
						   name="status[]"
						   value="held"
						   class=""
						   wire:model="status" /> <span class="ml-1">Held</span>
				</label>

				<label for="resolved" class="font-normal mr-2">
					<input id="resolved"
						   type="checkbox"
						   name="status[]"
						   value="resolved"
						   class=""
						   wire:model="status" /> <span class="ml-1">Resolved</span>
				</label>

			</div>

			<div class="mr-2 flex-shrink border-l inline-block">

				<label for="orgs_only" class="font-normal ml-2">
					<input id="orgs_only"
						   type="checkbox"
						   name="orgs_only"
						   value="orgs_only"
						   class=""
						   wire:model="orgs_only" /> <span class="ml-1">Organizations Only</span>
				</label>

			</div>

		</div>

		<div class="text-center mt-4 w-full">

			<div class="mr-2 flex-shrink inline-block">

				<select class="border w-48 text-sm"
						wire:model="type">

					<option value="">
						-- Narrow by Type --
					</option>

					@foreach($case_types as $type)

						<option value="{{ $type }}"
								class="truncate">
							{{ $type }}
						</option>

					@endforeach

				</select>

			</div>

			<div class="mr-2 flex-shrink inline-block">

				<select class="border w-48 text-sm"
						wire:model="subtype">

					<option value="">
						-- Narrow by Subtype --
					</option>

					@foreach($case_subtypes as $subtype)

						<option value="{{ $subtype }}"
								class="truncate">
							{{ $subtype }}
						</option>

					@endforeach

				</select>

			</div>

			<div class="mr-2 flex-shrink inline-block border-l pl-4">

				<select class="border w-48 text-sm"
						wire:model="group">

					<option value="">
						-- Narrow by Group --
					</option>

					@foreach($available_groups as $group)

						<option value="{{ $group->id }}"
								class="truncate">
							{{ $group->cat->name }} - {{ $group->name }}
						</option>

					@endforeach

				</select>

			</div>

			<div class="mr-2 flex-shrink inline-block border-l pl-4">

				<select class="border w-48 text-sm"
						wire:model="city">

					<option value="">
						-- Narrow by City/Town --
					</option>

					@foreach($available_cities as $city_option)

						<option value="{{ $city_option->id }}"
								class="truncate">
							{{ $city_option->name }}
						</option>

					@endforeach

				</select>

			</div>
			<div class="mr-2 flex-shrink inline-block border-l pl-4">

				<label for="high" class="font-normal mr-2">
					<input id="high"
						   type="checkbox"
						   name="priority[]"
						   value="high"
						   class=""
						   wire:model="priority" /> <span class="ml-1">High</span>
				</label>

				<label for="medium" class="font-normal mr-2">
					<input id="medium"
						   type="checkbox"
						   name="priority[]"
						   value="medium"
						   class=""
						   wire:model="priority" /> <span class="ml-1">Medium</span>
				</label>

				<label for="low" class="font-normal mr-2">
					<input id="low"
						   type="checkbox"
						   name="priority[]"
						   value="low"
						   class=""
						   wire:model="priority" /> <span class="ml-1">Low</span>
				</label>

				<label for="none" class="font-normal mr-2">
					<input id="none"
						   type="checkbox"
						   name="priority[]"
						   value="none"
						   class=""
						   wire:model="priority" /> <span class="ml-1">No Priority</span>
				</label>

			</div>

		</div>

		<div class="text-center mt-2 w-full">

			<div class="mr-2 flex-shrink inline-block pl-4"
				 x-show="datesOpen"
				 x-cloak>

				<div class="flex">

			    	<div class="py-8 px-2">
			    		Opened Between:
				    </div>

			    	<div class="p-2">
			    		<div class="text-xs text-grey-dark py-1">
			    			Start
			    		</div>
			    		<div>
				    		<input class="border rounded-lg mr-1 w-24 px-2 py-1 text-sm text-black"
				    			   type="text"
				    			   placeholder="{{ \Carbon\Carbon::now()->format('n/d/y') }}"
				    			   wire:model="openedStart" />
				    	</div>
				    </div>

			    	<div class="p-2">
			    		<div class="text-xs text-grey-dark py-1">
			    			End
			    		</div>
			    		<div>
				    		<input class="border rounded-lg mr-1 w-24 px-2 py-1 text-sm text-black"
				    			   type="text"
				    			   placeholder="{{ \Carbon\Carbon::now()->format('n/d/y') }}"
				    			   wire:model="openedEnd" />
				    	</div>
				    </div>

				</div>

			</div>

	    	<div class="mr-2 flex-shrink inline-block pl-4"
	    		 x-show="datesOpen"
	    		 x-cloak>

	    		<div class="flex">

			    	<div class="py-8 px-2">
			    		Resolved Between:
				    </div>

			    	<div class="p-2">
			    		<div class="text-xs text-grey-dark py-1">
			    			Start
			    		</div>
			    		<div>
				    		<input id="resolved_start"
				    			   class="border rounded-lg mr-1 w-24 px-2 py-1 text-sm text-black"
				    			   type="text"
				    			   placeholder="{{ \Carbon\Carbon::now()->format('n/d/y') }}"
				    			   wire:model="resolvedStart" />
				    	</div>
				    </div>

			    	<div class="p-2">
			    		<div class="text-xs text-grey-dark py-1">
			    			End
			    		</div>
			    		<div>
				    		<input class="border rounded-lg mr-1 w-24 px-2 py-1 text-sm text-black"
				    			   type="text"
				    			   placeholder="{{ \Carbon\Carbon::now()->format('n/d/y') }}"
				    			   wire:model="resolvedEnd" />
				    	</div>
				    </div>

				</div>

			</div>

			<div class="text-center">
				<span class="cursor-pointer text-blue text-sm"
					 @click="datesOpen = true"
					 x-show="!datesOpen"
					 x-cloak>
					Show Date Filter
				</span>
			</div>

		</div>


	</div>

	<div class="text-center text-sm pb-2">

		{{ $cases->links() }} 

	</div>

	<!------------------------------------/ DISPLAY /------------------------------------>

	<div class="flex">

		<div class="w-4/5 pr-4">

			@if(!$cases->first())

				<div class="py-2 font-bold pl-1">
					No Cases Found.
				</div>

			@else

				<div class="flex text-sm uppercase bg-blue border-blue-light border-b-2">

					<div class="px-2 py-1 text-grey-lightest truncate w-24 text-right">
						Date
					</div>

					<div class="py-2 text-grey-lightest w-12">
						
					</div>

					<div class="px-2 py-1 text-grey-lightest truncate w-4/5">
						Case
					</div>


				</div>

			@endif

			@foreach($cases as $case)

				<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">

					<div class="text-sm border-b border-dashed border-grey cursor-pointer hover:bg-orange-lightest">

						<div class="flex">

							<div class="px-2 py-4 truncate w-24 text-right">
								{{ \Carbon\Carbon::parse($case->created_at)->format('n/d/y') }}

								<div class="text-xs text-grey-dark">
									{{ $case->user->shortName }}
								</div>

								<div class="text-xs text-grey-dark capitalize font-bold">
									{{ $case->status }}
								</div>

							</div>

							<div class="py-4 truncate w-12 text-center text-base">
								@if ($case->status == 'resolved')
									<i class="fa fa-check-circle text-blue -ml-1"></i>
									<div class="text-xs text-grey uppercase">Done</div>
								@elseif ($case->priority == 'High')
									<i class="fas fa-star text-red w-6 -ml-1"></i>
									<div class="text-xs text-grey uppercase">High</div>
								@elseif ($case->priority == 'Medium')
									<i class="fas fa-star text-blue w-6 -ml-1"></i>
									<div class="text-xs text-grey uppercase">Med</div>
								@elseif ($case->priority == 'Low')
									<i class="fas fa-star text-grey w-6 -ml-1"></i>
									<div class="text-xs text-grey uppercase">Low</div>
								@endif
							</div>

							<div class="py-4 w-4/5 leading-relaxed">

								<div>

									@if($case->type)
										<span class="border text-blue mr-1 px-2 bg-grey-lighter text-xs uppercase font-bold">
											{{ $case->type }}
											@if($case->subtype)
												<span class="font-normal">{{ $case->subtype }}</span>
											@endif
										</span>
									@endif

									<span class="font-bold text-black">{{ $case->subject }}</span>

								</div>

								<div class="text-grey-dark mt-1 truncate">
									{{ $case->notes }}
								</div>

								@if($case->people)

									<div class="flex flex-wrap leading-loose mt-1">
										@foreach($case->people as $person)

											@if($person->is_household)

												<div class="inline mr-2 border-l border-r border-grey-light text-grey-darker px-2 text-xs rounded-lg mt-1 bg-white">		{{ $person->addressNoState }}
													@if($group_people->contains($person->id))
														<i class="fas fa-tag ml-1 text-blue"></i>
													@endif
												</div>

											@else

												<div class="inline mr-2 border-l border-r border-grey-light text-grey-darker px-2 text-xs rounded-lg mt-1 bg-white">		{{ $person->full_name }}
													@if($group_people->contains($person->id))
														<i class="fas fa-tag ml-1 text-blue"></i>
													@endif
												</div>

											@endif

										@endforeach

									</div>
								@endif

								@foreach ($case->entities as $entity)
									<div class="text-grey-darker m-1">
										<i class="fa fa-hotel"></i> {{ $entity->name }}
									</div>
								@endforeach

							</div>

						</div>

					</div>

				</a>

			@endforeach

		</div>

		<div class="pl-2 w-1/5">

			<div>

				<div class="font-medium text-sm bg-blue text-grey-lightest px-2 py-1 text-center border-b-2 border-blue-light">
					By User
				</div>

				<div>

					@if($user)

						<div class="border p-4 bg-grey-lightest mb-4 text-center"
							 wire:key="user_{{ $user }}">

							<div class="mb-4 text-sm">
								<div>
									<i class="fas fa-user-circle text-lg"></i>
								</div>
								<div class="">
									Showing cases for
								</div>
								<div class="font-bold">
									{{ \App\User::find($user)->name }}
								</div>
							</div>

							<button class="rounded-lg bg-blue text-white px-4 py-1 text-xs"
									wire:click="$set('user', null)">
								Show Whole Team
							</button>

						</div>

					@elseif($owner == 'team')

						@foreach($cases_unpaginated->groupBy('user_id') as $user_id => $user_cases)

							<div class="flex text-grey-dark text-xs border-dashed {{ (!$loop->last)? 'border-b' : '' }} cursor-pointer"
								 wire:click="$set('user', {{ $user_id }})">

								<div class="truncate border-r px-2 py-1 text-right" style="width:70px;">
									{{ number_format($user_cases->count()) }}
								</div>

								<div class="text-blue w-full truncate px-2 py-1">
									{{ $user_cases->first()->user->shortName }}
								</div>

							</div>

						@endforeach

						<div class="flex text-grey-black text-xs border-t-2 border-blue font-bold cursor-pointer">
							
							<div class="truncate px-2 py-1 bg-grey-lightest text-right" style="width:70px;">
								{{ number_format($cases_unpaginated->count()) }}
							</div>

							<div class="w-full truncate px-2 py-1 bg-grey-lightest">
								Team
							</div>

						</div>

					@endif

				</div>

			</div>

			@if($unresolved_cases)

				<div class="mt-6">

					<div class="font-medium text-sm bg-blue text-grey-lightest px-2 py-1 text-center border-b-2 border-blue-light mt-2">
						@if($user)
							<div class="font-bold">
								{{ \App\User::find($user)->name }}'s
							</div>
						@endif
						Oldest Unresolved ({{ number_format($unresolved_cases_count) }})
					</div>

					<div class="mt-2">

						@foreach($unresolved_cases as $case)

							<div class="text-sm mb-2">

								<div class="font-bold text-xs">
									{{ \Carbon\Carbon::parse($case->created_at)->diffForHumans() }}
								</div>
								<div class="ml-4 border-l pl-2 text-blue">
									<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">
										{{ $case->subject }}
									</a>
								</div>
							</div>

						@endforeach

					</div>

				</div>

			@endif

		</div>

	</div>

</div>
