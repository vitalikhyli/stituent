@extends('business.base')

@section('title')
    @lang('Prospects')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    Patterns
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	Patterns




	<div class="float-right font-normal">
		<form action="/{{ Auth::user()->team->app_type }}/patterns/" method="post">
			@csrf
			<input type="text" name="type" class="hidden" value="">
			<input type="text" name="name" class="border px-2 py-1 text-sm" placeholder="New Name">
			<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
				New Pattern
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
			<span class="font-bold text-black">Patterns</span> are the steps we take to approach and follow up with each prospect
		</div>
	</div>
</div>



<div class="flex-1 pb-8 text-grey-dark">		

@if($patterns->first())
	<div class="table">

		<div class="table-row text-xs w-full">

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-8">
				
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-24">
				
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				Pattern
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				User
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				Steps
			</div>



		</div>


		@foreach($patterns as $pattern)
			<div class="table-row text-sm w-full mb-1">

				<div class="table-cell p-1 align-middle border-b">
					{{ $loop->iteration }}.
				</div>

				<div class="table-cell p-1 align-middle border-b ">
					<a href="/{{ Auth::user()->team->app_type }}/patterns/{{ $pattern->id }}/edit">
						<button class="rounded-lg bg-blue text-xs uppercase text-white px-3 py-1">
							Edit
						</button>
					</a>
				</div>

				<div class="table-cell p-1 align-middle border-b w-1/4 truncate text-lg font-bold">
					{{ $pattern->name }}
				</div>

				<div class="table-cell p-1 align-middle border-b text-blue">
					{{ $pattern->user->name }}
				</div>

				<div class="table-cell p-1 align-middle border-b w-1/4 truncate">

					@foreach($pattern->steps->sortBy('the_order') as $step)

						<div class="{{ (!$loop->last) ? 'border-b' : '' }} p-2">

							<span class="border p-1 px-2 rounded-full bg-green text-white text-sm text-center mr-2">{{ $step->the_order }}</span>

							{{ $step->name }}

						</div>

					@endforeach

				</div>



			</div>
		@endforeach

	</div>
@endif


</div>






@endsection

@section('javascript')
	

@endsection
