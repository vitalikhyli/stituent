@extends('campaign.base')

@section('title')
    Volunteer Opportunities
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Volunteers</b>
@endsection

@push('styles')
	@livewireStyles
@endpush

@section('main')

	<div class="">

		@livewire('campaign.volunteers.volunteers')

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

