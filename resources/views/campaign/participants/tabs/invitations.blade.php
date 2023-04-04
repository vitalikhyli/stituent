<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
	Invitations
	
</div>

@if(!$participant->invites->first())

	<div class="text-grey-dark text-sm italic pl-2">None</div>

@else

	<div class="pl-2">


		<div class="w-full">

			@foreach($participant->invites->sortByDesc('date') as $invite)

				<div class="flex text-sm {{ (!$loop->last) ? 'border-b' : '' }}">
					<div class="flex-shrink w-24 truncate p-2">
						{{ $invite->event->date }}
					</div>
					<div class="flex-1 truncate p-2">
						{{ $invite->event->name }}
					</div>
					<div class="flex-1 truncate p-2 text-right">
						@if($invite->can_attend)
							@if($invite->guests)
								{{ $invite->guests }} guests
							@endif
						@endif
					</div>
				</div>

			@endforeach

		</div>
	</div>
	
@endif