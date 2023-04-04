<div class="mt-4">

		<div class="text-xl font-bold">
			Invite Volunteers

			@if($opp->invited->first())
				<i class="fas fa-check-circle text-blue ml-1 text-base"></i>
			@endif

			<div class="float-right bg-grey-lightest border px-4 py-2 text-base w-1/2">

				<input type="text"
					   class="border-2 bg-white p-2 w-full"
					   placeholder="Lookup Volunteers"
					   wire:model.debounce="filter_volunteers"
					   id="filter_volunteers" />

			</div>

		</div>

		<div>

			<label for="subscribable"
				   class="font-normal">

				<input type="checkbox"
					   id="subscribable"
					   wire:model="subscribable" />

				<span class="ml-1">
					Allow any volunteer to join / invite themselves
				</span>

			</label>


		</div>

		<div class="flex mt-2 pl-8 border-l-4">


			<div class="w-1/2 pr-4">

				@if(!$opp->invited->first())

					<div class="text-red">
						None
					</div>

				@endif

				@foreach($opp->invited as $volunteer)

					<div class="px-2 py-1 border-b cursor-pointer text-blue">


						<i class="fas fa-times w-4 ml-1 text-red float-right"
						   wire:click="uninvite({{ $volunteer->id }})"></i>

						   <i class="fas fa-user w-4 mr-1 text-blue"></i>

						{{ $volunteer->email }}

						@if($volunteer->pivot->emailed_at)

							<div class="float-right text-sm -mt-1 font-semibold px-2 py-1 text-black">
								Emailed
							</div>

						@endif


					</div>

				@endforeach

			</div>

			<div class="w-1/2 pl-4">


				@if(!$volunteer_options->first())

					<div class="text-grey-dark">
						No Volunteers to Invite
					</div>

				@endif

				@foreach($volunteer_options as $volunteer)

					<div class="px-2 py-1 border-b cursor-pointer flex hover:bg-orange-lightest"
						 wire:click="invite({{ $volunteer->id }})">
						
						<div class="w-6">
							<i class="fas fa-arrow-circle-left w-4 mr-1 text-blue"></i>
						</div>

						<div class="truncate font-semibold w-1/3">
							{{ $volunteer->username }}
						</div>

						<div class="truncate text-grey-dark w-1/2">
							{{ $volunteer->email }}
						</div>

					</div>

				@endforeach
				

			</div>
			
		</div>

	</div>