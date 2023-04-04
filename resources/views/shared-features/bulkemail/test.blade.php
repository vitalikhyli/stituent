@extends('office.base')

@section('title')
    Edit Email
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Edit', 'edit', 'level_1') !!}

@endsection

@section('style')


	<style>


	</style>

@endsection


@section('main')



	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">

			<span class="text-2xl">
				Send a Test Email
			</span>

		</div>

	</div>



	@include('elements.errors')

	<form action="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/testconfirm" method="post">

		@csrf
		<div class="text-left py-4 border-b flex">
			<div class="w-1/6">
				Subject
			</div>
			<div class="font-bold w-full">
				{{ $email->subject }}
			</div>
		</div>

		<div class="text-left py-4 border-b flex w-full">
			<div class="w-1/6">
				To:
			</div>
			<div class="font-medium text-blue-dark w-full">
				<input type="text" name="email" class="rounded-lg p-2 border w-1/2" placeholder="Enter email here" value="{{ $errors->any() ? old('email') : Auth::user()->email }}" />
			</div>
		</div>

		@if(1 == 2)
		<div class="text-left py-4 border-b flex w-full">
			<div class="w-1/6">
				Test Person:
			</div>
			<div class="font-medium text-black w-full">
				
				<select name="person_id">
					@foreach($test_people as $person)
						<option value="{{ $person->id }}">{{ $person->full_name }}</option>
					@endforeach
				</select>

				<div class="text-sm text-grey-darker p-2">
					The purpose of choosing a test person is in case you included merge fields like {% first_name %} in your email. The test email will use this person's information for that, but it will still go to the email address you specify.
				</div>

			</div>
		</div>
		@endif

		<div class="text-center p-2 mt-4">

			<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/edit">
				<button type="button" class="rounded-lg text-white bg-black px-4 py-2">
					Cancel
				</button>
			</a>

			<button type="submit" class="rounded-lg text-white bg-blue-dark px-4 py-2">
				Send the Test Now
			</button>

		</div>

	</form>


	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">
			<span class="text-2xl">
				Preview
			</span>
		</div>
	</div>

	<div class="bg-grey-lighter p-4 text-grey-darker">
		(For preview purposes, a fake person has been used for your merge fields.)
	</div>

	<center>
		<div class="w-5/6 border p-4 shadow mt-4 text-left">
			{!! $preview_html !!}
		</div>
	</center>

<br />
<br />

@endsection

@section('javascript')



@endsection
