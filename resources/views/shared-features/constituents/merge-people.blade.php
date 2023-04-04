@extends(Auth::user()->team->app_type.'.base')


@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">@lang('Constituents')</a> >
	Merge People

@endsection


@section('title')
	Merge People
@endsection

@section('style')
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    
    @livewireStyles

@endsection

@section('main')


	<div class="text-2xl font-sans border-b-4 border-blue pb-3">
		Merge People
	</div>

	@livewire('merge-people')

@endsection

@section('javascript')

    @livewireScripts

@endsection