<div>
	<div class="no-bills"></div>
	<div class="flex w-full text-sm font-bold items-center">
		<div class="w-1/3 sm:w-1/8 text-blue hover:text-blue-dark cursor-pointer">
			<div wire:click="prevMonth()">
				&laquo; {{ \Carbon\Carbon::parse($date)->subMonth()->format('M Y') }}
			</div>
		</div>
		<div class="w-1/3 sm:w-3/4 text-center">
			<div class="text-2xl font-sans w-full font-bold">
				 <i class="fa fa-birthday-cake fa-2x text-blue"></i> <br>
				 {{ \Carbon\Carbon::parse($date)->format('F Y') }} 
				 <span class="">
				 	@lang('Birthdays')
				 </span>
				 <span class="text-xl text-green font-italic -mt-4">
				 	BETA
				 </span>

			</div>
		</div>
		<div class="w-1/3 sm:w-1/8 text-right text-blue hover:text-blue-dark cursor-pointer">
			<div wire:click="nextMonth()">
				{{ \Carbon\Carbon::parse($date)->addMonth()->format('M Y') }} &raquo;
			</div>
		</div>
	</div>

	@if ($editing)
	<div class="w-full">
		<div class="mt-4 flex mx-auto text-xs">
			<div class="mr-4">
				<select wire:model="show" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="500">Show 500</option>
					<option value="100">Show 100</option>
					<option value="1000">Show 1000</option>
					<option value="2000">Show 2000</option>
				</select>
			</div>

			<div class="mr-4">
				<select wire:model="linked" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="">Residents</option>
					<option value="linked">Contacted Office</option>
				</select>
			</div>

			@if (\Carbon\Carbon::parse($this->date)->startOfMonth() == \Carbon\Carbon::today()->startOfMonth())
			<div class="mr-4">
				<select wire:model="past_birthdays" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="false">Upcoming</option>
					<option value="true">All Month</option>
				</select>
			</div>
			@endif

			<div class="mr-4">
				<select wire:model="year_interval" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="5">5-year</option>
					<option value="10">10-year</option>
					<option value="1">Any Age</option>
				</select>
			</div>

			<div class="mr-4">
				<select wire:model="municipality" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="">All Towns</option>
					@foreach ($municipalities as $municipality_obj)
						<option value="{{ $municipality_obj->id }}">
							{{ $municipality_obj->name }}
						</option>
					@endforeach
				</select>
			</div>

			
			
			<div class="mr-4">
				<select wire:model="sort_by" class="border rounded-lg uppercase text-blue font-bold py-1 px-2">
					<option value="dob">Sort By Age</option>
					<option value="birth_date, dob">Sort By Date</option>
				</select>
			</div>

			

			
			
		</div>
	</div>
	@endif


	<div class="w-full text-center mt-4 cursor-pointer text-grey-darker hover:text-black" wire:click="toggleEditing">
		<div class="py-1">
			Showing 
			
			<b>
			@if ($show == $voters->count())
				the first {{ $show }}
			@else
				{{ $voters->count() }}
			@endif
			</b>

		
			@if ($linked)
				<b>linked {{ Str::plural('person', $voters->count()) }}</b> (linked = contacted the office)
			@else
				{{ Str::plural('residents', $voters->count()) }}
			@endif
			<br>

			with
			<b>
			@if (!$past_birthdays)
				upcoming
			@endif
			@if ($year_interval > 1)
				{{ $year_interval }}-year
			@endif
			</b>
			birthdays
			
			in <b>{{ \Carbon\Carbon::parse($date)->format('F \'y') }}</b>

			<br>
			from
			<b>
			@if ($municipality)
				{{ \App\Municipality::find($municipality)->name }}</b>,
			@else 
				any town</b>,
			@endif

			sorted by 
			
			@if ($sort_by == 'dob')
				eldest age.
			@else
				upcoming date.
			@endif
			<br>
			<span class="italic text-xs text-blue-light">
				@if (!$editing)
					Click to Edit
				@else
					Click to finish Editing
				@endif
			</span>
		</div>
	</div>

    <div class="flex border-b-4 pb-2 border-blue text-xs mt-4">
		
		<div class="w-5/6 text-grey-dark">
			* NOTICE: We have removed anyone marked as <b>deceased</b> in official voting records, but we do not update regularly with death notices or obituaries.
			Please be aware that these persons could be deceased since the last voter file, and use at your discretion.

		</div>

		@if(Auth::user()->permissions->export)
			<div class="w-1/6">
				<div class="cursor-pointer rounded-lg text-sm px-2 border py-1 ml-4 text-center whitespace-no-wrap"
						wire:click="export()">
					Export
				</div>
			</div>
		@endif

	</div>



	<div wire:loading.class="opacity-50" class="w-full">
		
		<table class="table text-grey-dark text-sm">
			@foreach ($voters as $voter)
				@if ($voter->dob->setYear($year) == \Carbon\Carbon::today())
					<tr class="bg-yellow-lighter">
				@else
					<tr>
				@endif
					<td class="text-grey">{{ (($voters->currentPage() - 1) * 500) + $loop->iteration }}.</td>
					<td class="whitespace-no-wrap">
						<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $voter->id }}">
			        		{{ $voter->name }}
			        	</a>
			        </td>
			        <td class="text-black whitespace-no-wrap"> 
						@if ($voter->dob->setYear($year) < \Carbon\Carbon::today())
							Turned <b>{{ $voter->upcoming_age }}</b> on {{ $voter->dob->format('n/j') }}
						@elseif ($voter->dob->setYear($year) == \Carbon\Carbon::today())
							Turning <b>{{ $voter->upcoming_age }}</b> today
						@else
							Turning <b>{{ $voter->upcoming_age }}</b> on {{ $voter->dob->format('n/j') }}
						@endif
					</td>
					<td>
						@if ($voter->dob->setYear($year) != \Carbon\Carbon::today())
							{{ $voter->dob->setYear($year)->diffForHumans() }}
						@endif
					</td>
					<td class="whitespace-no-wrap">
						{{ $voter->full_address }}
					</td>

					<td> 

						<!-- include('shared-features.constituents.activity-icons', ['person' => $voter]) -->
					</td>
					
				</tr>
			@endforeach
		</table>
		<div class="mx-auto text-center">
			
		</div>
	</div>
</div>
