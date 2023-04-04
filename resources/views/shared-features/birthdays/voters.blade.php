@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Birthdays')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
    <!-- <a href="/{{ Auth::user()->team->app_type }}/constituents">Constituents</a> > -->
	&nbsp; <b>@lang('Birthdays')</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

	@livewire('birthdays')

@endsection

@push('scripts')
	@livewireScripts
@endpush


