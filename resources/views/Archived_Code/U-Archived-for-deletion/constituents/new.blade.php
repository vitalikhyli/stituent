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

<form method="POST" id="contact_form" action="{{dir}}/constituents/save">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<div class="float-right text-base">

			<input type="submit" name="save" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
			
			<a href="{{ url()->previous() }}">
				<button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest text-white text-center ml-2"/>
					Cancel
				</button>
			</a>

		</div>


		<span class="text-2xl">
		<i class="fas fa-user-circle mr-2"></i>
		New Person
		</span>
	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				First Name
			</td>
			<td class="p-2">

				<input name="first_name" placeholder="First Name" value="{{ ucfirst($first_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>

			</td>
		</tr>
	
			<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				Last Name
			</td>
			<td class="p-2">


				<input name="last_name"  placeholder="Last Name" value="{{ ucfirst($last_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>


			</td>
		</tr>

	</table>



	<div class="flex mt-2">
	@foreach ($categories as $thecategory)
	  <div class="mr-4 p-2 cursor-pointer">

	    <div class="flex text-sm tracking-wide text-left uppercase pb-1 border-b-4 border-blue">
	        {{ $thecategory->name }}
	        ({{ $thecategory->groups->where('team_id',Auth::user()->team->id)->count() }})
	    </div>

	    <div class="mt-2 text-sm">
	    @foreach ($thecategory->groups->where('team_id',Auth::user()->team->id)->sortBy('name') as $thegroup)
	      <div class="w-full flex">
	           <input type="checkbox" name="group_{{ $thegroup->id }}" id="{{ $thegroup->id }}" />
	           <label class="ml-2 font-normal" for="{{ $thegroup->id }}">{{ $thegroup->name }}</label>
	      </div>
	    @endforeach
	    </div>

	  </div>
	  @endforeach
	</div>


</form>

<br /><br />


@endsection

@section('javascript')

@endsection