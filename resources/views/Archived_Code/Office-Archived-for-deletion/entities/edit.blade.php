@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Edit Organizations
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Edit Organizations', 'edit_organization') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="{{dir}}/entities/{{ $entity->id }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<input type="submit" name="save" value="Save" class="mr-2 rounded-full bg-grey-darker hover:bg-grey-dark text-white float-right text-sm px-8 py-2 mt-1 shadow ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow" />


		<span class="text-2xl">
		<i class="fas fa-building mr-2"></i>
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

				<input name="name" value="{{ $entity->name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-full"/>

			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				@lang('Address')
			</td>

			<td class="p-2">

				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="Street Number" name="address_number" value="{{ $entity->address_number }}" class="border-2 rounded-lg px-4 py-2 w-24"/>
				 - <input data-toggle="tooltip" data-placement="top" title="Optional Fraction (such as 1/2)" name="address_fraction" value="{{ $entity->address_fraction }}" class="border-2 rounded-lg px-4 py-2 w-16"/>
				<input data-toggle="tooltip" data-placement="top" title="Street Name" name="address_street" value="{{ $entity->address_street }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
				Apt# <input data-toggle="tooltip" data-placement="top" title="Apartment Number" name="address_apt" value="{{ $entity->address_apt }}" class="border-2 rounded-lg px-4 py-2 w-24"/>

				</div>
				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="City" name="address_city" value="{{ $entity->address_city }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
				<input data-toggle="tooltip" data-placement="top" title="State" name="address_state" value="{{ $entity->address_state }}" class="border-2 rounded-lg px-4 py-2 w-24"/>
				<input data-toggle="tooltip" data-placement="top" title="Zip" name="address_zip" value="{{ $entity->address_zip }}" class="border-2 rounded-lg px-4 py-2 w-24"/>

				</div>

				<div class="text-grey-darker p-2 font-thin">
					{{ $entity->full_address}} 
					<div class="text-xs font-mono">Unique Household ID: {{ $entity->household_id }}</div>
				</div>

			</td>
		</tr>

<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Contacts
			</td>
			<td class="p-2">


			<?php $i = 0; ?>
			@if(!$errors->any())
				@if($entity->contact_info)
					@foreach($entity->contact_info as $thecontact)
					<div class="flex w-full mb-2 text-sm">
		
						<div class="w-1/3 pr-2">
							<input name="name_{{ $i }}" placeholder="Name" value="{{ ($errors->any()) ? old('name_'.$i) : $thecontact['name'] }}" class="border-2 rounded-lg px-4 py-2 w-full font-semibold"/>
						</div>

						<div class="w-1/3 pr-2">
							<input name="phone_{{ $i }}" placeholder="Phone" value="{{ ($errors->any()) ? old('phone_'.$i) : $thecontact['phone'] }}" class="border-2 rounded-lg px-4 py-2 w-full "/>
						</div>

						<div class="w-1/3">
							<input name="email_{{ $i++ }}" placeholder="Email" value="{{ ($errors->any()) ? old('email_'.$i++) : $thecontact['email'] }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
						</div>

					</div>
					@endforeach
				@endif
				<div class="flex w-full text-sm">
	
					<div class="w-1/3 pr-2">
						<input name="name_{{ $i }}" placeholder="New Name" value="{{ ($errors->any()) ? old('name_'.$i) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full font-semibold"/>
					</div>

					<div class="w-1/3 pr-2">
						<input name="phone_{{ $i }}" placeholder="New Phone" value="{{ ($errors->any()) ? old('phone'.$i) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full "/>
					</div>

					<div class="w-1/3">
						<input name="email_{{ $i++ }}" placeholder="New Email" value="{{ ($errors->any()) ? old('email_'.$i++) : '' }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
					</div>


				</div>
			@else

				<?php
					$e = 0;
					foreach(old() as $key => $value){
						if (("email_" == substr($key,0,6)) || ("name_" == substr($key,0,6))) {
							$e++;
						}
					}
					$f = ($entity->contact_info) ? count($entity->contact_info) : 0;
					$contact_info_count = ($f >= $e) ? $f : $e;
				?>

				@for($num = 0; $num < $contact_info_count; $num++)
					<div class="flex w-2/3 mb-2">
		
						<div class="w-1/2 pr-2">
							<input name="name_{{ $i }}" placeholder="Name" value="{{ old('name_'.$num) }}" class="border-2 rounded-lg px-4 py-2 w-full "/>

						</div>
						<div class="w-1/2 pl-2">
							<input name="email_{{ $i++ }}" placeholder="Email" value="{{ old('email_'.$num) }}" class="border-2 rounded-lg px-4 py-2 w-full"/>
						</div>

					</div>
				@endfor

			@endif

			</td>
		</tr>




		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Notes')
			</td>
			<td class="p-2">
				<textarea name="private" rows="6" type="text" class="border-2 rounded-lg px-4 py-2 w-full">{{ $entity->private }}</textarea>
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