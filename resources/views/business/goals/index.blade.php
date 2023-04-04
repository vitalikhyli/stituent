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
	{{ Auth::user()->name }}'s Goals



	<div class="float-right font-normal">
		<form action="/{{ Auth::user()->team->app_type }}/goals/" method="post">
			@csrf
			<input type="text" size="4" name="year" class="border px-2 py-1 text-sm" placeholder="Year">
			<input type="text" size="4" name="quarter" class="border px-2 py-1 text-sm" placeholder="Qtr">
			<span class="text-base">$</span>
			<input type="text" size="8" name="amount" class="border px-2 py-1 text-sm" placeholder="Amount">
			<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
				New Goal
			</button>
		</form>
	</div>



</div>

<div class="flex text-grey-darker mb-8">
	<div class="w-2/3">


	</div>
	<div class="w-1/3 border-l-2 pl-2 italic text-sm text-darkest">
		<div class="p-2">
			<span class="font-bold text-black">Goals</span> are how to track revenue goals by year, quarter or month.
		</div>
	</div>
</div>



<div class="flex-1 pb-8 text-grey-dark">		

@if($goals->first())
	<div class="table w-full">

		<div class="table-row text-xs w-full">

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-8">
				
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-10">
				
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b text-right">
				Year
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b text-right">
				Quarter
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b text-right">
				Goal
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b text-right">
				Met
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b text-right">
				%
			</div>
		</div>


		@foreach($goals as $goal)
			<div class="table-row 	w-full mb-1 w-full text-grey-darker">

				<div class="table-cell p-2 align-middle border-b">
					{{ $loop->iteration }}.
				</div>


				<div class="table-cell p-2 align-middle border-b w-10">
					<a href="/{{ Auth::user()->team->app_type }}/goals/{{ $goal->id }}/delete">
						<i class="fas fa-times"></i>
					</a>
				</div>

				<div class="table-cell p-2 align-middle border-b  text-right">
					{{ $goal->year }}
				</div>

				<div class="table-cell p-2 align-middle border-b  text-right">
					{{ $goal->quarter }}
				</div>

				<div class="table-cell p-2 align-middle border-b text-blue text-right">
					${{ number_format($goal->amount) }}
				</div>

				<div class="table-cell p-2 align-middle border-b text-blue text-right">
					${{ number_format($goal->totalMet) }}
				</div>

				<div class="table-cell pl-4 align-middle border-b text-blue text-right w-1/3">

					@if($goal->amount > 0 && $goal->totalMet > 0)
						<div class="flex h-6">
							

							<div class="z-50 absolute text-white text-xs pt-1 pl-1">
								{{ number_format(round($goal->totalMet/$goal->amount *100)) }} %
							</div>

							<div class="bg-green-dark rounded p-2 text-xs" style="width:{{ round($goal->totalMet/$goal->amount *100) }}%" />
							</div>
							
						</div>
					@endif

				</div>
			</div>
		@endforeach

	</div>
@endif


</div>






@endsection

@section('javascript')
	

@endsection
