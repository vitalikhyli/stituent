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
	Edit Prospect
</div>




<form action="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}/update" method="post">

	@csrf

<div class="text-grey-darkest text-black">

	<div class="flex">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Name</div>
		<div class="w-3/4 p-2">
			<input type="text" name="name" value="{{ $opportunity->entity->name }}" class="font-bold w-3/4 border p-2 text-lg text-blue" />
		</div>

	</div>

	<div class="flex">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Department</div>
		<div class="w-3/4 p-2">
			<input type="text" name="department" value="{{ $opportunity->entity->department }}" class="font-bold w-3/4 border p-2" />
		</div>

	</div>

	<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Website</div>
		<div class="w-3/4 p-2">
			<input type="text" name="url" value="{{ $opportunity->entity->url }}" class="w-3/4 border p-2" />
		</div>

	</div>


	<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Address</div>
		<div class="w-3/4 p-2">
			<input type="text" name="address_number" size="4" value="{{ $opportunity->entity->address_number}}" placeholder="#" class="border p-2" />
			<input type="text" name="address_street"value="{{ $opportunity->entity->address_street}}" placeholder="Street" class="border p-2 w-1/2" />
			<input type="text" name="address_apt" value="{{ $opportunity->entity->address_apt}}" placeholder="Apt" class="border p-2" size="4" />
		</div>

	</div>

	<div class="flex border-b pb-10">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">City, State, Zip</div>
		<div class="w-3/4 p-2">
			<input type="text" name="address_city" value="{{ $opportunity->entity->address_city}}" placeholder="City" class="border p-2 w-1/2" />
			<input type="text" name="address_state" value="{{ $opportunity->entity->address_state}}" placeholder="State" size="4" class="border p-2" />
			<input type="text" name="address_zip" value="{{ $opportunity->entity->address_zip}}" placeholder="Zip" size="8" class="border p-2" />
		</div>

	</div>

<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-3">Client?</div>
		<div class="w-3/4 p-2 pt-3">
			<label for="check_in" class="font-normal">
                <input type="checkbox" {{ ($opportunity->client) ? 'checked' : '' }} name="client" id="check_in" value="1" /> Is a Client
            </label>
		</div>

	</div>

	<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Check Ins</div>
		<div class="w-3/4 p-2">
				Check In Every
				<input type="text" name="days_check_in" value="{{ $opportunity->days_check_in }}" size="2" class="border px-2 py-1" placeholder="0" /> Days, next on
				<input type="text" name="next_check_in" value="{{ $opportunity->next_check_in }}" size="10" class="border px-2 py-1" placeholder="0000-00-00" />
				
				<span class="text-sm">
					@if($opportunity->lastCheckIn)
						- last was {{ Carbon\Carbon::parse($opportunity->lastCheckIn)->toDateString() }}
					@endif
				</span>
		</div>

	</div>

	<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right pt-4">Type</div>
		<div class="w-3/4 p-2">
			<select name="type">
				@foreach($prospect_types as $type)
					<option {{ ($opportunity->type == $type) ? 'selected' : '' }} value="{{ $type }}">{{ $type }}</option>
				@endforeach
			</select>
			<input type="text" name="new_type" class="border p-2" placeholder="New Type"/>
		</div>

	</div>

	<div class="flex border-b">

		<div class="w-1/6 p-2 uppercase text-sm text-right">Pattern</div>
		<div class="w-3/4 p-2">
			<select name="pattern_id">

				<option {{ ($opportunity->pattern_id == '') ? 'selected' : '' }} value="">-- None --</option>

				@foreach($patterns as $pattern)
					<option {{ ($opportunity->pattern_id == $pattern->id) ? 'selected' : '' }} value="{{ $pattern->id }}">{{ $pattern->name }}</option>
				@endforeach

			</select>
		</div>

	</div>






</div>

 
<div class="pt-2 mt-2 text-right">
	<button class="rounded-lg bg-blue text-white px-3 py-2">Save</button>
	<button formaction="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}/update/close" class="rounded-lg bg-green-dark text-white px-3 py-2">Save and Close</button>
</div>

</form>


@endsection

@section('javascript')
	

@endsection
