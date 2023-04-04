<div class="flex text-grey-dark text-sm">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['elections'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Elections
					</div>
				@else
					Elections
				@endif
			</div>

			<div class="flex-grow">

				@if(count($input['elections']) > 1)

					<div class="pt-2" wire:key="electionCountSelect">
						
						<select wire:model="input.electionsCount" class="" >

							<!-- <option value="">- # -</option> -->

							@for($t = count($input['elections']); $t>0; $t--)
								<option value="{{ $t }}">
									@if($t == count($input['elections']))
										All
									@else
										Any
									@endif
									{{ $t }}
								</option>
							@endfor

						</select>

						of these

					</div>					

				@endif

				<div class="w-full flex px-2 pt-2" wire:ignore wire:key="electionsSelect">

					@php(krsort($elections))	

					<select wire-select-model="input.elections" class="select2 w-full" multiple>

						<option value="">- Select Elections -</option>

						@foreach ($elections as $election_id => $election_arr)
							<option value="{{ $election_id }}">{{ $election_arr['name'] }} ({{ $election_arr['count'] }}%)</option>
						@endforeach

					</select>

				</div>


			</div>

		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['reliability']['state'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Statewide Reliability
					</div>
				@else
					Statewide Reliability
				@endif

			</div>
			<div class="w-2/3">

				<div class="w-full flex px-2">
				
					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.state" value="somewhat" />
						Occasional
					</label>

					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.state" value="reliable" />
						Reliable
					</label>

					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.state" value="stalwart" />
						Super Voter
					</label>

					@if ($input['reliability']['state'])
						<label class="font-normal pt-2 mr-6">
							<input type="radio" wire:model="input.reliability.state" value="" />
							Clear
						</label>
					@endif

				</div>


				<div wire:key="explain_state">
					<div class="w-full flex">
						<div class="pr-4">
						    <button class="text-blue bg-blue-lightest hover:bg-blue-light hover:text-grey-lighter text-xs py-1 px-2 rounded whitespace-no-wrap w-16"
						    	    wire:click="$toggle('explain_state')">
						    	@if(!$explain_state)
						    		EXPLAIN
						    	@else
						    		GOT IT
						    	@endif
						    </button>
						</div>
					    <div class="{{ ($explain_state) ? '' : 'hidden' }} border-l-4 border-blue pl-4">
					        <div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Occasional:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">2</span> <span class="underline">state</span> elections in the past 8 years
					        	</div>
					        </div>
					        <div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Reliable:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">3</span> <span class="underline">state</span> elections in the past 8 years
					        	</div>
					        </div>
					       	<div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Super Voter:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">4</span> <span class="underline">state</span> elections in the past 8 years
					        	</div>
					        </div>
					    </div>
					</div>
				</div>

			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['reliability']['local'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Local Reliability
					</div>
				@else
					Local Reliability
				@endif
			</div>
			<div class="w-2/3">

				<div class="w-full flex px-2">


					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.local" value="somewhat" />
						Occasional
					</label>

					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.local" value="reliable" />
						Reliable
					</label>

					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.local" value="stalwart" />
						Super Voter
					</label>

					@if ($input['reliability']['local'])
					<label class="font-normal pt-2 mr-6">
						<input type="radio" wire:model="input.reliability.local" value="" />
						Clear
					</label>
					@endif

				</div>

				<div wire:key="explain_state">
					<div class="w-full flex">
						<div class="pr-4">
						    <button class="text-blue bg-blue-lightest hover:bg-blue-light hover:text-grey-lighter text-xs py-1 px-2 rounded whitespace-no-wrap w-16"
						    	    wire:click="$toggle('explain_local')">
						    	@if(!$explain_local)
						    		EXPLAIN
						    	@else
						    		GOT IT
						    	@endif
						    </button>
						</div>
					    <div class="{{ ($explain_local) ? '' : 'hidden' }} border-l-4 border-blue pl-4">
					        <div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Occasional:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">2</span> <span class="underline">local</span> elections in the past 8 years
					        	</div>
					        </div>
					        <div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Reliable:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">3</span> <span class="underline">local</span> elections in the past 8 years
					        	</div>
					        </div>
					       	<div class="text-blue mb-2">
					        	<div class="font-bold text-black">
					        		Super Voter:
					        	</div>
					        	<div class="">
					        		Voted in at least <span class="font-bold">4</span> <span class="underline">local</span> elections in the past 8 years
					        	</div>
					        </div>
					    </div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['frequency']['state']['times'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Statewide Frequency
					</div>
				@else
					Statewide Frequency
				@endif
			</div>
			<div class="w-2/3">

				<div class="w-full flex px-2 pt-2">
				

					Voted at least
					<select class="mx-1" wire:model="input.frequency.state.times">
						<option value="">- # -</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					times 
					@if ($input['frequency']['state']['times'])
					since
					<select class="mx-1" wire:model="input.frequency.state.year">
						<option value="">- Select Year -</option>
						<option value="2018">2018</option>
						<option value="2016">2016</option>
						<option value="2014">2014</option>
						<option value="2012">2012</option>
					</select>
					@endif


				</div>


			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['frequency']['local']['times'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Local Frequency
					</div>
				@else
					Local Frequency
				@endif
			</div>
			<div class="w-2/3">

				<div class="w-full flex px-2 pt-2">
				

					Voted at least
					<select class="mx-1" wire:model="input.frequency.local.times">
						<option value="">- # -</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					times 
					@if ($input['frequency']['local']['times'])
					since
					<select class="mx-1" wire:model="input.frequency.local.year">
						<option value="">- Select Year -</option>
						<option value="2018">2018</option>
						<option value="2016">2016</option>
						<option value="2014">2014</option>
						<option value="2012">2012</option>
					</select>
					@endif


				</div>


			</div>
		</div>

		<div class="flex pt-1">
			<div class="w-1/3 uppercase text-sm pt-2 relative">
				@if ($input['primary_ballot']['year'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Primary Voters
					</div>
				@else
					Primary Voters
				@endif
			</div>
			<div class="w-2/3">

				<div class="w-full flex px-2 pt-2">
				
					<select class="mx-1" wire:model="input.primary_ballot.year">
						<option value="">- Select Year -</option>
						<option value="2020">2020</option>
						<option value="2018">2018</option>
						<option value="2016">2016</option>
					</select>

					@if ($input['primary_ballot']['year'])
						<select class="mx-1" wire:model="input.primary_ballot.party">
							<option value="">- Party Ballot -</option>
							<option value="D">Democratic Ballot</option>
							<option value="R">Republican Ballot</option>
						</select>
					@endif


				</div>


			</div>
		</div>

		@if(Auth::user()->permissions->developer)
				<!-- <pre>
					{{ print_r($input['flexqueries']) }}
				</pre> -->

			<div class="flex p-1 border-t-4 mt-4 bg-blue-lightest">


				<div class="w-1/5 uppercase text-sm p-4 relative">
					Voted in:
				</div>

				<div class="w-4/5">

					@foreach($input['flexqueries'] as $key => $q)

						<div class="mt-1 pt-1 flex">
							
							<div class="w-12 pr-2 py-2">
								@if(!$loop->first)
									AND
								@else
									<!-- >= -->
								@endif
							</div>

							<select class="p-2 border-2 w-48" wire:model="input.flexqueries.{{ $key  }}.type">
								<option value="">-- Choose Type </option>

								@foreach($election_types as $type => $label)
									<option value="{{ $type }}">{{ $label }}</option>
								@endforeach

							</select>

							<div class="p-2">>=</div>

							<input type="text" class="p-2 border-2 w-16" wire:model="input.flexqueries.{{ $key }}.num" />

							<div class="p-2">times since</div>

							<input type="text" class="p-2 border-2 w-16" wire:model="input.flexqueries.{{ $key }}.year" />

							<button class="rounded-lg text-red px-2 py-1" wire:click="deleteFlexQuery('{{ $key }}')">
								X
							</button>

						</div>

					@endforeach

					<div class="{{ (count($input['flexqueries']) > 0) ? 'border-t-4' : '' }} mt-2 pt-1 flex">

						<div class="w-12 p-2 text-blue">
							<!-- ADD: -->
						</div>

						<select class="p-2 border-2 w-48"
								wire:model="input.flexqueries.{{ max(
								array_merge(
									array_keys($input['flexqueries']),
									[0]
								)
								) + 1 }}.type">
							<option value="">-- Choose Type </option>
							@foreach($election_types as $type => $label)
								<option value="{{ $type }}">{{ $label }}</option>
							@endforeach
						</select>
					</div>

				</div>

			</div>

		@endif

	</div>
</div>