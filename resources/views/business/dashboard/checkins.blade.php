

<div class="mt-6 text-xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	Check-Ins

	<div class="text-grey-dark text-xs italic font-normal">Keep in touch with clients and check in on their needs</div>
</div>


@if(!$checkins->first())

	<div class="py-2 text-grey-dark">
		None
	</div>

@else

	<div class="table">

		<div class="table-row text-sm w-full">

			<div class="table-cell p-2 bg-grey-lighter uppercase border-b">
				Entity
			</div>

			<div class="table-cell py-2 bg-grey-lighter uppercase border-b">
				Last
			</div>

			<div class="table-cell py-2 bg-grey-lighter uppercase border-b font-bold">
				Scheduled
			</div>

		</div>

		@foreach($checkins as $checkin)

			<div class="table-row text-sm w-full mb-1">

				<div class="table-cell p-2 align-middle border-b w-1/2 truncate">
					<a href="/{{ Auth::user()->team->app_type }}/prospects/{{ $checkin->id }}">
						{{ mb_strimwidth($checkin->entity->name, 0, 20, "...") }}
					</a>
				</div>

				<div class="table-cell p-2 align-middle border-b w-1/4">
					@if($checkin->lastCheckIn)
						{{ Carbon\Carbon::parse($checkin->lastCheckIn)->diffForHumans() }}
					@endif
				</div>

				<div class="table-cell p-2 align-middle border-b w-1/4">
					{{ $checkin->next_check_in }}
				</div>

			</div>	

		@endforeach

	</div>

@endif