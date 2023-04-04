@extends('campaign.base')

@section('title')
    Campaign Volunteers
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Volunteers</b>
@endsection

@push('styles')
	@livewireStyles
@endpush

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Campaign Volunteers
		@if($participants_count > 0)
			({{$participants_count}})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Use <span class="font-bold text-black">Campaign Volunteers</span> to keep track of all the people making your campaign happen. Coordinate volunteers via group emails.
			</div>
		</div>
	</div>

	<div class="h-16"></div>

	@if(!$participants->first())

		<div class="text-grey-dark">
			No volunteers to display yet.
		</div>

	@else


	<div class="text-right">

		<div class="font-bold pt-3 italic text-sm text-blue">Click headers to filter by type, click again to remove</div>

 	</div>
 	
		@livewire('volunteers-live', ['participants' => $participants, 'volunteer_options' => $volunteer_options])
	
		
	@endif

@endsection

@push('scripts')

	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {

			$("#search").focus();

		});

	</script>

@endpush

