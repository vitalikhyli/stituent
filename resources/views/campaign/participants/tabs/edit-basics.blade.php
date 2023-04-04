<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-2 mb-2">
	Name
</div>

<div class="table-cell mt-2 text-grey-darker font-bold pb-1">
	<input name="first_name" type="text" value="{{ $model->first_name }}" class="border p-2 rounded w-32 text-blue-dark" />

	<input name="middle_name" type="text" size="4" value="{{ $model->middle_name }}" class="border p-2 rounded text-blue-dark" />

	<input name="last_name" type="text" value="{{ $model->last_name }}" class="border p-2 rounded text-blue-dark" />
</div>

<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-4">
	Address
</div>

<div class="pb-2">
	<div class="mt-1 text-grey-darker">
		<input name="address_number" size="4" type="text" value="{{ $model->address_number }}" class="border p-2 rounded text-blue-dark" placeholder="Number" data-toggle="tooltip" data-placement="top" title="Street Number" /> 

		<input name="address_fraction" size="4" type="text" value="{{ $model->address_fraction }}" class="border p-2 rounded text-blue-dark" placeholder="Fraction" data-toggle="tooltip" data-placement="top" title="Fraction (like 1/2, B, etc.)" /> 

		<input name="address_street" type="text" value="{{ $model->address_street }}" class="border p-2 rounded w-64 text-blue-dark" placeholder="Street" data-toggle="tooltip" data-placement="top" title="Street Name" />

		Apt <input name="address_apt" size="4" type="text" value="{{ $model->address_apt }}" class="border p-2 rounded text-blue-dark" placeholder="Apt" data-toggle="tooltip" data-placement="top" title="Apartment" />
	</div>

	<div class="mt-1 text-grey-darker">
		<input name="address_city" type="text" value="{{ $model->address_city }}" class="border p-2 rounded text-blue-dark" placeholder="City" data-toggle="tooltip" data-placement="top" title="City" />

		<input name="address_state" size="3" type="text" value="{{ $model->address_state }}" class="border p-2 rounded text-blue-dark" placeholder="State" data-toggle="tooltip" data-placement="top" title="State" />

		<input name="address_zip" size="6" type="text" value="{{ $model->address_zip }}" class="border p-2 rounded text-blue-dark" placeholder="Zip" data-toggle="tooltip" data-placement="top" title="Zip Code" />
	</div>
</div>


@php
	$cfplus = $model->cfPlus();
@endphp

@if ($cfplus)
<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-4">
	<div class="float-right">

		<x-cf-plus-modal :voter="$model"></x-cf-plus-modal>

	</div>
	CF+
</div>
<div class="py-2"></div>

<table class="text-left w-full">

       <tr>
	      <td class="border-b p-1 text-sm text-gray-500">Cell Phone</td>
	      <td class="border-b p-1 text-sm text-gray-900">
	      	@if ($cfplus)
	      		{{ $cfplus->cell_phone }}
	      	@else
	      		<span class="text-gray-300">Add Cf+ Data</span>
	      	@endif
	      </td>
	   </tr>
	   <tr>
	      <td class="border-b p-1 text-sm text-gray-500">Ethnic Description</td>
	      <td class="border-b p-1 text-sm text-gray-900">
	      	@if ($cfplus)
	      		{{ $cfplus->ethnic_description }}
	      	@else
	      		<span class="text-gray-300">Add Cf+ Data</span>
	      	@endif
			</td>
	   </tr>
	   <tr>
	      <td class="border-b p-1 text-sm text-gray-500">Est. Income</td>
	      <td class="border-b p-1 text-sm text-gray-900">
	      	@if ($cfplus)
	      		{{ $cfplus->estimated_income }}
	      	@else
	      		<span class="text-gray-300">Add Cf+ Data</span>
	      	@endif
	      	</td>
	   </tr>

  </table>
@endif

@if($model)
	@if ($model->household_id)
		@foreach($model->cohabitators() as $res)

			@if($loop->first)

				<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-2">
					Other Residents Here
				</div>

			@endif

			<div class="py-1 {{ ($loop->last) ? 'mb-4' : '' }}">
				<a href="/campaign/participants/{{ $res->id }}/edit">
					<i class="fas fa-user-circle mr-1"></i> {{ $res->full_name }}
				</a>
			</div>

		@endforeach
	@endif
@endif
