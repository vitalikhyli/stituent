@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>@lang('Link Voters')</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')


<div class="flex border-b-4 pb-2 border-blue flex">

	<div class="text-2xl font-sans flex-shrink">
		 @lang('Link Voters')

	</div>

</div>

<div class="p-8 text-grey-dark w-2/3">
	Do you have people you have added to your database that are not linked to a voter file? This page lets you review the people staff have added to the database and link them to the official MA voter file where possible. <br><br>
	Click on one of our suggestions or do a name search.
</div>

<div class="text-center">

	{{ $individuals->links() }}

</div>

<div>

	@foreach($individuals as $individual)

		@if(trim($individual->full_name) == null)

			<div class="border-b py-2 flex items-center">

				<div class="w-1/2">
					<div class="font-bold text-lg">
						<a href="/{{ Auth::user()->team->app_type }}/{{ (Auth::user()->team->app_type == 'office') ? 'constituents' : 'participants' }}/{{$individual->id}}" class=" text-grey">
							Name is blank <span class="text-blue text-base">/ click to edit</span>
							<span class="text-grey-dark">
								@if($individual->dob)
									Age {{ \Carbon\Carbon::parse($individual->dob)->age }}
								@endif
								{{ $individual->gender }}
							</span>
						</a>
					</div>
				</div>

			</div>
			
			@continue

		@endif

		<div class="border-b py-2 flex items-center">

			<div class="w-1/2">
				<div class="font-bold text-lg">
					<a href="/{{ Auth::user()->team->app_type }}/{{ (Auth::user()->team->app_type == 'office') ? 'constituents' : 'participants' }}/{{$individual->id}}">
						{{ $individual->full_name }}
						<span class="text-grey-dark">
							@if($individual->dob)
								Age {{ \Carbon\Carbon::parse($individual->dob)->age }}
							@endif
							{{ $individual->gender }}
						</span>
					</a>
				</div>
				<div class="flex">
					<div class="text-grey-dark">
						{{ $individual->address_number }} {{ $individual->address_street }}
					</div>

					@if($individual->address_city)
						<div class="text-grey-dark ml-2">
							{{ $individual->address_city }}, {{ $individual->address_state }}
						</div>
					@endif
				</div>

				

			</div>

			<div class="flex-grow">
				@livewire('voter-link.one-voter', ['model' => $individual])
			</div>

		</div>

	@endforeach

</div>


@endsection

@section('javascript')

	@livewireScripts

@endsection
