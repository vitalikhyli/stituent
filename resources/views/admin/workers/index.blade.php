@extends('admin.base')

@section('title')
    Admin Data Workers
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Workers', 'data_workers', 'level_1') !!}


@endsection

@section('style')

@endsection

@section('main')

<div class="text-xl mb-4 border-b bg-orange-lightest p-2 ">
    Data Workers
</div>

<table class="font-normal text-sm table">

	<tr>
		<th style="border-top: 0px;"></th>
		<th style="border-top: 0px;"></th>
		<th style="border-top: 0px;"></th>
		<th style="border-top: 0px;">Time</th>
		<th style="border-top: 0px;">Jobs</th>
		<th style="border-top: 0px;">Log</th>
		<th style="border-top: 0px;"></th>
		
	</tr>

	@foreach ($workers as $worker)

		

		@if ($worker->trashed()) 
			<tr class="text-grey">
				<td>{{ $worker->id }}</td>
				<td class="">
					@if(!$worker->interrupted)
						<i class="fas fa-check-circle text-green text-3xl -mt-1"></i>
					@endif
				</td>
				<td class="">
					@if($worker->interrupted)
						<i class="fas fa-times text-green text-3xl -mt-1"></i>
					@endif
				</td>
				<td class="align-top whitespace-no-wrap">
					{{ $worker->created_at->format('Y-m-d h:i:s') }} - <br />
					{{ $worker->updated_at->format('Y-m-d h:i:s') }}
				</td>

				<td class="align-top whitespace-no-wrap">
					@if($worker->jobs != null)
						{{ count($worker->jobs) }} Jobs
					@else
						No Jobs
					@endif
				</td>
				<td class="align-top">Log End: {{ $worker->last_log }}</td>
				<td>
					@if($worker->jobs != null)
					<a href="/admin/workers/{{ $worker->id }}" class="remote-modal float-right border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white m-1 px-2 py-1 text-sm"
						target="#worker-detail-modal">
								Details
					</a>
					@endif
				</td>
			</tr>
		@else
			<tr class="">
				<td>{{ $worker->id }}</td>
				<td class=""><i class="fas fa-cog fa-spin text-grey text-3xl -mt-1"></i></td>
				<td class=""></i></td>
				<td class="align-top whitespace-no-wrap">{{ $worker->created_at->format('Y-m-d g:ia') }}
					<div class="text-blue">Last Ping: {{ time() - $worker->ping }} secs</div>
				</td>
				<td class="align-top whitespace-no-wrap">
					@foreach ($worker->jobs as $job_id => $job_start)
						{{ $job_id }} - {{ $job_start }}<br>
					@endforeach
				</td>
				<td class="align-top">{!! nl2br($worker->log) !!}</td>
				<td>
					<a href="/admin/workers/{{ $worker->id }}" class="remote-modal group-hover:opacity-100 opacity-0 float-right border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white m-1 px-2 py-1 text-sm"
						target="#worker-detail-modal">
								Details
					</a>
				</td>
			</tr>
		@endif
			
		

	@endforeach    

</table>


<!-- Modal -->
<div id="worker-detail-modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		

	</div>
</div>

@endsection