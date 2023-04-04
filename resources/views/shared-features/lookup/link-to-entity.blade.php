@if ($people->first())

	@foreach ($people as $person)

		<div data-person_id="{{ $person->id }}" 
			 data-person_name="{{ $person->full_name }}" 
			 class="link_person_id cursor-pointer w-full pl-3 block text-grey-darker p-1 hover:bg-blue hover:text-white flex" style="transition:0s;">

			<div class="w-2/3 flex">

				@if($person->person)
					<div class="w-4 mr-2">
						<i class="fa fa-user-circle text-blue"></i>
					</div>
				@else
					<div class="w-4 mr-2"></div>
				@endif

				{{ $person->name }}

			</div>

			<div class="w-1/6 text-sm">

				{{ $person->address_city }}

			</div>

			<div class="w-1/6 text-sm text-right">

				{{ $person->email }}
				
			</div>
			
		</div>

	@endforeach

	@foreach ($emails as $person)
		

	@endforeach

@endif

@if ($groups->first())


	@foreach ($groups as $group)

	@endforeach

@endif


@if ($cases->first())

	@foreach ($cases as $case)

	@endforeach

@endif

