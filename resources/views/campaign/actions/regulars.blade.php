@extends('campaign.base')

@section('title')
    Campaign Regulars
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > &nbsp;<a href="/campaign/actions">Campaign Actions</a>
    > &nbsp;<b>Regulars</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Campaign Regulars
		@if($grouped_sorted->count() > 0)
			({{ $grouped_sorted->count() }})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full mb-12">
		<div class="w-2/3">

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">Campaign Regulars</span> are the constituents that have had the most activity of any kind with the campaign.
			</div>
		</div>
	</div>


	<div class="flex">
		<div class="w-1/2">
			<a class="bg-blue text-white hover:text-white hover:bg-blue-dark px-3 py-2 rounded-full" href="/campaign/actions">
				See All
			</a>
		</div>
		<div class="w-1/2">
			<div class="text-right">
				<div class="text-sm">
			    	<!-- <button type="button" class="rounded-lg py-2 px-4 text-blue text-center ml-2 bg-grey-lighter font-normal"/>
			    		<i class="fas fa-download mr-2"></i> Export as CSV
			    	</button> -->
			 	</div>
		 	</div>
		</div>
	</div>



	@if($grouped_sorted)
		<div class="mt-2 w-full text-sm text-grey-darker full table">

			<div class="table-row uppercase bg-grey-lighter">
				<div class="p-2 table-cell border-b border-r w-8">
					
				</div>
				<div class="p-2 table-cell border-b border-r w-1/6">
					Who
				</div>
				<div class="p-2 table-cell border-b border-r w-12">
					#
				</div>
				<div class="p-2 table-cell border-b border-r w-12">
					Actions
				</div>
				<div class="p-2 table-cell border-b w-1/6">
					Last By
				</div>
				<div class="p-2 table-cell border-b w-1/6 text-right">
					When
				</div>
			</div>

			@foreach($grouped_sorted as $pid => $actions)

				<div class="table-row">
					<div class="p-2 table-cell border-b border-r text-grey text-xs">
						{{ $loop->iteration }}.
					</div>
					
					
					<div class="p-2 table-cell border-b border-r">
						@if($actions->last()->participant)
							<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $actions->last()->participant->id }}/edit">
								{{ $actions->last()->participant->full_name }}
							</a>
						@endif
					</div>
					<div class="p-2 table-cell border-b border-r w-12">
						@if ($actions->count() > 3)
							<b class="text-lg">{{ $actions->count() }}</b>
						@elseif ($actions->count() > 1)
							<b>{{ $actions->count() }}</b>
						@else
							{{ $actions->count() }}
						@endif
					</div>
					<div class="p-2 table-cell border-b border-r w-1/2">
						<b>{{ $actions->implode('name', ', ') }}</b>
					</div>
					
					<div class="p-2 table-cell border-b text-xs uppercase text-grey-dark">
						{{ $actions->last()->added_by }}
					</div>
					<div class="p-2 table-cell border-b border-r text-xs text-grey-dark text-right">
						{{ $actions->last()->created_at->format('D n/j/Y g:ia') }}
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