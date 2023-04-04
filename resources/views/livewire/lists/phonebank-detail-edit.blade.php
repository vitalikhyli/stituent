<div class="md:flex w-full border-b-4 md:border-t-4 border-blue py-2">


	<div class="md:w-1/2 truncate py-2 pr-2">
	
		<div class="text-blue text-lg">
			{{ $voter->name }}

			@if($voter->is_participant)
				<i class="fa fa-check-circle text-blue ml-2"></i> 
			@endif

			@if(!Auth::user()->permissions->guest)
				<a href="/campaign/participants/{{ $voter->id }}/edit"
				   class="text-xs rounded-lg bg-grey-lightest text-grey-darkest hover:bg-blue hover:text-white px-2 py-1 border ml-2">
					Go to page
				</a>
			@endif

		</div>



		@if ($voter->archived_at)
			<i class="fa fa-archive hover:text-blue-500" style="cursor:help;" title="Voter is Archived. May be moved or deceased, but was NOT in most recent active voter file."></i>
		@endif

		<div>{{ $voter->address_line_street }}</div>
		<div>{{ $voter->address_city_zip }}</div>

		<div class="md:w-2/3">

		<div class="mb-1">
			<input type="text" autocomplete="off" wire:model.debounce.1000ms="data.{{ $voter->id }}.participant_email" class="form-control mt-2" placeholder="Email"/>

			<input type="text" autocomplete="off" wire:model.debounce.1000ms="data.{{ $voter->id }}.participant_phone" class="form-control mt-2" placeholder="Phone #"/>
		</div>

		
		@if($voter->email)
			<div class="py-1"><span class="font-bold">Email</span> {{ $voter->email }}</div>
		@endif
		@if($voter->readable_phone)
			<div class="py-1"><span class="font-bold">Phone</span> {{ $voter->readable_phone }}</div>
		@endif
		@if ($voter->cf_plus_phones)
			<div class="text-green">
				CF+ {{ $voter->cf_plus_phones }} <i class="fa fa-check-circle"></i>
			</div>
		@endif
		@if ($voter->cf_plus_cell)
			<div class="text-orange">
				CF+ CELL {{ $voter->cf_plus_cell }} <i class="fa fa-check-circle"></i>
			</div>
		@endif
			
		</div>

	</div>


	<div class="text-right md:w-1/2 truncate">

		<div class="flex w-full text-center p-2">
			
			<div class="pr-6">
				Support:
			</div>

			@if($voter->is_participant)
				
				@for ($i=1; $i<6; $i++)
					<div class="w-1/5 text-center">
						@if ($voter->support == $i)
							
							<div style="padding-top: 6px; margin-top: -6px; margin-left: 4px;"
								 class="{{ getSupportClass($i) }} text-white rounded-full mx-auto  w-8 h-8 center cursor-pointer"
								 wire:click="setSupport('{{ $voter->id }}', {{ $i }})">
								{{ $i }}
							</div>

						@else

							<div style="padding-top: 6px; margin-top: -6px; margin-left: 4px;"
								 class="cursor-pointer border rounded-lg w-8 h-8 "
								 wire:click="setSupport('{{ $voter->id }}', {{ $i }})">
								{{ $i }}
							</div>

						@endif
					</div>
					
				@endfor
				
			@else


				@for ($i=1; $i<6; $i++)
					<div class="w-1/5 cursor-pointer"
						 wire:click="setSupport('{{ $voter->id }}', {{ $i }})">
						{{ $i }}
					</div>
				@endfor
				
			@endif


		</div>
		
		<textarea wire:model.debounce.1000ms="data.{{ $voter->id }}.notes"
				 class="w-full form-control p-2 mr-2 mt-4"
				 rows="4"
				 placeholder="Current Campaign Notes"></textarea>

		<div class="py-2">
			<button class="rounded-lg bg-grey-lighter text-sm text-grey-darkest px-3 py-1 border"
				    wire:click="called('{{ $voter->id }}')">
				Called
			</button>
			<button class="rounded-lg bg-grey-lighter text-sm text-grey-darkest px-3 py-1 border"
				    wire:click="leftMessage('{{ $voter->id }}')">
				Left Message
			</button>
			<button class="rounded-lg bg-grey-lighter text-sm text-grey-darkest px-3 py-1 border"
				    wire:click="wrongNumber('{{ $voter->id }}')">
				Wrong #
			</button>
			<button class="rounded-lg bg-grey-lighter text-sm text-grey-darkest px-3 py-1 border"
				    wire:click="notInService('{{ $voter->id }}')">
				Not in service
			</button>

		</div>
		

	</div>

</div>