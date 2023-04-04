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
		@if($actions->count() > 0)
			({{ $actions->count() }})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full mb-12">
		<div class="w-2/3">

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">Campaign Actions</span> show recent updates in support, contributions, volunteers and more.
			</div>
		</div>
	</div>


	<div class="flex">
		<div class="w-1/2">
			<a class="bg-blue text-white hover:text-white hover:bg-blue-dark px-3 py-2 rounded-full" href="/campaign/actions/regulars">
				See Regulars
			</a>
		</div>
		<div class="w-1/2">
			<div class="text-right">
				<div class="text-sm">
					<a href="/campaign/actions/export">
				    	<button type="button" class="rounded-lg py-2 px-4 text-blue text-center ml-2 bg-grey-lighter font-normal"/>
				    		<i class="fas fa-download mr-2"></i> Export as CSV
				    	</button>
				    </a>
			 	</div>
		 	</div>
		</div>
	</div>



	@if($actions)
		<div class="mt-2 w-full text-sm text-grey-darker full table">

			<div class="table-row uppercase bg-grey-lighter">
				<div class="p-2 table-cell border-b border-r w-8">
					#
				</div>
				<div class="p-2 table-cell border-b border-r w-1/6">
					Who
				</div>
				<div class="p-2 table-cell border-b border-r">
					Current Support
				</div>				
				<div class="p-2 table-cell border-b border-r w-12">
					What
				</div>
				<div class="p-2 table-cell border-b border-r w-1/3">
					Details
				</div>
				<div class="p-2 table-cell border-b w-1/6">
					By
				</div>
				<div class="p-2 table-cell border-b w-1/6 text-right">
					When <i class="fas fa-arrow-down"></i>
				</div>
			</div>

			@foreach($actions as $action)

				<div class="table-row">
					<div class="p-2 table-cell border-b border-r text-grey text-xs">
						{{ $loop->iteration }}.
					</div>
					
					
					<div class="p-2 table-cell border-b border-r">
						@if($action->participant)
							<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $action->participant->id }}/edit">
								{{ $action->participant->full_name }}
							</a>
						@endif
					</div>

					<div class="table-cell border-b border-r text-center">

						<div class="flex items-center">

							@if($action->participant && $action->participant->support)
							
								<div class="{{ getSupportClass($action->participant->support) }} text-white rounded-full mx-auto rounded-full text-center cursor-pointer font-bold px-2 py-1  mt-2">

									<div class="pt-1 -mt-1 font-normal h-6 w-3">
									 {{ $action->participant->support }}
									</div>

								</div>

							@endif

						</div>

					</div>

					<div class="p-2 table-cell border-b border-r">
						<b>{{ $action->name }}</b>
					</div>
					
					<div class="p-2 table-cell border-b border-r">
						{{ $action->details }}
					</div>
					<div class="p-2 table-cell border-b text-xs uppercase text-grey-dark">
						{{ $action->added_by }}
					</div>
					<div class="p-2 table-cell border-b border-r text-xs text-grey-dark text-right">
						@if ($action->created_at)
						{{ $action->created_at->format('D n/j/Y g:ia') }}
						@endif
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