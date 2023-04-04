@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit {{ $entity->name }}
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/organizations">Organizations</a> 

    > <a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}">{{ $entity->name }}</a>
    > <b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

	     <button type="button" data-toggle="modal" data-target="#deleteModal" name="remove" id="remove" class="rounded-lg py-2 text-red text-center text-sm float-right mt-2">
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Organization
	     </button>

		<span class="text-xl">
		<i class="fas fa-building mr-2"></i>
			<span class="text-grey-dark">@lang('Edit'):</span>
			{{ $entity->name }}
		</span>

	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Organization Type
			</td>
			<td class="p-2">

				<div class="flex">



					@if($entity_types->count() > 0)

						<select name="type" class="form-control w-1/3">

							<option value="">-- Select a Type --</option>

							@foreach ($entity_types as $type)
								@if ($type)
									@if ($type == $entity->type)
										<option value="{{ $type }}" selected="selected">{{ $type }}</option>
									@else
										<option value="{{ $type }}">{{ $type }}</option>
									@endif
								@endif
							@endforeach

						</select>

						<input name="new_type" placeholder="Add a New Type" value="" class="border px-4 py-2 w-1/3"/>

					@else

						<input name="new_type" placeholder="Add a New Type" value="" class="border px-4 py-2 w-1/3"/>

					@endif

				</div>

			</td>
		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Name')
			</td>
			<td class="p-2">

				<input name="name" value="{{ $entity->name }}" class="font-bold border px-4 py-2 w-full text-lg"/>

			</td>
		</tr>

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Description')
			</td>
			<td class="p-2">
				<textarea autocomplete="off" name="private" rows="6" type="text" class="border px-4 py-2 w-2/3" placeholder="Description goes here.">{{ $entity->private }}</textarea>
			</td>
		</tr>

<!-- 		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				@lang('Full Address')
			</td>
			<td class="p-2">

				<input name="address_raw" value="{{ $entity->address_raw }}" class="font-bold border px-4 py-2 w-full"/>

			</td>
		</tr> -->

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top">
				@lang('Address Parts')
			</td>

			<td class="p-2">

				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="Street Number" placeholder="Num" name="address_number" value="{{ $entity->address_number }}" class="border px-4 py-2 w-24"/>
				 - <input data-toggle="tooltip" data-placement="top" title="Optional Fraction (such as 1/2)" placeholder="" name="address_fraction" value="{{ $entity->address_fraction }}" class="border px-4 py-2 w-16"/>
				<input data-toggle="tooltip" data-placement="top" title="Street Name" placeholder="Street Name" name="address_street" value="{{ $entity->address_street }}" class="border px-4 py-2 w-1/2"/>
				Apt# <input data-toggle="tooltip" data-placement="top" title="Apartment Number" placeholder="" name="address_apt" value="{{ $entity->address_apt }}" class="border px-4 py-2 w-24"/>

				</div>
				<div class="p-1">

				<input data-toggle="tooltip" data-placement="top" title="City" placeholder="City or Town" name="address_city" value="{{ $entity->address_city }}" class="border px-4 py-2 w-1/2"/>
				<input data-toggle="tooltip" data-placement="top" title="State" placeholder="State" name="address_state" value="{{ $entity->address_state }}" class="border px-4 py-2 w-24"/>
				<input data-toggle="tooltip" data-placement="top" title="Zip" placeholder="Zip" name="address_zip" value="{{ $entity->address_zip }}" class="border px-4 py-2 w-24"/>

				</div>

				<div class="text-grey-darker p-2 font-thin">
					{{ $entity->full_address}} 
					@if($entity->household_id)
						<div class="text-xs font-mono">
							Unique Household ID: {{ $entity->household_id }}
						</div>
					@endif
				</div>

			</td>
		</tr>

		

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top">
				@lang('Social Media')
			</td>

			<td class="p-2 flex">

				<div class="p-1">

					<input data-toggle="tooltip" data-placement="top" title="Twitter" placeholder="@Twitter" name="social_twitter" value="{{ $entity->social_twitter }}" class="border px-4 py-2" />

				</div>

				<div class="p-1">

					<input data-toggle="tooltip" data-placement="top" title="Facebook" placeholder="Facebook" name="social_facebook" value="{{ $entity->social_facebook }}" class="border px-4 py-2" />

				</div>


			</td>
		</tr>

		


		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top">
				Contacts
			</td>

			<td class="p-2">

				<?php $i = 0; ?>
				@if($entity->contact_info)
					@foreach($entity->contact_info as $thecontact)

					<div class="border-b-4 border-grey-lightest pb-1 mb-3">
						<div class="w-full pl-2">
							<input name="name_{{ $i }}" placeholder="Contact" value="{{ $thecontact['name'] }}" class="font-bold border px-4 py-2 w-full mb-2"/>
						</div>

						<div class="flex w-full mb-2">

							<div class="w-1/2 px-2 flex">
								<i class="fas fa-envelope float-left text-lg mx-4 mt-2 text-blue"></i>
								<input name="email_{{ $i }}" placeholder="Email" value="{{ $thecontact['email'] }}" class="border px-4 py-2 flex-grow"/>
							</div>

							<div class="w-1/2 pr-2 flex">
								<i class="fas fa-phone float-left text-lg mx-4 mt-2 text-blue"></i>
								<input name="phone_{{ $i++ }}" placeholder="Phone" value="{{ $thecontact['phone'] }}" class="border px-4 py-2 flex-grow"/>
							</div>

						</div>
					</div>
					@endforeach
				@endif

				<div class="w-full">
					<input name="name_{{ $i }}" placeholder="Contact" value="" class="border px-4 py-2 w-full mb-2"/>
				</div>

				<div class="flex w-full">
	
					<div class="w-1/2 pl-2">
						<input name="email_{{ $i }}" placeholder="Email" value="" class="border px-4 py-2 w-full"/>
					</div>

					<div class="w-1/2 pr-2">
						<input name="phone_{{ $i++ }}" placeholder="Phone" value="" class="border px-4 py-2 w-full mb-2 ml-2"/>
					</div>

				</div>

			</td>
		</tr>


		



	</table>

	<div class="text-2xl font-sans py-2">


		<input type="submit" name="save" value="Save" class="mr-2 rounded-lg bg-blue-dark hover:bg-blue-darker text-white float-right text-sm px-8 py-2 mt-1 ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-lg bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1" />

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
            Are you sure you want to delete this organization?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->



@endsection

@section('javascript')

@endsection