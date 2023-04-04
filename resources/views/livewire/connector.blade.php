<div class="relative">
	<div class="absolute pin-t pin-r border text-xs px-2 cursor-pointer bg-white" wire:click="toggleEditing()">
		@if ($editing)
			Done
		@else
			Add/Edit
		@endif
	</div>
	<div class="flex flex-wrap text-grey-dark">

		

		@if (!$editing)

			@if ($linked->count() > 0)

				<table class="w-full">

				@foreach($linked as $link)

					<tr x-data="{ tooltip: false }"
			   		     class="">


				   			@if($link->in_team)

				   				
				   				<td class="pb-2">

							   			<a class="font-bold" href="/{{ Auth::user()->team->app_type }}/{{ $url_dir }}/{{ $link->id }}">
							   				{{ $link->full_name }}
							   				
							   			</a>

							   			
							   	
							   		@if ($details)
							   			@if ($link->gender || $link->age)
							   				({{ $link->age }}{{ $link->gender }})
						   				@endif
						   				
							   			@if ($groups_loaded)
								   			@if(get_class($model) == 'App\WorkCase')

												@if($link->team->groups->first())
													
													@include('livewire.connector-groups-include')
													

												@endif

											@endif
										@endif

								   		
					   				@endif

					   				<!-- <div class="" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false" wire:mouseenter="loadGroups()">Groups</div> -->
							   	</td>
							   	

						   			

						   		</td>
						   		<td class="pb-2">
						   			@if ($details)
						   				<a href="" class="pull-right">
						   					View
						   				</a>
							   			@if ($link->address_city)
								   			@if ($link->address_number)
								   				{{ $link->address_number }} {{ $link->address_street }}
								   			@endif
								   			{{ $link->address_city }}<br>
							   			@endif
							   			@if ($link->email)
							   				{{ $link->email }}
							   			@endif
							   			@if ($link->phone)
							   				{{ $link->phone }}
							   			@endif
						   			@endif
						   		</td>
							

								


					   		@elseif($link->in_voter_file)

					   			<a href="/{{ Auth::user()->team->app_type }}/{{ $url_dir }}/{{ $link->voter_id }}">
					   				{{ $link->full_name}}
					   			</a>

							    <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
							    	<div class="absolute top-0 z-10 w-64 px-4 py-2 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">
							        	<div class="font-bold">{{ $link->full_name }}</div>
							        	<div class="text-sm">was added by team "{{ \App\Team::find($link->team_id)->name }}."</div>
							    	</div>
							    </div>

					   		@else

					   			{{ $link->full_name }}

							    <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
							    	<div class="absolute top-0 z-10 w-64 px-4 py-2 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">
							        	<div class="font-bold">{{ $link->full_name }}</div>
							        	<div class="text-sm">is not in Your Voter File.</div>
							    	</div>
							    </div>

					   		@endif
					   	</div>

			   		</tr>

				@endforeach

				</table>

			@else
				<div class="italic text-grey pb-2">
					None
				</div>
			@endif

		@else

		  <div>

	   		@if($lookup)

	   			<div class="border-2 border-grey-dark p-6 shadow bg-white absolute z-10 mt-10">


				    <button class="rounded-lg px-2 py-1 float-right"
				    		 wire:click="$set('lookup', '')"
				    		 type="button">
				    	<i class="fas fa-times text-2xl"></i>
				    </button>

		   			<div class="w-5/6 mb-2">

					@if(Auth::user()->team->app_type == 'office' 
						&& !Auth::user()->permissions->createconstituents)

						<!-- Does not have authority to create new constituents -->

					@else

		   				<div class="font-medium text-sm">

		   					<div class="font-bold border-b pb-1">
		   						Add + Link a New {{ $description_singular }}:
		   					</div>

		   					<div class="p-2 bg-grey-lightest">
		   						<button class="rounded-lg bg-blue text-white px-2 py-1 text-sm hover:bg-blue-darker capitalize"
		   								wire:click="createNew()"
		   								type="button">
		   							Create "{{ $lookup }}"
		   						</button>
		   					</div>

		   				</div>

		   			@endif

		   			</div>


		   			<div style="width:600px;">

		   				@if($results->first())
		   					<div class="font-medium py-2 border-b text-sm font-bold">
		   						Link an Existing {{ $description_singular }}:
		   					</div>
		   				@else
		   					<div class="font-medium py-2 border-b text-sm font-bold">
		   						None found.
		   					</div>
		   				@endif

				   		@foreach($results as $result)

					   		@if($result->linked)
					   			<div class="flex text-sm {{ (!$loop->last) ? 'border-b' : '' }} cursor-pointer"
					   				 wire:click="unlink('{{ $result->id }}')">

					   				<div class="py-1">

				   						<button class="rounded-lg bg-red text-white px-2 py-1 text-sm hover:bg-red-darker"
				   								type="button">
				   							Unlink
				   						</button>

					   				</div>

					   				<div class="p-1 pl-2 flex-grow opacity-50">
					   					{{ $result->full_name }}
					   				</div>

					   				<div class="p-1 pl-2 w-1/3 text-grey truncate opacity-50">
					   					{{ $result->full_address }}
					   				</div>
					   			</div>
					   		@endif

					   		@if(!$result->linked)
					   			<div class="flex text-sm {{ (!$loop->last) ? 'border-b' : '' }} cursor-pointer"
					   				 wire:click="link('{{ $result->id }}')">

					   				<div class="py-1">

				   						<button class="rounded-lg bg-blue text-white px-2 py-1 text-sm hover:bg-blue-darker"
				   								type="button">
				   							Link
				   						</button>

					   				</div>

					   				<div class="p-1 pl-2 flex-grow">
					   					{{ $result->full_name }}
					   				</div>

					   				<div class="p-1 pl-2 truncate w-1/3">
					   					{{ $result->full_address }}
					   				</div>
					   			</div>
					   		@endif
				   		@endforeach

				   	</div>

				</div>

			@endif

	   	</div>

			@if(!$search_goes_at_the_end)
				<div>
				    <input type="text"
				    	   wire:model="lookup"
				    	   placeholder="Search {{ $description }}"
				    	   class="border p-2"
				    	   />

				</div>
			@endif

			@if($display_count)
				<div class="font-medium p-2">
					<span class="font-bold">
						{{ number_format($display_count) }}
					</span> {{ $description }}
				</div>
			@endif



		    @foreach($linked as $link)

		   		<div x-data="{ tooltip: false }"
		   		     class="rounded-lg border bg-orange-lightest px-2 py-1 m-1 text-sm my-1 cursor-pointer">


			   			@if($link->in_team)

				   			<i class="fas fa-times text-red mr-2" wire:click="unlink('{{ $link->id }}')"></i>
				   			<a href="/{{ Auth::user()->team->app_type }}/{{ $url_dir }}/{{ $link->id }}">
				   				{{ $link->full_name}}
				   			</a>




				   		@elseif($link->in_voter_file)

				   			<a href="/{{ Auth::user()->team->app_type }}/{{ $url_dir }}/{{ $link->voter_id }}">
				   				{{ $link->full_name}}
				   			</a>

						    <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
						    	<div class="absolute top-0 z-10 w-64 px-4 py-2 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">
						        	<div class="font-bold">{{ $link->full_name }}</div>
						        	<div class="text-sm">was added by team "{{ \App\Team::find($link->team_id)->name }}."</div>
						    	</div>
						    </div>

				   		@else

				   			{{ $link->full_name }}

						    <div class="relative" x-cloak x-show.transition.origin.top="tooltip">
						    	<div class="absolute top-0 z-10 w-64 px-4 py-2 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg">
						        	<div class="font-bold">{{ $link->full_name }}</div>
						        	<div class="text-sm">is not in Your Voter File.</div>
						    	</div>
						    </div>

				   		@endif
				   	</div>

		   		</div>
		    @endforeach

			@if($search_goes_at_the_end)
				<div>
				    <input type="text"
				    	   wire:model="lookup"
				    	   placeholder="Search {{ $description }}"
				    	   class="border p-2"
				    	   />

				</div>
			@endif

		@endif
    </div>

</div>