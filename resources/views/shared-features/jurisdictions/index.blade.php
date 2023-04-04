@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Jurisdictions')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>@lang('Jurisdictions')</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue flex">

	<div class="text-2xl font-sans flex-shrink">
		 @lang('Jurisdictions') in {{ session('team_state') }}
	</div>

</div>


<!-- <div class="italic font-bold py-2">
	All jurisdictions in {{ session('team_state') }} and the voters who are in your voter file.
</div> -->

<a href="?show=all">
	<button class="{{ (isset($_GET['show']) && $_GET['show'] == 'all') ? 'bg-blue text-white' : 'bg-grey-lighter text-grey-darker' }} px-4 py-1 pb-2">
		Whole State
	</button>
</a>
<a href="?show=mine">
	<button class="{{ (!isset($_GET['show']) || !$_GET['show'] || $_GET['show'] == 'mine') ? 'bg-blue text-white' : 'bg-grey-lighter text-grey-darker' }} px-4 py-1 pb-2">
		My Voters
	</button>
</a>


<div class="flex mt-2">

	<div class="w-1/3 mr-2">

		<div class="py-1 border-b-4 text-lg text-blue">
			Cities <span class="text-grey-dark float-right">({{ $cities->count() }})</span>
		</div>

		@foreach($cities as $city)

			<div class="flex border-b text-sm cursor-pointer {{ (!$city->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					<!-- <a href="/{{ Auth::user()->team->app_type }}/jurisdictions/{{ $city->state }}/city/{{ $city->code }}"> -->
						{{ $city->name }}
					<!-- </a> -->
				</div>

				<div class="p-1 w-24 text-right">
					{{ number_format($city->the_count) }}
				</div>

			</div>

		@endforeach

	</div>

	<div class="w-1/3 mx-2">

		<div class="py-1 border-b-4 text-lg text-blue">
			Counties <span class="text-grey-dark float-right">({{ $counties->count() }})</span>
		</div>

		@foreach($counties as $county)

			<div class="flex border-b text-sm cursor-pointer {{ (!$county->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					{{ $county->name }}
				</div>

				<div class="p-1 w-24 text-right">
					{{ number_format($county->the_count) }}
				</div>

			</div>

		@endforeach

	</div>

	<div class="w-1/3 ml-2">

		<div class="py-1 border-b-4 text-lg text-blue">
			Congressional
			<span class="text-grey-dark float-right">({{ $congress->count() }})</span>
		</div>

		@foreach($congress as $district)

			<div class="flex border-b text-sm cursor-pointer {{ (!$district->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					{{ $district->name }}
				</div>

				<div class="p-1 w-24 text-right">
					{{ number_format($district->the_count) }}
				</div>

			</div>

		@endforeach


		<div class="py-1 border-b-4 text-lg text-blue mt-2">
			State Senate
			<span class="text-grey-dark float-right">({{ $senate->count() }})</span>
		</div>

		@foreach($senate as $district)

			<div class="flex border-b text-sm cursor-pointer {{ (!$district->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					{{ $district->name }}
				</div>

				<div class="p-1 w-24 text-right">
					{{ number_format($district->the_count) }}
				</div>

			</div>

		@endforeach


		<div class="py-1 border-b-4 text-lg text-blue mt-2">
			State House
			<span class="text-grey-dark float-right">({{ $house->count() }})</span>
		</div>

		@foreach($house as $district)

			<div class="flex border-b text-sm cursor-pointer {{ (!$district->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					{{ $district->name }}
				</div>

				<div class="p-1 w-24 text-right">
					{{ number_format($district->the_count) }}
				</div>

			</div>

		@endforeach


	</div>


</div>

@endsection

@section('javascript')

	@livewireScripts

@endsection
