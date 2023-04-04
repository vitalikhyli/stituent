@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    New Case
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('New Case', 'edit_case') !!}


@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="{{dir}}/cases/save/{{ $contact->id }}">
	@csrf

	<div class="text-xl font-sans border-b-4 border-blue pb-3">

		<a href="{{dir}}/constituents/{{ $person_id }}">
			<button type="button" class="ml-4 rounded-full bg-grey-darkest hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Cancel
			</button>
		</a>


		<button class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save
		</button>

		<i class="fa fa-folder mr-2"></i>
		New Case
	
	</div>


	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full">


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				@lang('Subject')
			</td>
			<td class="p-2">
				<input type="text" name="subject" id="subject" placeholder="Add Subject" class="border-2 rounded-lg px-4 py-2 w-2/3 font-bold" />
			</td>
		</tr>


	</table>

</form>


@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

		$("#subject").focus();

	});

</script>
@endsection