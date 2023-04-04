<div class="flex w-full items-center">



@if(!$searchMode)

    <div wire:loading class="">
        <i class="fas fa-sync fa-spin text-xl text-blue"></i>
    </div>


    <div wire:init="loadPosts" wire:loading.remove>

		@if(!$possible_matches->first())

			@if($model->voter_id)

				<button wire:click="unPickID()" class=" hover:bg-orange bg-orange text-white px-3 py-2 ml-2 mb-2 w-full text-left h-18">

	    			<i class="fas fa-check-circle"></i>

					{{ $model->voter_id }}

					<div class="text-xs mt-1 text-center">Click to Unlink</div>

				</button>

			@else

			    <div class="text-grey-dark">
			    	No suggestions.
			    </div>

			@endif

		@elseif($model->voter_id && !in_array($model->voter_id, $possible_matches->pluck('voter_id')->toArray()))

			<button wire:click="unPickID()" class="hover:bg-orange bg-orange text-white px-3 py-2 ml-2 mb-2 text-left h-18">

    			<i class="fas fa-check-circle"></i>

				{{ $model->voter_id }}

				<div class="text-xs mt-1 text-center">Click to Unlink</div>

			</button>

	    @endif

	    @foreach($possible_matches as $voter)

			<div class="flex text-sm w-full">

		    	@if($model->voter_id == $voter->id)

			    	<div>

						<button wire:click="unPickID()" class=" hover:bg-orange bg-orange text-white px-3 py-2 ml-2 mb-2 w-full text-left text-2xl h-18">

			    			<i class="fas fa-check-circle"></i>

							{{ $voter->id }}

						</button>

						<div class="ml-2 w-full border-2 border-blue bg-blue-lightest pt-2">
							
							@foreach($missing_data as $field => $val)
								@if(is_array($val))
									@continue
								@endif

								<div class="flex {{ (!$loop->last)? 'border-b' : '' }}">
									<div class="font-medium text-right pr-2 pb-1 w-32 text-grey-darker">{{ $field }}</div>
									<div class="text-blue pr-2">{{ $val }}</div>
								</div>

							@endforeach


							<div class="text-center">
								<button class="rounded-lg bg-blue hover:bg-blue-dark text-white m-2 px-3 text-lg py-2"
										wire:click="importVoterDataForLinkedPerson()">
									Import This Missing Voter Data
								</button>
							</div>

						</div>

					</div>


		    	@else

			        <button wire:click="pickID('{{ $voter->id }}')" class="hover:bg-blue hover:text-white bg-grey-lightest border text-grey-darkest px-3 py-2 ml-2 mb-2 w-full h-18">
			            
			           <div class="flex">
			                <div class="pr-4 font-normal border-r border-white text-xs">
			                    Click to Link:
			                </div>
			                <div class="pl-4 text-left">
			                    <div class="font-base flex text-base font-bold">
			                        {{ $voter->full_name }} ({{ $voter->age }}) {{ $voter->gender }}
			                    </div>
			                    {{ $voter->full_address }}
			                </div>

			            </div>
			            
			        </button>

			    @endif

				<div class="p-4 whitespace-no-wrap w-36 text-lg text-grey-dark">
			        {{ $voter->match_score }}%
			    </div>


			</div>

		@endforeach

	</div>

@endif

@if($searchMode)

	<div class="w-full">

		<div class="flex text-right w-full">

			<div class="flex-grow pr-2">

			@if($model->voter_id)

				<button wire:click="unPickID()" class=" hover:bg-orange bg-orange text-white px-3 py-2 mt-1 shadow text-left text-sm w-full h-18 whitespace-no-wrap text-center">

	    			<i class="fas fa-check-circle"></i>

					{{ $model->voter_id }}

					<div class="text-xs mt-1 text-center">Click to Unlink</div>

				</button>

			@endif

		</div>

			<div class="flex flex-shrink">
			    <input type="text"
			    	   wire:model.debounce="lookup"
			    	   placeholder="Search Voters"
			    	   class="border p-2 w-48 mt-1"
			    	   />

				<div wire:click="$set('searchMode', '0')" class="text-red cursor-pointer p-2 text-xl ml-2">
		    		<i class="fas fa-times"></i>
		    	</div>
		    </div>

		</div>

		<div class="mt-1">

			@if(!$results->first() && $lookup)
				<div class="font-medium py-2 border-b text-xs font-bold">
					None found.
				</div>
			@endif

	   		@foreach($results as $result)

	   			@if($result->id == $model->voter_id)

					<div class="flex text-xs {{ (!$loop->last) ? 'border-b' : '' }} cursor-pointer hover:bg-red-lightest"
		   				 wire:click="unPickID('{{ $result->id }}')">
		   				 
		   				<div class="py-1 pl-2">

							<button class="bg-orange text-white px-2 py-1 text-xs hover:bg-orange-darker w-12"
									type="button">
								Unlink
							</button>

		   				</div>

		   				<div class="p-1 pl-2 flex-grow">
		   					<div class="font-bold">
		   						{{ $result->full_name }}
		   					</div>
							<div class="truncate text-grey-dark">
			   					{{ $result->full_address }}
			   				</div>
		   				</div>

	   					<div class="text-grey-dark text-xs w-18 p-1 pl-2 pr-2 text-right">
   							{{ $result->age }}
   							@if($result->age && $result->gender)
   								&nbsp;-&nbsp;
   							@endif
   							{{ $result->gender }}
	   					</div>
		   				
		   			</div>


	   			@else

		   			<div class="flex text-xs {{ (!$loop->last) ? 'border-b' : '' }} cursor-pointer hover:bg-blue-lightest"
		   				 wire:click="pickID('{{ $result->id }}')">

		   				<div class="py-1 pl-2">

							<button class="bg-blue text-white px-2 py-1 text-xs hover:bg-blue-darker w-12"
									type="button">
								Link
							</button>

		   				</div>

		   				<div class="p-1 pl-2 flex-grow">
		   					<div class="font-bold">
		   						{{ $result->full_name }}
		   					</div>
							<div class="truncate text-grey-dark">
			   					{{ $result->full_address }}
			   				</div>
		   				</div>

	   					<div class="text-grey-dark text-xs w-18 p-1 pl-2 pr-2 text-right">
   							{{ $result->age }}
   							@if($result->age && $result->gender)
   								/
   							@endif
   							{{ $result->gender }}
	   					</div>

		   			</div>

		   		@endif

	   		@endforeach

	   	</div>

	</div>

@endif

	<div class="flex-grow text-right">

		@if(!$searchMode)

			<div wire:click="$set('searchMode', true)" class="text-blue cursor-pointer">
	    		<i class="fas fa-search"></i> Name Search
	    	</div>

    	@endif

    </div>


</div>
