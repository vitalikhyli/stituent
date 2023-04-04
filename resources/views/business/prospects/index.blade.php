@extends('business.base')

@section('title')
    @lang('Prospects')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    Prospects
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	


		@if(isset($_GET['type']))

			{{ $_GET['type'] }} Prospects

				<a href="/{{ Auth::user()->team->app_type }}/prospects">
					<button class="font-normal ml-2 rounded-lg bg-grey-dark text-white text-xs uppercase px-3 py-2 mr-2">
						Show All Prospect Types
					</button>
				</a>

		@else

			Prospects

		@endif

	<div class="float-right font-normal">
		<form action="/{{ Auth::user()->team->app_type }}/prospects/" method="post">
			@csrf
			<input type="text" name="type" class="hidden" value="">
			<input type="text" name="name" class="border px-2 py-1 text-sm" placeholder="New Name">
			<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
				New Prospect
			</button>
		</form>
	</div>



</div>

<div class="flex text-grey-darker mb-8">
	<div class="w-2/3">
		<div class="font-normal mr-2 pt-2">
			<span class="font-bold text-blue text-sm">Search</span>
			<input type="text" name="search" id="seach" class="border px-2 py-1 text-sm" placeholder="Search">
		</div>
	</div>
	<div class="w-1/3 border-l-2 pl-2 italic text-sm text-darkest">
		<div class="p-2">
			<span class="font-bold text-black">Prospects</span> are an extension of Entities. We use these to keep track of who we are approaching for business.
		</div>
	</div>
</div>



<div class="flex-1 pb-8 text-grey-dark">

	@foreach($prospect_types as $type)
		<div class="mt-4 text-xl font-sans text-black border-b-4 pb-1">
			@if(!$type)
				(No Type)
			@else
				

				{{ $type }}

				@if(!isset($_GET['type']))
					<a href="/{{ Auth::user()->team->app_type }}/prospects?type={{ $type }}">
						<button class="font-normal 	rounded-lg bg-grey-dark text-white text-xs uppercase px-2 py-1 text-xs">
							Show
						</button>
					</a>
				@endif

			@endif

			<div class="float-right font-normal">
				<form action="/{{ Auth::user()->team->app_type }}/prospects/" method="post">
					@csrf
					<input type="text" name="type" class="hidden" value="{{ $type }}">
					<input type="text" name="name" class="border px-2 py-1 text-sm" placeholder="New Name">
					<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
						New Prospect
					</button>
				</form>
			</div>

		</div>

		<div class="table">

			<div class="table-row text-xs w-full">


				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-12">
					
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
					Prospect
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-6">
					State
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
					City
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
					Pattern
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-24">
					Highest Step
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-12">
					Complete
				</div>


				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-24">
					User
				</div>

			</div>


			@foreach($prospects->where('type', $type) as $opportunity)
				<div class="table-row text-sm w-full mb-1">

					<div class="table-cell p-1 align-middle border-b">
						{{ $loop->iteration }}.
					</div>

					<div class="table-cell p-1 align-middle border-b w-1/4 truncate">
						<a href="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}">
							{{ $opportunity->entity->name }}
						</a>
					</div>

					<div class="table-cell p-1 align-middle border-b truncate w-6">
						{{ $opportunity->entity->address_state }}
					</div>

					<div class="table-cell p-1 align-middle border-b w-1/4 truncate">
						{{ $opportunity->entity->address_city }}
					</div>

					<div class="table-cell p-1 align-middle border-b truncate">
						@if($opportunity->pattern)
							{{ $opportunity->pattern->name }}
						@endif
					</div>


					<div class="table-cell p-1 align-middle border-b runcate">
						@if($opportunity->pattern)
							{{ $opportunity->highestStep }}
						@endif
					</div>

					<div class="table-cell p-1 align-middle border-b runcate">
						@if($opportunity->pattern)
							{{ $opportunity->progressPercentage }}%
						@endif
					</div>


					<div class="table-cell p-1 align-middle border-b text-xs text-blue">
						{{ $opportunity->user->name }}
					</div>

				</div>
			@endforeach

		</div>

	@endforeach


</div>






@endsection

@section('javascript')
	

@endsection
