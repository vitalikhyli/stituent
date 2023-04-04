@extends('campaign.base')

@section('title')
    Voters
@endsection

@section('style')

	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.css" rel="stylesheet"></link>
	@livewireStyles


@endsection


@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Voters</b>
@endsection

@section('main')


<div class="w-full">

	<div class="text-3xl font-bold border-b-4 pb-2 mb-3 flex">
		Voters & Participants


		<div class="flex-1 text-right font-normal flex-grow">
	
			<a href="/{{ Auth::user()->team->app_type }}/participants/new">
				<button class="rounded-lg bg-blue text-white p-2 text-sm uppercase px-4">
					<i class="fas fa-user mr-2"></i> New Participant
				</button>
			</a>

			<a href="/{{ Auth::user()->team->app_type }}/participants/export/{{ arrayToURL(request()->input()) }}">
				<button class="rounded-lg bg-grey-lightest text-blue p-2 text-sm uppercase px-4">
					<i class="fas fa-file-csv mr-2"></i> Export
				</button>
			</a>

		</div>

		<!-- <span class="text-blue">*</span> -->
	</div>

	<div class="flex border-b-4 border-blue pb-4 w-full">

		<div class="w-20 font-bold pr-4	 text-blue">
			Filter:
		</div>

		<div class="w-full">

			<div id="basic_search" class="w-full px-1">
					
				<form action="/{{ Auth::user()->team->app_type }}/voters" method="get" autocomplete="off">

					<!-- Make toggle -->
					<input type="hidden" name="include_archived" value="0" />

					<div class="flex h-8">

						<input type="text" autocomplete="off" value="{{ (request('first_name')) ? request('first_name') : '' }}" placeholder="First Name" name="first_name" id="first_name" class="rounded-lg border p-2 w-1/4 mr-2" />

						<input type="text" autocomplete="off" value="{{ (request('last_name')) ? request('last_name') : '' }}" placeholder="Last Name" name="last_name" id="last_name" class="rounded-lg border p-2 w-1/4" />

						<input type="text" autocomplete="off" value="{{ (request('street')) ? request('street') : '' }}" placeholder="Street Name" name="street" id="street" class="ml-2 rounded-lg border p-2 w-1/4" />

						<div class="w-1/4 px-2">
							<select class="slim-select" query_form="true" id="slim-select-municipality" multiple="multiple" name="municipalities[]" style="opacity:0;">
								<option data-placeholder="true"></option>
								@foreach ($municipalities as $municipality)
									<option {{ selectedIfInArray($municipality->id, request()->input(), 'municipalities') }} value="{{ $municipality->id }}">
										{{ $municipality->name }}
									</option>
								@endforeach
							</select>
						</div>


					</div>

					<div class="flex">

						<div class="py-4">

							<select name="filter_by_support" class="mr-2">
								<option value=""
									@if (request('filter_by_support') == '')
										selected
									@endif 
									>-- Filter by Support --
								</option>
								@foreach(['= 1 - Yes', 
										  '= 2 - Lean-yes',
										  '< 3 - Yes OR Lean-yes',
										  '= 3 - Undecided',
										  '> 3 - No OR Lean-no',
										  '= 4 - Lean-no',
										  '= 5 - No'
										  
										  ] as $level)
									<option value="{{ $level }}"
										@if (substr(request('filter_by_support'),0,3) == substr($level,0,3))
											selected
										@endif 
										>{{ $level }}
									</option>
								@endforeach
							</select>


							<select name="filter_by_tag" class="mr-2">
								<option value=""
									@if (request('filter_by_tag') == '')
										selected
									@endif 
									>-- Filter By Tag --
								</option>
								@foreach(\App\Tag::thisTeam()->get() as $tag)
									<option value="{{ $tag->id }}"
										@if (request('filter_by_tag') == $tag->id)
											selected
										@endif 
										>{{ $tag->name }}
									</option>
								@endforeach
							</select>

						</div>

						<div class="p-2">

							<label for="participants_only" class="font-normal mr-2 pt-2">
								<input type="checkbox" name="participants_only" id="participants_only" value="1" 
								@if (request('participants_only'))
									checked
								@endif 
								class="" />
								<span class="text-sm text-grey-darker">
									Only if interacted with (i.e. Participant)
								</span>
							</label>

						</div>

					</div>

					<div class="flex-grow text-right whitespace-no-wrap">

						<select name="sort_by" class="mr-2">

							@foreach(['' 			=> 'Last Name',
									  'address' 	=> 'Address',
									  'zip' 		=> 'Zipcode',
									  'precinct'	=> 'Ward/Precinct'
									  ] as $field => $choice)
								<option value="{{ $field }}"
										@if (request('sort_by') == $field)
											selected
										@endif 
										>Sort By {{ $choice }}</option>
							@endforeach

						</select>

						<select name="perpage" class="mr-2">
							<option value="100"
									@if (request('perpage') == 100)
										selected
									@endif 
									>100 Per Page</option>
							<option value="250"
									@if (request('perpage') == 250)
										selected
									@endif 
									>250 Per Page</option>
							<option value="500"
									@if (request('perpage') == 500)
										selected
									@endif 
									>500 Per Page</option>
							<option value="1000"
									@if (request('perpage') == 1000)
										selected
									@endif 
									>1,000 Per Page</option>
							<!-- <option value="all">All</option> -->
						</select>

						<input type="submit" name="go" value="Search" class="bg-blue text-white rounded-lg px-3 py-2 uppercase text-sm" />

						<input type="submit" name="clear" value="Clear" class="bg-red-lighter text-white rounded-lg px-3 py-2 uppercase text-sm float-right ml-1" />

					</div>

				</form>

			
			</div>

		</div>



	</div>

	<div class="text-xs font-bold text-blue float-right">
		<span class="font-normal">Query:</span> {{ number_format(abs(Carbon\Carbon::now()->milliseconds - session('voter_query_start')) / 1000, 2) }}s
	</div>

	@if(!$voters->first())

	<div class="flex mt-4">


		<div class="w-1/5 mr-2 pr-4">
			<div class="py-2 text-2xl font-bold text-blue">
				Quick Search:
			</div>

		</div>


		<div class="w-1/4 mr-2">
			<div class="border-b py-2 text-lg font-bold">
				Cities & Towns
			</div>

			@foreach($municipalities->map(function ($item) {
				return substr($item->name, 0, 1);
			})->unique() as $first_letter)

				<div class="flex {{ (!$loop->first) ? 'border-t mt-4' : '' }} ">

					<div class="font-bold text-2xl mt-1 w-12">
						{{ $first_letter }}
					</div>

				
					<div class="mt-1 w-full">
						@foreach($municipalities->reject(function($item) use ($first_letter) {
							return (substr($item->name, 0, 1) != $first_letter) ? true : false;
						}) as $city)
							<a href="?municipalities%5B%5D={{ $city->id }}">
								<div class="p-1 hover:bg-blue-lightest">
									{{ $city->name }}
								</div>
							</a>
						@endforeach
					</div>

				</div>

			@endforeach

		</div>


		<div class="w-1/4 mr-2">
			<div class="border-b py-2 text-lg font-bold mb-1">
				Tags
			</div>

			@foreach(\App\Tag::thisTeam()->get() as $tag)
				<a href="?filter_by_tag={{ $tag->id }}">
					<div class="p-1 hover:bg-blue-lightest">
						<i class="fas fa-tag mr-2 text-black"></i> {{ $tag->name }}
					</div>
				</a>
			@endforeach
		</div>

		<div class="w-1/4 mr-2">
			<div class="border-b py-2 text-lg font-bold mb-1">
				Support
			</div>

			@foreach(['Yes', 
					  'Lean Yes', 
					  'Undecided', 
					  'No', 
					  'Hard No'] as $label)
				<a href="?filter_by_support=%3D+{{ $loop->iteration }}">
					<div class="p-1 hover:bg-blue-lightest">
						<button class="{{ getSupportClass($loop->iteration) }} w-8 h-8 rounded-full mr-2">
							&nbsp;
						</button> {{ $label }}
					</div>
				</a>
			@endforeach
		</div>

	</div>

	@else

		<div>
			@include('campaign.participants.tag-list')
		</div>

		<!-------------------- PAGINATION TOP -------------------->
		<div class="flex">
			<div class="flex-grow">
				&nbsp;
			</div>
			<div class="flex-shrink">
				@if($voters instanceof \Illuminate\Pagination\Paginator )
					{{ $voters->appends($_GET)->links() }}
				@endif
			</div>
			<div class="flex-grow">
				&nbsp;
			</div>
		</div>
		<!-------------------------------------------------------->


		<table wire:loading.class="opacity-50" class="table text-grey-dark text-sm mt-8">

			<tr>
				@if(request('tag_with'))
					<th>Tag</th>
				@endif
				<th colspan="2" class="">
					
				</th>

				<th>Address</th>

				<th>Phone</th>

				<th class="text-center">
					<div class="w-5/6">
						Support 
						<!-- (1=YES, 5=NO) -->
					</div>
				</th>

			</tr>

			@php
				$last_city 		= null;
				$last_street 	= null;
				$last_zip		= null;
				$last_precinct  = null
			@endphp


			@foreach ($voters as $voter)

				@php
					if (request('page')) {
						$iteration = (request('page')-1)*100+$loop->iteration;
					} else {
						$iteration = $loop->iteration;
					}
				@endphp

				@if(request('sort_by') == 'address')

					@if(strtolower($voter->address_city) != $last_city || !$last_city)

						@php
							$last_city = strtolower($voter->address_city);
						@endphp

						<tr>
							<td colspan="6" class="bg-black text-grey-lightest text-lg font-bold border-b-2 py-2 uppercase">
								{{ $voter->address_city }}
							</td>
						</tr>

					@endif

					@if(strtolower($voter->address_street) != $last_street || 
						strtolower($voter->address_city) != $last_city || 
						!$last_street)

						@php
							$last_street = strtolower($voter->address_street);
						@endphp

						<tr>
							<td colspan="6" class="bg-grey-lightest pl-10 text-sm text-blue font-bold border-b-2 py-2 uppercase">
								{{ $voter->address_street }}
							</td>
						</tr>

					@endif

				@endif

				@if(request('sort_by') == 'zip')

					@if($voter->address_zip != $last_zip || !$last_zip)

						@php
							$last_zip = $voter->address_zip;
						@endphp

						<tr>
							<td colspan="6" class="bg-black text-grey-lightest text-lg font-bold border-b-2 py-2 uppercase">
								{{ $voter->address_zip }}
							</td>
						</tr>

					@endif

				@endif

				@if(request('sort_by') == 'precinct' && ($voter->precinct || $voter->ward))

					@if(strtolower($voter->ward.$voter->precinct) != $last_precinct || !$last_precinct)

						@php
							$last_precinct = strtolower($voter->ward.$voter->precinct);
						@endphp

						<tr>
							<td colspan="6" class="bg-black text-grey-lightest text-lg font-bold border-b-2 py-2 uppercase">

								<span class="text-grey">{{ $voter->address_city }}</span>

								@if($voter->ward)
									<span class="text-grey-dark">Ward</span> {{ $voter->ward }}
								@endif

								@if($voter->precinct)
									<span class="text-grey-dark">Pct</span> {{ $voter->precinct }}
								@endif

							</td>
						</tr>

					@endif

				@endif

				@livewire('participant-details', ['voter_or_participant' => $voter, 
												  'iteration' => $iteration, 
												  'edit' => false, 
												  'tag_with_id' => request('tag_with'),
												  ])

			@endforeach

		</table>

		<!-------------------- PAGINATION BOTTOM -------------------->
		<div class="flex">
			<div class="flex-grow">
				&nbsp;
			</div>
			<div class="flex-shrink">
				@if($voters instanceof \Illuminate\Pagination\Paginator )
					{{ $voters->appends($_GET)->links() }}
				@endif
			</div>
			<div class="flex-grow">
				&nbsp;
			</div>
		</div>
		<!----------------------------------------------------------->

	@endif

</div>


@endsection


@section('javascript')

	@livewireScripts

	<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script>

	<script type="text/javascript">
		createSlimSelects();
		function createSlimSelects() {
			new SlimSelect({
			  select: '#slim-select-municipality',
			  placeholder: 'Municipalities'
			})
			$(".slim-select").css('opacity', 100);
		}
	</script>

@endsection
