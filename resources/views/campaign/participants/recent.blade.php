@extends('campaign.base')

@section('title')
    Participants
@endsection



@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Participants</b>
@endsection

@section('main')


<div class="w-full">

	<div class="text-3xl font-bold border-b-4 pb-2 mb-3 flex">
		Participants


		<div class="flex-1 text-right font-normal flex-grow">
	
			<a href="/{{ Auth::user()->team->app_type }}/participants/new">
				<button class="rounded-lg bg-blue text-white p-2 text-sm uppercase px-4">
					<i class="fas fa-user mr-2"></i> New Participant
				</button>
			</a>

		</div>

	</div>

	<table class="w-full">
		<tr>
			<th class="font-bold p-1">#</th>
			<th class="font-bold p-1">Name</th>
			<th class="font-bold p-1">Actions</th>
			<th class="font-bold p-1 text-center">Support</th>
			<th class="font-bold p-1 text-center">Phone</th>
			<th class="font-bold p-1">Notes</th>
			<th class="font-bold p-1">Added On</th>
			<th class="font-bold p-1">By</th>

		</tr>
		@foreach ($participants as $iteration => $participant)
			
			<tr>
				<td class="border-t align-top p-1 text-grey">
					{{ $participants->count() - $iteration }}.
				</td>
				<td class="border-t align-top p-1 whitespace-no-wrap">
					<a href="/campaign/participants/{{ $participant->id }}">
						{{ $participant->name }}
					</a>
				</td>
				<td class="border-t align-top p-1">
					@foreach ($participant->actions as $action)
						<i title="{{ $action->name }}" class="fa fa-hand-paper text-grey hover:text-grey-dark cursor-pointer"></i>
					@endforeach
				</td>
				<td class="border-t align-top p-1 text-center ">{{ $participant->support }}</td>
				<td class="border-t align-top p-1 text-center whitespace-no-wrap">{{ $participant->phone }}</td>
				<td class="border-t align-top p-1">{{ $participant->notes }}</td>

				<td class="border-t align-top p-1 text-sm text-grey-dark border-l whitespace-no-wrap">{{ $participant->created_at->format('n/j/Y g:ia') }}</td>
				<td class="border-t align-top p-1 text-sm text-grey-dark whitespace-no-wrap">
					@if ($participant->user)
						{{ $participant->user->name }}
					@endif
				</td>
			</tr>

		@endforeach
	</table>


@endsection


@section('javascript')

	@livewireScripts

@endsection
