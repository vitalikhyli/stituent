@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit @lang('Constituent')
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">@lang('Constituents')</a> >
	<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">{{ $person->name }}</a>
    > &nbsp;<b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<span class="text-2xl">
		<i class="fas fa-user-circle mr-2"></i>
		@lang('Edit') @lang('Constituent')
		</span>
	</div>

	@if($person->deceased)
		<div class="p-4 border-b bg-red-lightest border-b-4 font-bold">
			{{ $person->full_name }} is <a href="#deceased" class="text-red">deceased</a>
			@if($person->deceased_date)
				({{ \Carbon\Carbon::parse($person->deceased_date)->diffForHumans() }})
			@endif
		</div>
	@endif

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Title')
			</td>
			<td class="p-2">

				<input name="name_title" type="text" class="form-control w-1/6" value="{{ $person->name_title }}" />

			</td>
		</tr>

		

		<tr class="">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Name')
			</td>
			<td class="p-2">

				<input name="first_name" value="{{ $person->getAttributes()['first_name'] }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>
				<input name="middle_name" value="{{ $person->middle_name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-24"/>
				<input name="last_name" value="{{ $person->last_name }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>


				@if($person->full_name)
				<div class="text-grey-darker p-2 font-thin">{{ $person->full_name }} ({{ $person->full_name_middle }})</div>
				@endif

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Nickname / Preferred')
			</td>
			<td class="p-2">

				<input name="nickname" value="{{ $person->nickname }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>

				<div class="text-grey-darker p-2 font-thin">
					The name you enter here will REPLACE the first name.
				</div>


			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Language')
			</td>
			<td class="p-2">

				<input name="language" value="{{ $person->language }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>

				<div class="text-grey-darker p-2 font-thin">
					Primary language spoken
				</div>


			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Date of Birth')
			</td>
			<td class="p-2">

				<input name="dob" value="{{ $person->dob_readable }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>

			</td>
		</tr>	

		

		<tr class="">
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
			<!-- Make optional based on account setting? -->
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Pronouns')
			</td>
			<td class="p-2">

				<input name="pronouns" value="{{ $person->pronouns }}" class="font-bold border-2 rounded-lg px-4 py-2 w-1/3"/>

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				@lang('Address')
			</td>

			<td class="p-2">

				<div class="p-1">

					<input data-toggle="tooltip" data-placement="top" title="Street Number" name="address_number" value="{{ $person->address_number }}" class="household border-2 rounded-lg px-4 py-2 w-24"/>

					 - <input data-toggle="tooltip" data-placement="top" title="Optional Fraction (such as 1/2)" name="address_fraction" value="{{ $person->address_fraction }}" class="household border-2 rounded-lg px-4 py-2 w-16"/>

					<input data-toggle="tooltip" data-placement="top" title="Street Name" name="address_street" value="{{ $person->address_street }}" class="household border-2 rounded-lg px-4 py-2 w-1/2"/>

					Apt# <input data-toggle="tooltip" data-placement="top" title="Apartment Number" name="address_apt" value="{{ $person->address_apt }}" class="household border-2 rounded-lg px-4 py-2 w-24"/>

				</div>
				<div class="p-1">

					<input data-toggle="tooltip" data-placement="top" title="City" name="address_city" value="{{ $person->address_city }}" class="household border-2 rounded-lg px-4 py-2 w-1/2"/>

					<input data-toggle="tooltip" data-placement="top" title="State" placeholder="{{ Auth::user()->team->account->state }}" name="address_state" value="{{ $person->address_state }}" class="household border-2 rounded-lg px-4 py-2 w-24"/>

					<input data-toggle="tooltip" data-placement="top" title="Zip" name="address_zip" value="{{ $person->address_zip }}" class="household border-2 rounded-lg px-4 py-2 w-24"/>

				</div>

				<div class="text-grey-darker p-2 font-thin">
					<span class="hidden">
						{{ $person->full_address}} 
					</span>
					<div class="text-sm font-mono">
						Unique Household ID: <span id="household_id" class="text-blue">{!! ($person->household_id) ? $person->household_id_pretty : 'Household ID not created' !!}</span>
					</div>
				</div>

			</td>
		</tr>


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				@lang('Mailing Info')
			</td>

			<td class="p-2">
				@if($mailing->empty || !$person->mailing_info)
					<div id="add_mailing_info" class="text-grey-darker font-thin">
						<button type="button" id="add_mailing_info_button" class="rounded-lg bg-grey-lighter text-black px-2 py-1 text-sm border">Adding Mailing Info</button>
					</div>
				@endif

				<div id="mailing_info" class="{{ ($mailing->empty) ? 'hidden' : '' }}">
					<div class="p-1">

						<input data-toggle="tooltip" data-placement="top" title="Address" name="mailing_address" value="{{ $mailing->address }}" class="household border-2 rounded-lg px-4 py-2 w-2/3"/>

					</div>

					<div class="p-1">

						<input data-toggle="tooltip" data-placement="top" title="Address2" name="mailing_address2" value="@isset($mailing->address2){{ $mailing->address2 }}@endisset" class="household border-2 rounded-lg px-4 py-2 w-2/3"/>

					</div>
					<div class="p-1">

						<input data-toggle="tooltip" data-placement="top" title="City" name="mailing_city" value="@isset($mailing->city){{ $mailing->city }}@endisset" class="household border-2 rounded-lg px-4 py-2 w-1/2"/>

						<input data-toggle="tooltip" data-placement="top" title="State" name="mailing_state" value="@isset($mailing->state){{ $mailing->state }}@endisset" class="household border-2 rounded-lg px-4 py-2 w-24"/>

						<input data-toggle="tooltip" data-placement="top" title="Zip" name="mailing_zip" value="@isset($mailing->zip){{ $mailing->zip }}@endisset" class="household border-2 rounded-lg px-4 py-2 w-24"/>
						 - 
						<input data-toggle="tooltip" data-placement="top" title="Zip + 4" name="mailing_zip4" value="@isset($mailing->zip4){{ $mailing->zip4 }}@endisset" class="household border-2 rounded-lg px-4 py-2 w-24"/>

					</div>

					<div class="text-grey-darker p-2 font-thin">
						<button type="button" id="use_same_address_button" class="rounded-lg bg-grey-lighter text-black px-2 py-1 float-right text-sm border">Make this same as regular address</button>
					</div>

				</div>

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Social Media')
			</td>
			<td class="p-2">

				<textarea name="social_media" rows="3" class="font-bold border-2 rounded-lg px-4 py-2 w-1/2">{{ $person->social_media }}</textarea>

				<div class="text-grey-darker p-2 font-thin">
					Enter urls here, separated by spaces
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

				@foreach($other_emails as $obj)
					<div class="pb-2">
						<input name="email_{{ $loop->iteration }}" value="{{ $obj->contact }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
						<input name="email-notes_{{ $loop->iteration }}" placeholder="Notes" value="{{ $obj->notes }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
					</div>
				@endforeach

				<div class="pb-2">
					<input name="email_new" placeholder="New@Email.Address" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
					<input name="email-notes_new" placeholder="Notes" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

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

				@foreach($other_phones as $obj)
					<div class="pb-2">
						<input name="phone_{{ $loop->iteration }}" value="{{ $obj->contact }}" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
						<input name="phone-notes_{{ $loop->iteration }}" placeholder="Notes" value="{{ $obj->notes }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
					</div>
				@endforeach

				<div class="pb-2">
					<input name="phone_new" placeholder="New Phone #" value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
					<input name="phone-notes_new" placeholder="Notes" class="border-2 rounded-lg px-4 py-2 w-1/3"/>
				</div>

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



		<tr class="border-t-4 border-red">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				<a name="deceased"></a>
				Other
			</td>
			<td class="p-2">
				<label for="deceased" class="font-normal">
					<input value="1" type="checkbox" {{ ($person->deceased) ? 'checked' : '' }} name="deceased" id="deceased" /> Deceased
				</label>
				
				<input name="deceased_date" class="datepicker ml-2 border-2 rounded-lg px-4 py-2 w-1/4" placeholder="Date" value="{{ ($person->deceased_date) ? \Carbon\Carbon::parse($person->deceased_date)->format('m/d/Y') : '' }}" />
				
			</td>
		</tr>



	</table>

	<div class="text-2xl font-sans pb-3">

	     <button type="button" data-toggle="modal" data-target="#deleteModal" name="remove" id="remove" class="rounded-lg px-4 py-2 text-red text-center text-sm float-left mt-2">
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete @lang('Constituent')
	     </button>

		<input type="submit" name="save" value="Save" class="mr-2 rounded-lg bg-blue hover:bg-orange-dark text-white float-right text-sm px-8 py-2 mt-1 shadow ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-lg bg-blue-darker hover:bg-oranger-dark text-white float-right text-sm px-8 py-2 mt-1 shadow" />



	</div>
</form>

<br /><br />


<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this constituent?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->


@endsection

@section('javascript')
<script type="text/javascript">
	

// var key = "happyCount";
// var obj = {};
// obj[key] = someValueArray;
// myArray.push(obj);

function updateHouseholdDiv(){
	
	var address = {};

	$('.household').each(function () {
		data = $(this).val().toUpperCase().replace(/ /g, '-');
		type = $(this).attr('name');
		address[type] = data; 
	});


	id = "Not enough information to create Household ID";
	ok = false

	if ((address['address_state'] !== "") &&
		(address['address_city'] !== "") &&
		(address['address_street'] !== "") &&
		(address['address_number'] !== "")
		)
		{
			ok = true;
			z = "0";
			id = "";
			id += String(address['address_state'] + z.repeat(2)).slice(0,2) + "|";
			id += String(address['address_city'] + z.repeat(15)).slice(0,15) + "|";
			id += String(address['address_street'] + z.repeat(15)).slice(0,20) + "|";
			id += (z.repeat(8) + address['address_number']).slice(-8) + "|";
			id += (z.repeat(5) + address['address_fraction']).slice(-5) + "|";
			id += (z.repeat(7) + address['address_apt']).slice(-7);

			id = id.replace(/0/g, '<span class="text-blue-lighter">0</span>');
		} 

	// $id = strtoupper(substr(str_pad($this->address_state, 2, 'Z', STR_PAD_LEFT),0,2).'|'.
	// 					Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
	// 					Str::slug(str_pad($this->address_street, 20, '0', STR_PAD_RIGHT)).'|'.
	// 					Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
	// 					Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
	// 					Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
	// 				);


	$('#household_id').html(id);

	if (ok == true) {
		$('#household_id').removeClass('text-red font-bold');
		$('#household_id').addClass('text-blue');
	} else {
		$('#household_id').addClass('text-red font-bold');
		$('#household_id').removeClass('text-blue');
	}
}



$(document).ready(function() {

	$('#add_mailing_info_button').on('click', function () {
		$('#add_mailing_info').toggleClass('hidden');
		$('#mailing_info').toggleClass('hidden');
	});


	$('#use_same_address_button').on('click', function () {

		address 		= ""
		address 		+= $('[name="address_number"]').val() + " ";
		address 		+= $('[name="address_fraction"]').val() + " ";
		address 		+= $('[name="address_street"]').val() + " ";
		address 		+= $('[name="address_apt"]').val() + " ";
		address 		= address.trim().replace(/ +(?= )/g,'');
		city 			= $('[name="address_city"]').val();
		state 			= $('[name="address_state"]').val();
		zip 			= $('[name="address_zip"]').val().slice(0,5);
		zip4 			= (zip.length > 5) ? $('[name="address_zip"]').val().slice(-4) : null;


		$('[name="mailing_address"]').val(address);
		$('[name="mailing_city"]').val(city);
		$('[name="mailing_state"]').val(state);
		$('[name="mailing_zip"]').val(zip);
		if (zip4 != null) $('[name="mailing_zip4"]').val(zip4);
	});

	$('.household').on('change keyup', function () {
		updateHouseholdDiv();
	})  

});


</script>
@endsection