<div class="relative">
	<div class="absolute pin-t pin-r border text-xs px-2 cursor-pointer" wire:click="toggleEditing()">
		@if ($editing)
			Done
		@else
			Add/Edit
		@endif
	</div>


	@if (!$editing)
		@if ($linked->count() > 0)
			<table class="w-4/5">

				@foreach($linked as $link)

			 		<tr class="">
			 			<td>
				   			<a class="font-bold" href="/{{ Auth::user()->team->app_type }}/households/{{ $link->id }}">
				   				{{ ucwords(strtolower($link->addressNoState)) }}
				   			</a>
				   		</td>
				   		@if ($details)
					   		<td>
					   			{{ $link->household_count }} Residents
					   		</td>
				   		@endif
				   	</tr>

				 @endforeach

			</table>
		@else
			<span class="italic text-grey">
				None
			</span>
		@endif

	@else

		<div>

			<div class="flex inline-flex flex-wrap">

				<div>
				    <input type="text"
				    	   wire:model="search"
				    	   placeholder="Search Households"
				    	   class="border p-2"
				    	   />

				</div>

				@foreach($linked as $link)

			 		<div class="rounded-lg border bg-blue-lightest px-2 py-1 m-1 text-sm my-1 cursor-pointer">
			   			<a href="/{{ Auth::user()->team->app_type }}/households/{{ $link->id }}">
			   				<i class="fas fa-home mr-1"></i>
			   				{{ ucwords(strtolower($link->addressNoState)) }}
			   			</a>

				   		<i class="fas fa-times text-red ml-1" wire:click="unlink('{{ $link->id }}')"></i>
				   	</div>

				 @endforeach

			</div>


		</div>

		@if($createNew)


			<div class="border-2 border-grey-dark px-4 pb-4 pt-2 shadow bg-white absolute z-10" style="width:500px;">

				<div class="text-right">

				    <button class="rounded-lg px-2 py-1 -mr-2"
				    		 wire:click="$set('createNew', false)"
				    		 type="button">
				    	<i class="fas fa-times text-2xl"></i>
				    </button>

				</div>

				<div class="text-2xl border-b-4 pb-1 -mt-2">
					{{ $revised_search }}
				</div>

				<div class="text-sm py-1 text-grey-darker">
					<span class="font-bold text-black">Confirm Address.</span> Getting the parts of an address right helps with the efficiency of the system and enhances your data.
				</div>

				<form wire:submit.prevent="confirmCreateNewHousehold()">

					<div class="py-2 font-mono flex">

						<div class="font-bold p-2 pr-4 text-sm text-blue">
							Line 1
						</div>

						<div>

							<div class="flex">
								<div class="bg-grey-light p-2 w-24">
									Number
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.number" />
								</div>
							</div>

							<div class="flex">
								<div class="bg-grey-lighter p-2 w-24">
									Fraction
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.fraction" />
								</div>
							</div>

							<div class="flex">
								<div class="bg-grey-lightest p-2 w-24">
									Street
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.street" />
								</div>
							</div>

							<div class="flex">
								<div class="bg-grey-lightest p-2 w-24">
									Apt
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.apt" />
								</div>
							</div>

						</div>

					</div>

					<div class="py-2 font-mono flex">

						<div class="font-bold p-2 pr-4 text-sm text-blue">
							Line 2
						</div>

						<div>

							<div class="flex mt-2">
								<div class="bg-grey-light p-2 w-24">
									City
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.city" />
								</div>
							</div>

							<div class="flex">
								<div class="bg-grey-lighter p-2 w-24">
									State
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.state" />
								</div>
							</div>

							<div class="flex">
								<div class="bg-gren-lightest p-2 w-24">
									Zip
								</div>
								<div>
									<input type="text"
										   class="text-blue border-b border-grey-light p-2"
										   wire:model="guess.zip" />
								</div>
							</div>

						</div>

					</div>



					<div class="hidden">
						{{ print_r($guess)}}
					</div>

					<div class="text-center mt-6">

						<button type="submit"
								class="rounded-lg bg-blue text-white px-6 py-2 text-xl shadow border border-grey-darker hover:bg-blue-darker">
							Create and Link
						</button>

					</div>

				</form>

			</div>

		@elseif($search)

			<div class="border-2 border-grey-dark px-4 pb-4 pt-2 shadow bg-white absolute z-10" style="width:500px;">

				<div class="text-right">

				    <button class="rounded-lg px-2 py-1 -mr-2"
				    		 wire:click="$set('search', '')"
				    		 type="button">
				    	<i class="fas fa-times text-2xl"></i>
				    </button>

				</div>

				<div class="py-2 border-b mb-2 border-dashed border-grey -mt-2">
					<button class="rounded-lg bg-blue-darker text-white px-2 py-1 text-sm hover:bg-blue-darkest capitalize"
							wire:click="createNewHousehold()"
							type="button">
						Create New <i class="fas fa-home"></i>: "{{ $search }}"
					</button>
				</div>

				@if(!$hhs->first())

					<div class="py-2">
						No matches found.
					</div>

				@endif

			    @foreach($hhs as $hh)

				    <div class="flex py-1 border-b border-dashed cursor-pointer
							    @if(in_array($hh, $linked_list))
						 			bg-blue-lightest
						 		@endif
						 		">

						 <div class="w-16 mr-3">

							<button class="rounded-lg bg-blue text-white px-2 py-1 text-sm mr-2 hover:bg-blue-darker text-xs
									@if(in_array($hh, $linked_list))
							 			hidden
							 		@endif"
									wire:click="linkHousehold('{{ base64_encode($hh) }}')">
								Choose
							</button>

						</div>
				    	
				    	<div class="flex-grow truncate text-grey-darker uppercase">
				    		{!! str_ireplace($search, '<span class="bg-blue-lighter border">'.strtoupper($search).'</span>', $hh) !!}
				    	</div>

				    </div>

			    @endforeach

			</div>

		@endif
	@endif
</div>
