@if ($people->first())

	<div class="uppercase border-b text-left bg-grey-darkest text-grey-lightest p-2">
		People
	</div>

	@foreach ($people as $person)

		
		<a class="w-full pl-3 block text-grey-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($person->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $person->name }}
				</div>
				<div class="w-2/3 text-sm text-right">

					{{ $person->full_address }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif

@if ($emails->first())

	<div class="uppercase border-b text-left bg-grey-darkest text-grey-lightest p-2">
		Emails
	</div>

	@foreach ($emails as $person)

		
		<a class="w-full pl-3 block text-grey-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($person->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $person->name }}
				</div>
				<div class="w-2/3 text-sm text-right">

					{{ $person->emailDisplay }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif

@if($phones->first())

	<div class="uppercase border-b text-left bg-grey-darkest text-grey-lightest p-2">
		Phones
	</div>

	@foreach ($phones as $person)

		
		<a class="w-full pl-3 block text-grey-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
			<div class="flex">
				<div class="w-1/3">
					@if ($person->person)
						<i class="fa fa-user-circle"></i>
					@endif

					{{ $person->name }}
				</div>
				<div class="w-1/3 text-sm">
					{{ $person->address_city }}
				</div>
				<div class="w-1/3 text-sm text-right truncate">

					{{ $person->phoneDisplay }}
					
				</div>
				
			</div>
		</a>


	@endforeach

@endif


@if ($groups->first())

	<div class="uppercase border-b text-left bg-grey-darkest text-grey-lightest p-2">
		Groups
	</div>

	@foreach ($groups as $group)

		<a class="w-full pl-3 block text-grey-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}">
			{{ $group->name }}
		</a>

	@endforeach

@endif

@if ($cases->first())

	<div class="uppercase border-b text-left bg-grey-darkest text-grey-lightest p-2">
		Cases
	</div>

	@foreach ($cases as $case)

		
		<a class="w-full pl-3 block text-grey-dark hover:text-black p-1 hover:bg-grey-lightest" href="/{{ Auth::user()->team->app_type }}/groups/{{ $case->id }}">
			{{ $case->subject }}
		</a>


	@endforeach
@endif

