@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Edit Constituent
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Edit Constituent', 'edit_person') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="{{dir}}/constituents/{{ $person->id }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<input type="submit" name="save" value="Save" class="mr-2 rounded-full bg-grey-darker hover:bg-grey-dark text-white float-right text-sm px-8 py-2 mt-1 shadow ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow" />


		<span class="text-2xl">
		<i class="fas fa-user-circle mr-2"></i>
		@lang('Edit')
		</span>
	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Name')
			</td>
			<td class="p-2">

				<input name="first_name" value="{{ $person->first_name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>
				<input name="middle_name" value="{{ $person->middle_name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-24"/>
				<input name="last_name" value="{{ $person->last_name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>


				@if($person->full_name)
				<div class="text-grey-darker p-2 font-thin">{{ $person->full_name }} ({{ $person->full_name_middle }})</div>
				@endif

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Gender')
			</td>
			<td class="p-2">

				<select name="gender">
					<option {{ ($person->gender == "") ? 'selected' : '' }} value="">None selected</option>
					<option {{ ($person->gender == "F") ? 'selected' : '' }} value="F">F</option>
					<option {{ ($person->gender == "M") ? 'selected' : '' }} value="M">M</option>
					<option {{ ($person->gender == "X") ? 'selected' : '' }} value="X">X</option>
				</select>

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				@lang('Address')
			</td>

			<td class="p-2">

				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="Street Number" name="address_number" value="{{ $person->address_number }}" class="border-2 rounded-lg px-4 py-2 w-24"/>
				 - <input data-toggle="tooltip" data-placement="top" title="Optional Fraction (such as 1/2)" name="address_fraction" value="{{ $person->address_fraction }}" class="border-2 rounded-lg px-4 py-2 w-16"/>
				<input data-toggle="tooltip" data-placement="top" title="Street Name" name="address_street" value="{{ $person->address_street }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
				Apt# <input data-toggle="tooltip" data-placement="top" title="Apartment Number" name="address_apt" value="{{ $person->address_apt }}" class="border-2 rounded-lg px-4 py-2 w-24"/>

				</div>
				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="City" name="address_city" value="{{ $person->address_city }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
				<input data-toggle="tooltip" data-placement="top" title="State" name="address_state" value="{{ $person->address_state }}" class="border-2 rounded-lg px-4 py-2 w-24"/>
				<input data-toggle="tooltip" data-placement="top" title="Zip" name="address_zip" value="{{ $person->address_zip }}" class="border-2 rounded-lg px-4 py-2 w-24"/>

				</div>

				<div class="text-grey-darker p-2 font-thin">
					{{ $person->full_address}} 
					<div class="text-xs font-mono">Unique Household ID: {{ $person->household_id }}</div>
				</div>

			</td>
		</tr>


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				Emails
			</td>
			<td class="p-2">

				<div class="pb-2">
					<input name="primary_email" value="{{$person->primary_email}}" class="border-2 rounded-lg px-4 py-2 w-1/2" placeholder="primary@email.com" />

					<span class="ml-2">Primary</span>
				</div>

				<div class="pb-2">
					<input name="work_email" value="{{$person->work_email}}" class="border-2 rounded-lg px-4 py-2 w-1/2" placeholder="work@email.com" />

					<span class="ml-2">Work</span>
				</div>

				<?php $i=0; ?>

				@if($person->other_emails)
					@foreach(json_decode($person->other_emails) as $theemail)
						<?php $i++; ?>
						<div class="pb-2">
<!-- 							<input name="email_main" value="{{ $i }}" type="radio" {!! ($theemail->main) ? 'checked' : '' !!} /> -->
							<input name="email_{{ $i }}" value="{{$theemail->email}}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
							<input name="email_notes_{{ $i }}" placeholder="Notes" value="{{$theemail->notes}}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
						</div>
					@endforeach
				@endif

				<?php $i++; ?>
				<div class="pb-2">
<!-- 					<input name="email_main" {{ ($i == 1) ? 'checked' : '' }} value="{{ $i }}" type="radio" /> -->
					<input name="email_{{ $i }}" placeholder="New@Email.Address" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
					<input name="email_notes_{{ $i }}" placeholder="Notes" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

				<input type="hidden" name="email_number" value="{{ $i }}" />

			</td>
		</tr>



		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				Phones
			</td>
			<td class="p-2">

				<div class="pb-2">
					<input name="primary_phone" value="{{$person->primary_phone}}" class="border-2 rounded-lg px-4 py-2 w-1/2" placeholder="(617) 000-0000" />

					<span class="ml-2">Primary</span>
				</div>


				<?php $i=0; ?>

				@if($person->other_phones)
					@foreach(json_decode($person->other_phones) as $thephone)
						<?php $i++; ?>
						<div class="pb-2">
<!-- 							<input name="phone_main" value="{{ $i }}" type="radio" {!! ($thephone->main) ? 'checked' : '' !!} /> -->
							<input name="phone_{{ $i }}" value="{{$thephone->phone}}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
							<input name="phone_notes_{{ $i }}" placeholder="Notes" value="{{$thephone->notes}}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
						</div>
					@endforeach
				@endif

				<?php $i++; ?>
				<div class="pb-2">
<!-- 					<input name="phone_main" {{ ($i == 1) ? 'checked' : '' }} value="{{ $i }}" type="radio" /> -->
					<input name="phone_{{ $i }}" placeholder="New Phone #" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
					<input name="phone_notes_{{ $i }}" placeholder="Notes" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

				<input type="hidden" name="phone_number" value="{{ $i }}" />

			</td>
		</tr>




		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Notes')
			</td>
			<td class="p-2">
				<textarea name="private" rows="10" type="text" class="border-2 rounded-lg px-4 py-2 w-full">{{ $person->private }}</textarea>
			</td>
		</tr>



	</table>

	<div class="text-2xl font-sans pb-3">
		<button class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-2 shadow">
			Save
		</button>
	</div>
</form>

<br /><br />


@endsection

@section('javascript')

@endsection