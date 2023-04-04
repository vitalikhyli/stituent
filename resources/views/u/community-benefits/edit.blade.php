@extends('u.base')

@section('title')
    Add Community Benefit Program
@endsection

@section('breadcrumb')
    <a href="/u">Home</a> > <a href="/u/community-benefits">Community Benefits</a> > &nbsp;<b>Add Program</b>
@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')


<form method="POST" action="/u/community-benefits/{{ $program->id }}/update">

	{{ csrf_field() }}

<div class="text-2xl font-sans border-b-4 border-blue pb-2">


	<button type="submit" class="float-right bg-blue text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark shadow">
		Save
	</button>

	<button type="submit" formaction="/u/community-benefits/{{ $program->id }}/update/close" class="float-right bg-blue-dark text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark shadow">
		Save and Close
	</button>

	Edit Program
</div>


	@include('elements.errors')


	<table class="w-full border-t text-grey-darkest">


	<tr class="">
		<td class="p-2 bg-grey-lightest pr-2 w-32 pt-4" valign="top">
			<div class="uppercase text-sm">Fiscal Year</div>
		</td>
		<td class="p-2 flex">

			<input type="text" size="4" name="fiscal_year" value="{{ $errors->any() ? old('fiscal_year') : $program->fiscal_year }}" placeholder="i.e. 2020" class="p-2 border-b-2 font-bold" />

			<div class="ml-2 p-2">
				
				<label class="radio-inline"><input type="radio" {{ ($program->pilot) ? 'checked' : '' }} name="pilot" value="1"><i class="fas fa-paper-plane text-green mx-1"></i> PILOT</label>
				
				<label class="radio-inline"><input type="radio" {{ (!$program->pilot) ? 'checked' : '' }} name="pilot" value="0">Non-PILOT</label>

			</div>

		</td>
	</tr>

	<tr class="">
		<td class="p-2 bg-grey-lightest pr-2 pt-4" valign="top">
			<div class="uppercase text-sm">Name</div>
		</td>
		<td class="p-2">
			<input type="text" name="program_name" value="{{ $errors->any() ? old('name') : $program->program_name }}" placeholder="Name of Program" class="p-2 border-b-2 w-3/4 font-bold" />
		</td>
	</tr>

	<tr class="">
		<td class="p-2 bg-grey-lightest pr-2 pt-4" valign="top">
			<div class="uppercase text-sm">Description</div>
		</td>
		<td class="p-2">
			<textarea name="program_description" placeholder="Description" class="p-2 border-b-2 w-full" rows="8">{{ $errors->any() ? old('program_description') : $program->program_description }}</textarea>
		</td>
	</tr>

	

	<tr class="">
		<td class="p-2 bg-grey-lightest pr-2 pt-4" valign="top">
			<div class="uppercase text-sm">Value</div>
		</td>
		<td class="p-2 flex">

			<div class="p-2">
				$
			</div>
			
			<input type="text" name="value" placeholder="0.00" class="p-2 border-b-2 w-1/5" value="{{ $errors->any() ? old('value') : number_format($program->value) }}" />

			<select class="form-control w-1/5 ml-2" name="value_type">
				<option value="Cash">Cash</option>
				<option value="In Kind">In Kind</option>
				<option value="Both">Both</option>
			</select>

		</td>
	</tr>

	<tr class="">
		<td class="p-2 bg-grey-lightest pr-2 pt-4" valign="top">
			<div class="uppercase text-sm">Time Frame</div>
		</td>
		<td class="p-2">
			<input type="text" name="time_frame" placeholder="Ongoing or One-time?" class="p-2 border-b-2 w-3/4" value="{{ $errors->any() ? old('time_frame') : $program->time_frame }}" />
		</td>
	</tr>

	<tr>

		<td colspan="2">

		
		<div class="w-full flex">

		<div class="w-1/3 truncate">

			<div class="p-2 bg-grey-lightest text-sm uppercase mt-2 pr-2 border-t border-l border-r">
				Beneficiaries
			</div>

			<div class="">

				<textarea rows="4" name="beneficiaries" placeholder="" class="p-2 border w-full">{{ $errors->any() ? old('beneficiaries') : $program->beneficiaries }}</textarea>

			</div>

			@if(Auth::user()->permissions->developer)
				<div class="-mt-1">
					@livewire('connector-community-benefits', ['community_benefit' => $program,
															   'link_as' => 'beneficiary'],
															   key('beneficiary'))
				</div>
			@endif

		</div>

		<div class="w-1/3 truncate mx-2">

			<div class="p-2 bg-grey-lightest text-sm uppercase mt-2 pr-2 border-t border-l border-r">
				Initiators
			</div>

			<div class="">

				<textarea rows="4" name="initiators" placeholder="" class="p-2 border w-full">{{ $errors->any() ? old('initiators') : $program->initiators }}</textarea>

			</div>

			@if(Auth::user()->permissions->developer)
				<div class="-mt-1">
					@livewire('connector-community-benefits', ['community_benefit' => $program,
															   'link_as' => 'initiator'],
															   key('initiator'))
				</div>
			@endif


		</div>

		<div class="w-1/3 truncate">

			<div class="p-2 bg-grey-lightest text-sm uppercase mt-2 pr-2 border-t border-l border-r">
				Partners
			</div>

			<div class="">

				<textarea rows="4" name="partners" placeholder="" class="p-2 border w-full">{{ $errors->any() ? old('partners') : $program->partners }}</textarea>

			</div>

			@if(Auth::user()->permissions->developer)
				<div class="-mt-1">
					@livewire('connector-community-benefits', ['community_benefit' => $program,
															   'link_as' => 'partner'],
															   key('partner'))
				</div>
			@endif

		</div>


		</div>

		</td>

	</tr>

	</table>

</form>

<div class="text-right text-sm mt-12">
	<button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg p-2 text-red text-center ml-2"/>
	<i class="fas fa-exclamation-triangle mr-2"></i> Delete this Program
	</button>
</div>

<!---------------------------- MODALS ---------------------------->



<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
		</div>
		<div class="modal-body">
		  <div class="text-lg text-left text-red font-bold">
		    Are you sure you want to delete this Community Benefit Program?
		  </div>
		  <div class="text-left font-bold py-2 text-base">

		  </div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
		  <a href="/{{ Auth::user()->team->app_type }}/community-benefits/{{ $program->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
		</div>
	</div>
</div>


@endsection



@section('javascript')

	@livewireScripts

@endsection