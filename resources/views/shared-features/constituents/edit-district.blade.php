@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit District
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">Constituents</a> >
	<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">{{ $person->name }}</a>
    > &nbsp;<b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/update">
	@csrf

    <div class="text-2xl font-sans border-b-4 border-blue pb-3">

        <span class="text-2xl">
            <i class="fas fa-user-circle mr-2"></i> Edit {{ ucwords($district_type) }} District
        </span>

    </div>
	
    <div class="p-2">

        <select name="district_code">
            @foreach($districts as $thedistrict)
                <option value="{{ $thedistrict->code }}" {{ ($person->$district_field == $thedistrict->code) ? 'selected' : '' }}>
                    {{ $thedistrict->name }}
                    ({{ $thedistrict->elected_official_name }})
                </option>
            @endforeach
        </select>

        <input type="hidden" name="district_type" value="{{ $district_type }}" />

    </div>


	<div class="text-2xl font-sans pb-3">

        <input type="submit" name="save" value="Save" class="mr-2 rounded-lg bg-blue hover:bg-orange-dark text-white float-right text-base px-8 py-2 mt-1 shadow ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-lg bg-blue-darker hover:bg-oranger-dark text-white float-right text-base px-8 py-2 mt-1 shadow" />

	</div>


</form>

<br /><br />


@endsection

@section('javascript')

@endsection