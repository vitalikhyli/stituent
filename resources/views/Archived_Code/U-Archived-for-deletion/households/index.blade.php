@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Households
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Households', 'households_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		Households
	</div>

	<div class="flex float-right">

		<a class="mx-1" href="{{dir}}/households_linked">
		<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ ('/'.request()->path() ==dir.'/households_linked') ? 'bg-blue-darker text-white' : '' }}">
				@lang('Linked')
		</div>
		</a>

		<a class="mx-1" href="{{dir}}/households">
		<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ ('/'.request()->path() ==dir.'/households') ? 'bg-blue-darker text-white' : '' }}">
				@lang('All')
		</div>
		</a>

	</div>

	
</div>

<div class="mt-4 text-center pt-4">
	<input id="search" type="text" placeholder="&#xf002;    @lang('Address Lookup')" style="font-family:Font Awesome\ 5 Free, Arial" data-toggle="dropdown" class="w-1/2 appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 focus:font-bold text-lg" />
</div>

	@include('u.households.list-households')

<br />
<br />
@endsection


@section('javascript')
<script type="text/javascript">

$(document).ready(function() {

	$("#search").keyup(function(){
		getSearchData(this.value);
	});

	function getSearchData(v) {

		var mode = '{{ (isset($mode_all)) ? 'households_all' : 'households' }}';
// alert('{{dir}}/'+mode+'_search/'+v);
		$.get('{{dir}}/'+mode+'_search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}

	$("#search").focus();

});

</script>
@endsection