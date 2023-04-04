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
		 {{ $model->name }}
	</div>

</div>


<div class="flex mt-2">

	<div class="w-1/3 mr-2">

		<div class="py-1 border-b-4 text-lg text-blue">
			People <span class="text-grey-dark float-right">({{ $people->count() }})</span>
		</div>

		@foreach($people as $person)

			<div class="flex border-b text-sm cursor-pointer {{ (!$city->the_count) ? 'text-grey' : '' }}">

				<div class="p-1 pl-2 flex-grow truncate">
					<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
						{{ $person->full_name }}
					</a>
				</div>

				<div class="p-1 w-24 text-right">
					{{ $person->full_address }}
				</div>

			</div>

		@endforeach

	</div>



</div>

@endsection

@section('javascript')

	@livewireScripts

@endsection
