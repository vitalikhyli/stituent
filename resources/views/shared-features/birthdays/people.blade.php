@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Birthdays')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
    <!-- <a href="/{{ Auth::user()->team->app_type }}/constituents">Constituents</a> > -->
	&nbsp; <b>@lang('Birthdays')</b> 

@endsection

@section('style')

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full font-bold">
		 @lang('Birthdays')
		 <span class="text-xl text-grey-dark font-italic">
		 	&nbsp;this week (<span class="bg-yellow-lighter">Today</span>)
		 </span>

	</div>

</div>

<div class="flex w-full">
	<table class="table text-grey-dark text-sm">
		@foreach ($birthdays as $birthday)
			@if ($birthday->dob->setYear(date('Y')) == \Carbon\Carbon::today())
				<tr class="bg-yellow-lighter">
			@else
				<tr>
			@endif
				<td class="text-grey">{{ $loop->iteration }}.</td>
				<td class="whitespace-no-wrap">
					<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $birthday->id }}">
		        		{{ $birthday->name }}
		        	</a>
		        </td>
		        <td class="text-black whitespace-no-wrap"> 
					@if ($birthday->dob->setYear(date('Y')) < \Carbon\Carbon::today())
						Turned <b>{{ $birthday->age }}</b> {{ $birthday->dob->setYear(date('Y'))->diffForHumans() }}
					@elseif ($birthday->dob->setYear(date('Y')) == \Carbon\Carbon::today())
						Turning <b>{{ $birthday->age }}</b> today
					@else
						Turning <b>{{ $birthday->age + 1 }}</b> on {{ $birthday->dob->format('n/j') }}
					@endif
				</td>
				<td class="whitespace-no-wrap">
					{{ $birthday->full_address }}
				</td>

				<td> 

					@include('shared-features.constituents.activity-icons', ['person' => $birthday])
				</td>
				
			</tr>
		@endforeach
	</table>
</div>

@endsection


