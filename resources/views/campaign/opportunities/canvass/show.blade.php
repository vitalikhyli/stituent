@extends('campaign.base')

@section('title')
    Opportunities
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > 
    <a href="/campaign/volunteers/manager">Volunteer Opportunities</a> > 
    <a href="/campaign/opportunities">Opportunities</a> > 
    <b>{{ $opp->name }}</b>
@endsection

@push('styles')
	@livewireStyles
@endpush

@section('main')

	<div class="text-2xl font-bold border-b-4 pb-2">
		<span class="text-blue capitalize">
			{{ $opp->type }} /
		</span>
		{{ $opp->name }}
	</div>

	<div class="">

		@livewire('campaign.opportunities.canvass', ['opp' => $opp])
		
	</div>


@endsection

@push('scripts')

	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {

			$("#search").focus();

		});

	</script>

@endpush

