@extends('business.base')

@section('title')
    @lang('Prospects')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    <a href="/business/prospects">Prospects</a>
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	{{ $opportunity->entity->name }}


	<div class="float-right font-normal">
		<a href="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}/edit">
			<button class="rounded-lg bg-blue text-xs uppercase text-white px-3 py-2">
				Edit
			</button>
		</a>
	</div>

	<div class="float-right font-normal">
		<a href="/{{ Auth::user()->team->app_type }}/prospects?type={{ $opportunity->type }}">
			<button class="rounded-lg bg-grey-dark text-white text-xs uppercase px-3 py-2 mr-2">
				All {{ $opportunity->type }} Prospects
			</button>
		</a>
	</div>

</div>


<div class="w-full flex">

	<div class="w-1/2 mr-2">

		<div class="border-b-4 border-grey font-bold pb-1 mt-4 mb-2">
			Basic
		</div>

		<div class="flex mb-2">
			<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
				Type
			</div>
			<div class="text-blue">
				{{ $opportunity->type }}
			</div>
		</div>

		<div class="flex mb-2">
			<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
				Website
			</div>
			<div class="">
				<a href="{{ $opportunity->entity->urlWithHttp }}" target="new" class="text-black">{{ $opportunity->entity->url }}</a>
			</div>
		</div>


		<div class="mb-2 py-2 flex">
			<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
				Address
			</div>
			<div>

				@if($opportunity->entity->department )
					<div class="font-bold">
						{{ $opportunity->entity->department }}
					</div>
				@endif

				<div>
					{{ $opportunity->entity->address_number }}
					{{ $opportunity->entity->address_street }}
					{{ $opportunity->entity->address_apt }}
				</div>
				<div>
					{{ $opportunity->entity->address_city }}
					{{ $opportunity->entity->address_state }}
					{{ $opportunity->entity->address_zip }}
				</div>
			</div>
		</div>

		<div class="flex mb-2">
			<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
				Status
			</div>
			<div class="">
				@if($opportunity->client)
					<span class="text-blue">
						<i class="fas fa-check-circle" class="mr-4 w-4"></i> Current client
					</span>
				@else
					<span class="text-red"><i class="fas fa-times" class="mr-4 w-4"></i></span> Not a client ...yet!
	            @endif
			</div>
		</div>

		@if($opportunity->cf_id)

			<div class="flex mb-2">
				<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
					CF Account ID
				</div>
				<div class="text-sm">
					{{ $opportunity->cf_id }}
				</div>
			</div>

		@endif


		@if($opportunity->client)

			<div class="flex mb-2">
				<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
					Last Check In
				</div>
				<div class="text-sm">
					@if($opportunity->lastCheckIn)
						{{ Carbon\Carbon::parse($opportunity->lastCheckIn)->toDateString() }}
					@else
						Never
					@endif
				</div>
			</div>

			<div class="flex mb-2">
				<div class="text-grey-dark pr-4 w-32 text-xs uppercase">
					Future
				</div>
				<div class="text-sm">
					Check in every <span class="font-bold">{{ ($opportunity->days_check_in) ? $opportunity->days_check_in : '____' }}</span> days, next on <span class="font-bold">{{ ($opportunity->next_check_in) ? $opportunity->next_check_in : '____' }}</span> 
				</div>
			</div>

		@endif

		<div class="border-b-4 border-grey font-bold pb-1 mb-2 mt-4">
			People
		</div>

		@foreach($opportunity->entity->people->sortBy('last_name') as $person)

			<div class="mb-3 flex border-b pb-2">

				<i class="fas fa-user float-left text-lg mx-4 mt-1 text-blue"></i>

				<div class="w-full">

					<div class="float-right text-sm">
						<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">
							<button class="rounded-lg bg-grey-lighter text-xs text-blue px-2 py-1 mr-1 mt-1">
								Edit
							</button>
						</a>
					</div>

					@if(isset($person->full_name))
						<div class="font-bold">{{ $person->full_name }}</div>
					@endif
					@if(isset($person->pivot->relationship))
						<div class="text-grey-dark text-sm">{{ $person->pivot->relationship }}</div>
					@endif
					@if(isset($person->primary_email))
						<div class="text-sm text-grey-darker">
							&lt;{{ $person->primary_email }}&gt;
						</div>
					@endif
					@if(isset($person->primary_phone))
						<div class="text-sm text-grey-darker">
							{{ $person->primary_phone }}
						</div>
					@endif


				</div>

			</div>

    	@endforeach


		<div class="mb-3 flex border-b pb-2">

			<i class="fas fa-plus float-left text-lg mx-4 mt-1 text-blue"></i>

			<div class="w-full text-sm">

				<form action="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}/add_person" method="post">

					@csrf

					<div>
						<input type="text" name="person_new_name" placeholder="New Name" class="border p-2 rounded-lg w-full font-bold" />
					</div>
					<div class="mt-1">
						<input type="text" name="person_new_title" placeholder="New Title" class="border p-2 rounded-lg w-full" />
					</div>

					<div class="mt-1 flex">
						<input type="text" name="person_new_email" placeholder="New Email" class="border p-2 rounded-lg flex-grow mr-1" />
						<input type="text" name="person_new_phone" placeholder="New Phone" class="border p-2 rounded-lg flex-grow ml-1" />
						<div class="p-2">
							<button class="rounded-lg bg-blue text-sm text-white px-2 py-1">
								Add
							</button>
						</div>
					</div>


				</form>

			</div>
		</div>

	</div>


	<div class="w-1/2">

		@if($opportunity->pattern)
			<div class="border-b-4 border-grey font-bold pb-1 mt-4">
				Pattern: <span class="text-grey-darker">{{ $opportunity->pattern->name }}</span>
			</div>
		@endif

		@if($opportunity->pattern)

			@foreach($opportunity->pattern->steps->sortBy('the_order') as $step)

				<div class="{{ (!$loop->last) ? 'border-b' : '' }} p-2">

					<span class="p-1 px-2 rounded-full {{ ($step->fulfilled($opportunity)) ? 'bg-blue' : 'bg-green' }} text-white text-sm text-center mr-2">{{ $step->the_order }}</span>

					{{ $step->name }}

				</div>

			@endforeach

		@endif

		<div class="border-b-4 border-grey font-bold pb-1 mt-4">
			Notes
		</div>

		@include('business.prospects.notes-form')
		
		@include('business.prospects.notes')
		
	</div>

	

@endsection

@section('javascript')
	

@endsection
