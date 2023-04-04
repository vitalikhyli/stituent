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

	<div class="mb-8">

		@livewire('campaign.opportunities.phonebank', ['opp' => $opp])
		
	</div>


@endsection

@push('scripts')

	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {

			$("#filter_lists").focus();

		});

	</script>

@endpush

