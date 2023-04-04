@if ($participants->first())

	<div class="uppercase border-b text-left bg-black rounded-t-lg text-grey-lightest px-2 py-1 mt-2">
		Participants
	</div>

	@foreach ($participants as $participant)

		
		<a class="w-full block text-blue-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/participants/{{ $participant->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($participant->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $participant->name }}
				</div>
				<div class="w-2/3 text-sm text-right">

					{{ $participant->full_address }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif

@if ($emails->first())

	<div class="uppercase border-b text-left bg-black rounded-t-lg text-grey-lightest px-2 py-1 mt-2">
		Emails
	</div>

	@foreach ($emails as $participant)

		
		<a class="w-full block text-blue-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/participants/{{ $participant->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($participant->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $participant->name }}
				</div>
				<div class="w-2/3 text-sm text-right">

					{{ $participant->emailDisplay }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif

@if($phones->first())

	<div class="uppercase border-b text-left bg-black rounded-t-lg text-grey-lightest px-2 py-1 mt-2">
		Phones
	</div>

	@foreach ($phones as $participant)

		
		<a class="w-full block text-blue-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/participants/{{ $participant->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($participant->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $participant->name }}
				</div>
				<div class="w-1/3 text-sm">
					{{ $participant->address_city }}
				</div>
				<div class="w-1/3 text-sm text-right truncate">

					{{ $participant->phoneDisplay }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif

