@extends('campaign.base')

@section('title')
    {{ $event->name }}
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > &nbsp;<a href="/campaign/events">Campaign Events</a>
    > &nbsp;<b>{{ $event->name }}</b>
@endsection

@push('styles')
	@livewireStyles
@endpush
@push('scripts')
	@livewireScripts
@endpush

@section('main')

<div class="text-3xl font-bold border-b-4 pb-2">
	<div class="float-right text-sm text-gray-500 hover:text-blue-600 transition m-2 uppercase">
		<a href="/campaign/events/{{ $event->id }}/edit">
			Edit
		</a>
	</div>
	{{ $event->name }}
</div>


<div class="w-full mt-8 flex">

	


	<div class="w-2/3">

		<div class="p-1">
		
			<div class="flex -my-1">
				<div class="w-1/6 uppercase text-right p-2 pt-4 text-gray-500">
					What:
				</div>

				<div class="pt-4 pl-4 font-bold">
					{{ $event->name }}
				</div>

			</div>

		</div>

		<div class="p-1">
		
			<div class="flex -my-1">
				<div class="w-1/6 uppercase text-right p-2 pt-4 text-gray-500">
					When:
				</div>
				<div class="pt-4 pl-4 font-bold">
					{{ ($event->time) ? \Carbon\Carbon::parse($event->time)->format('g:ia') : '' }}
					{{ \Carbon\Carbon::parse($event->date)->format('l, F jS, Y') }}
					
					<span class="font-normal">
						({{ \Carbon\Carbon::parse($event->date.' '.$event->time)->diffForHumans() }})
					</span>
				</div>

				
			</div>


		</div>


		<!-- Edit Section -->

		<div class="p-1">
		
			<div class="flex -my-1">
				<div class="w-1/6 uppercase text-right p-2 pt-4 text-gray-500">
					Where:
				</div>

				<div class="pt-4 pl-4 font-bold">
					{{ $event->venue_name }}
					<div class="font-normal">
						{{ $event->venue_street }}
					</div>
					<div class="font-normal">
						{{ $event->venue_city }}, {{ $event->venue_state }} {{ $event->venue_zip }}
					</div>
				</div>

			</div>

		</div>

		<div class="p-1">
		
			<div class="flex -my-1">
				<div class="w-1/6 uppercase text-right p-2 pt-4 text-gray-500">
					Notes:
				</div>

				<div class="pt-4 px-4 w-5/6">
					@livewire('campaign-event-notes', ['event' => $event])
				</div>

			</div>

		</div>

	</div>

	<div class="w-1/3">

		<div class="flex -my-1">
				<div class="pt-4 pl-4 w-full">
					<div class="flex w-full text-gray-500 uppercase font-normal border-b">
						<div class="w-1/2">
							Invited
						</div>
						<div class="w-1/2 text-right">
							Can Attend
						</div>
						
					</div>
					@foreach ($event->invitees as $invitee)
						<div class="flex w-full mt-2">
							<div class="w-5/6">
								<div class="font-normal">
									<span class="w-8 text-sm text-gray-400">{{ $loop->iteration }}.</span>
									<a href="/campaign/participants/{{ $invitee->id }}">
										{{ $invitee->name }}
										<div class="text-xs ml-4 text-gray-400">
											{{ $invitee->full_address }}
										</div>
										<div class="text-xs ml-4 text-gray-400">
											{{ $invitee->phone }}
										</div>
									</a>
								</div>
							</div>
							<div class="w-1/6">
								@if ($invitee->pivot->can_attend)
									<i class="fa fa-check-circle text-green-500"></i>
								@else
									<span class="text-gray-400 text-sm">
										?
									</span>
								@endif
							</div>
						</div>
					@endforeach

				
					<div class="mt-4">
						<a class="mt-2 text-white bg-blue-400 cursor-pointer px-4 py-2 rounded-full hover:text-white hover:bg-blue-500" href="/campaign/events/{{ $event->id }}/guests">
							Edit Guest List
						</a>
					</div>
					
				</div>

			</div>

	</div>

</div>

@endsection


@section('javascript')

@endsection