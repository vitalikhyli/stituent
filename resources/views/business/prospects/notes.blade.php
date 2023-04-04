<div class="border-t-2 mb-8">

	@foreach($opportunity->entity->contacts->sortByDesc('date') as $contact)

		<div class="border-b text-sm py-1">

			<div class="px-2 mb-1 text-grey-dark">
				{{ Carbon\Carbon::parse($contact->date)->diffForHumans() }}
			</div>

			@if($contact->salescontact->amount_secured)
				<div class="p-2 text-3xl font-bold text-white bg-blue text-center rounded mb-2 shadow">
					${{ number_format($contact->salescontact->amount_secured) }}
				</div>
			@endif

			@if($contact->salescontact->step)
				<div class="px-2 text-blue font-bold">
					Step: {{ $contact->salescontact->step }}
				</div>
			@endif

			@if($contact->salescontact->check_in)
				<div class="px-2 text-green font-bold">
					{{$contact->salescontact->user->name }} Checked In
				</div>
			@endif

			<div class="px-2">
				{{ $contact->notes }}
			</div>

		</div>

	@endforeach

</div>