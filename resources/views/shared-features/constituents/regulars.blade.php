@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Regulars')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> >
    <a href="/{{ Auth::user()->team->app_type }}/constituents">Constituents</a> > 
	&nbsp;<b>@lang('Regulars')</b> 

@endsection

@section('style')

	<style>

	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 w-full">

	<div class="w-1/3 text-3xl font-sans font-bold">
		 @lang('Regulars')
	</div>

	<div class="w-2/3 text-grey-dark pt-4 p-2 text-right">
		Showing <b>{{ $people->count() }}</b> with 3 or more contacts, cases, or groups
		(out of {{ Auth::user()->team->people()->count() }} linked people)
		<a target="_blank" class="bg-blue text-white rounded-full px-3 py-1 text-sm" href="?export=true">
			Export
		</a>
	</div>
</div>

<div class="">

	<table class="w-full text-grey-dark text-sm">
		@foreach ($people as $person)
			<tr>
				<td class="border-t p-1">{{ $loop->iteration }}.</td>
				<td class="border-t p-1">
					<a href="/{{ Auth::user()->app_type }}/constituents/{{ $person->id }}">
						{{ $person->name }}
					</a>
				</td>
				<td class="border-t p-1">
					
					{{ $person->address_city }}
					
				</td>
				<td class="border-t p-1 text-blue-light">
					
					@include('shared-features.constituents.activity-icons-simple')
					
				</td>
			</tr>
		@endforeach
	</table>

</div>





@endsection

@section('javascript')


@endsection
