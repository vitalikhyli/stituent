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


<!-- Include stylesheet -->


<!-- Create the editor container -->



	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">

			<span class="text-2xl">
				Ready to Send this Email?
			</span>
		</div>

	</div>



	@include('elements.errors')

	<div class="text-left py-4 border-b flex">
		<div class="w-1/6">
			Subject
		</div>
		<div class="font-bold">
			{{ $email->subject }}
		</div>
	</div>

	<div class="text-left py-4 border-b flex">
		<div class="w-1/6">
			Going to
		</div>
		<div class="font-medium text-blue-dark">
			{{ $email->expected_count }} Recipients
		</div>
	</div>

	<div class="text-center p-2 mt-4">

		<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/edit">
			<button class="rounded-lg text-white bg-black px-4 py-2">
				No, Cancel
			</button>
		</a>

		<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/queueconfirm">
			<button class="rounded-lg text-white bg-blue-dark px-4 py-2">
				Yes, Send Now
			</button>
		</a>

	</div>



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
