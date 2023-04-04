@extends('campaign.base')

@section('title')
    Opportunities
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > 
    <a href="/campaign/volunteers/manager">Volunteer Opportunities</a> > 
    <b>Opportunities</b>
@endsection

@push('styles')
	@livewireStyles
@endpush

@section('main')

	<div class="text-2xl font-bold border-b-4 pb-2">
		Opportunities
	</div>

	<div class="">

		@livewire('campaign.opportunities.opportunities')

	</div>

@endsection

@push('scripts')

	@livewireScripts

@endpush

