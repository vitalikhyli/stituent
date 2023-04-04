@extends('u.base')

@section('title')
    Add Community Benefit Program
@endsection

@section('breadcrumb')
    <a href="/u">Home</a> > <a href="/u/community-benefits">Community Benefits</a> > &nbsp;<b>Add Program</b>
@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')


<form method="POST" action="/u/community-benefits">

	{{ csrf_field() }}

<div class="text-2xl font-sans border-b-4 border-blue pb-2">



	<a href="/u/community-benefits">
		<button type="button" class="float-right bg-grey-dark text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark shadow">
			Cancel
		</button>
	</a>

		<button type="submit" class="float-right bg-blue text-white px-4 py-2 rounded-lg text-base ml-2 hover:bg-blue-dark shadow">
			Save
		</button>

	Add Program
</div>


	@include('elements.errors')


	<table class="w-full border-t text-grey-darkest">


	<tr class="border-b">
		<td class="p-2 bg-grey-lighter pr-2 w-1/5">
			Fiscal Year
		</td>
		<td class="p-2 flex">
			<input type="text" size="4" name="fiscal_year" value="{{ $errors->any() ? old('fiscal_year') : $fiscal_year }}" placeholder="i.e. 2020" class="rounded-lg p-2 border font-bold" />

			<div class="ml-2 p-2">
				
				<label class="radio-inline"><input type="radio" {{ ($mode == 'pilot') ? 'checked' : '' }} name="pilot" value="1"><i class="fas fa-paper-plane text-green mx-1"></i> PILOT</label>
				
				<label class="radio-inline"><input type="radio" {{ ($mode != 'pilot') ? 'checked' : '' }} name="pilot" value="0">Non-PILOT</label>

			</div>

		</td>
	</tr>

	<tr class="border-b">
		<td class="p-2 bg-grey-lighter pr-2 w-1/5">
			Name
		</td>
		<td class="p-2">
			<input type="text" name="program_name" value="{{ $errors->any() ? old('name') : '' }}" placeholder="Name of Program" class="rounded-lg p-2 border w-3/4 font-bold" />
		</td>
	</tr>

	</table>

</form>
	
@endsection



