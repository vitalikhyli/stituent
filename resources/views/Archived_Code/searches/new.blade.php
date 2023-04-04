@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Edit Search
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Edit Searches', 'reports_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')


<form action="{{dir}}/search/save" method="post">

	@csrf

	<div class="border-b-4 border-blue pb-2 w-full text-2xl font-sans">

			<button type="submit" class="text-base mr-2 float-right bg-blue text-white rounded-lg px-4 py-2">
				Save
			</button>

			<a href="{{dir}}/search/">
			<button type="button" class="text-base mr-2 float-right bg-grey-darker text-white rounded-lg px-4 py-2">
				Cancel
			</button>
			</a>




			New Search

	</div>

	<table class="w-full border-b">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				Search Name:
			</td>
			<td class="p-2">
				<input type="text" name="name" placeholder="Search Name" class="w-full font-bold rounded-lg px-4 py-2 border" />
			</td>
		</tr>
	</table>


</form>


<br />
<br />
@endsection

@section('javascript')


@endsection
