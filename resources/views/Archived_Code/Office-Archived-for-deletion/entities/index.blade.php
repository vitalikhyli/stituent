@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    @lang('Organizations')
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Organizations', 'organizations_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		 Organizations
	</div>


</div>

<div class="mt-4 text-center pt-4">
<form autocomplete="off" autocomplete="false">
	<input type="hidden" autocomplete="false" />
	<input type="username" style="position:absolute; top:-50px;" />

	<input id="cfbar" 
		   type="text" 
		   placeholder="&#xf002;    @lang('Name Lookup')" 
		   style="font-family:Font Awesome\ 5 Free, Arial" 
		   class="w-1/2 appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
</form>

</div>


	<div class="mt-4 w-full">
		@include('office.entities.list')
	</div>

@endsection

@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {
		$.get('{{dir}}/entities/search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}

	$(document).ready(function() {

		$("#cfbar").focus();
		
		$("#cfbar").keyup(function(){
			getSearchData(this.value);
		});

	});


</script>
@endsection
