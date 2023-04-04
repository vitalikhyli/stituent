@extends('campaign.base')

@section('title')
    Campaign Lists
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Lists</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Campaign Lists
		<!-- <i class="fa fa-info-circle text-grey hover:text-blue transition cursor-pointer pl-2"></i> -->
		<span class="text-blue">*</span>
	</div>

	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">

			<div class="text-center m-8 pt-4">
				<a href="/campaign/lists/new" class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark hover:text-white">
					Create a New List
				</a>
			</div>

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Use <span class="font-bold text-black">Campaign Lists</span> to build customized queries of the voters in your district. Create unlimited custom lists, then mix and match them to target or export the exact voters you want to work with.
			</div>
		</div>
	</div>

	<table class="table text-grey-dark">

		<tr>
			<th></th>
			<th>Name</th>
			<!-- <th>Participants</th> -->
			<th>Voters</th>
			<th>Doors</th>
			<th>Created By</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>

		@foreach ($lists as $list)

			@php
				$start = microtime(-1);
			@endphp

			<tr>
				<td class="text-grey w-4">
					{{ $loop->iteration }}.
				</td>
				<td>
					<a href="/campaign/lists/{{ $list->id }}">
						{{ $list->name }}
					</a>
				</td>
				<!-- <td class="">
					number_format($list->voterParticipants()->count())
				</td> -->

				<td>
					{{ number_format($list->count()) }}
				</td>

				<td>
					{{ number_format($list->doorsCount()) }}
				</td>

				<td>
					<span class="text-grey text-sm">
						{{ $list->user->name }}, {{ $list->created_at->format('n/j/Y g:ia') }}
					</span>
				</td>
				<td class="text-right text-sm">
					
					<div class="truncate">
						<a href="/campaign/lists/{{ $list->id }}/assign" class="bg-blue-lightest rounded-lg px-2 py-1">
							Assign Volunteers

							@if($list->assignedUsers->first())
								({{ $list->assignedUsers->count() }})
							@endif

						</a>
					</div>

				</td>				
				<td class="text-right text-sm">
					<a href="/campaign/lists/{{ $list->id }}/edit">Edit</a>
				</td>
				<td class="text-right">
					@include('campaign.lists.export-button', ['list' => $list, 'title' => 'Export'])
				</td>
				<td class="text-xs">
					<div class="mt-1">
						{{ number_format(microtime(-1) - $start, 1) }}s
					</div>
				</td>
			</tr>

		@endforeach

	</table>


	@include('campaign.lists.export-modal')


@endsection


@section('javascript')
	@include('campaign.lists.export-modal-js')
@endsection