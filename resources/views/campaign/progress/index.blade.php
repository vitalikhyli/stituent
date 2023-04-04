@extends('campaign.base')

@section('title')
    Campaign Actions
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Actions</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Campaign Actions
		@if($items_count > 0)
			({{$items_count}})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">Campaign Progress</span> shows recent progress in support, contributions, volunteers and more.
			</div>
		</div>
	</div>


	<div class="text-right">
		<div class="text-sm">
	    	<button type="button" class="rounded-lg py-2 px-4 text-blue text-center ml-2 bg-grey-lighter font-normal"/>
	    		<i class="fas fa-download mr-2"></i> Export as CSV
	    	</button>
	 	</div>
 	</div>



	@if($items)
		<div class="mt-2 w-full text-sm text-grey-darker full table">

			<div class="table-row uppercase bg-grey-lighter">
				<div class="p-2 table-cell border-b border-r w-8">
					#
				</div>
				<div class="p-2 table-cell border-b border-r w-1/6">
					When
				</div>
				<div class="p-2 table-cell border-b border-r w-12">
					Type
				</div>
				<div class="p-2 table-cell border-b border-r">
					Who
				</div>
				<div class="p-2 table-cell border-b w-1/3">
					What
				</div>
				<div class="p-2 table-cell border-b w-1/6">
					Running
				</div>
			</div>

			@foreach($items as $item)

				<div class="table-row">
					<div class="p-2 table-cell border-b border-r text-grey text-xs">
						{{ $loop->iteration }}.
					</div>
					<div class="p-2 table-cell border-b border-r">
						{{ $item->the_date }}
					</div>
					<div class="p-2 table-cell border-b border-r">
						{{ $item->type }}
					</div>
					<div class="p-2 table-cell border-b border-r">
						@if($item->participant)
							<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $item->participant->id }}/edit">
								{{ $item->participant->full_name }}
							</a>
						@endif
					</div>
					<div class="p-2 table-cell border-b border-r">
						{{ $item->qual }}
					</div>
					<div class="p-2 table-cell border-b">
						{{ $item->qual2 }}
					</div>
				</div>

			@endforeach
		</div>
	@endif

@endsection


@section('javascript')

	<script type="text/javascript">
		
		$(document).ready(function() {


		});

	</script>

@endsection