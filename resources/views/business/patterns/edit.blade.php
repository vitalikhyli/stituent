@extends('business.base')

@section('title')
    @lang('Prospects')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    <a href="/business/patterns">Patterns</a> >  
    Edit Pattern
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	Edit Pattern


</div>




<div class="flex-1 pb-8 text-grey-dark">		

<form action="/{{ Auth::user()->team->app_type }}/patterns/{{ $pattern->id }}/update" method="post">

	@csrf

	<div class="p-2 bg-green-lightest border-b">

		<span class="rounded-full text-green text-lg p-2 font-bold">Name</span>
		<input type="text" name="pattern_name" placeholder="Pattern Name" value="{{ $pattern->name }}" class="border p-2" />

		<span class="rounded-full text-green text-lg p-2 font-bold">Default for:</span>
			<select name="default_type">
				<option value="">-- None --</option>
				@foreach($prospect_types as $type)
					<option {{ ($pattern->default_type == $type) ? 'selected' : '' }} value="{{ $type }}">{{ $type }}</option>
				@endforeach
			</select>

	</div>


	@foreach($pattern->steps->sortBy('the_order') as $step)

		<div class="border-b p-2 text-sm">

			<input type="text" name="step_order_{{ $step->id }}" size="2" placeholder="0" value="{{ $step->the_order }}" class="border p-2 rounded-full bg-green text-white text-center" />

			<input type="text" name="step_name_{{ $step->id }}" value="{{ $step->name }}" class="border p-2 font-bold" />

		</div>

	@endforeach


	<div class="p-2 bg-green-lightest border-b">

		<span class="rounded-full text-green text-lg p-2 font-bold">{{ $pattern->steps->max('the_order') + 1 }}</span>
		<input type="text" name="new_step_name" placeholder="Add New Step" class="border p-2" />
	</div>

	<div class="mt-2">
		<button class="rounded-lg bg-blue text-white px-2 py-1">Save</button>
	</div>

</form>

</div>






@endsection

@section('javascript')
	

@endsection
