@extends('office.base')

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>@lang('Constituents')</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue flex">

	<div class="text-2xl font-sans flex-shrink">
		 @lang('Constituents')

	</div>

	<div class="flex-grow text-right text-sm">

		<a class="mr-4" href="/{{ Auth::user()->team->app_type }}/constituents/regulars">
			Regulars <span class="bg-red text-xs uppercase text-white p-1">New</span>
		</a>

		@if(Auth::user()->permissions->createconstituents)
			<a href="/{{ Auth::user()->team->app_type }}/constituents/new">
				<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white">
					Add a New @lang('Constituent')
				</button>
			</a>
		@endif

		

	</div>

</div>

<div>

	@livewire('constituents.query-form')

</div>

@endsection

@section('javascript')

	@livewireScripts

@endsection
