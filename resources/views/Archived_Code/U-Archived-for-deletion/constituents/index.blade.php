@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Constituents', 'constituents_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		 @lang('Constituents')

		<span class="capitalize text-grey-dark">
		@if (isset($category))
			- Category: {{ $category->name }}
		@endif

		@if (isset($group))
			- Group: {{ $group->name }}
		@endif
		</span>

	</div>

			<div class="flex float-right">

				<a class="mx-1" href="{{dir}}/constituents_linked">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (request()->path() =='office/constituents_linked') ? 'bg-blue-darker text-white' : '' }}">
						@lang('Linked')
				</div>
				</a>

				<a class="mx-1" href="{{dir}}/constituents">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (request()->path() =='office/constituents') ? 'bg-blue-darker text-white' : '' }}">
						@lang('All')
				</div>
				</a>

			</div>

</div>

<div class="flex w-full">
	<div class="w-2/3">

		<div class="mt-4 w-full">
			@include('office.constituents.list')
		</div>

	</div>
	<div class="w-1/3">

		<div class="text-center mt-4">
			<form autocomplete="off" autocomplete="false">
				<input type="hidden" autocomplete="false" />
				<input type="username" style="position:absolute; top:-50px;" />

				<input id="cfbar" 
					   type="text" 
					   placeholder="&#61447;    @lang('Name Lookup')" 
					   style="font-family:Font Awesome\ 5 Free, Arial" 
					   class="appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
			</form>

		</div>

	</div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {
		var mode = '{{ (isset($mode_all)) ? 'constituents_all' : 'constituents' }}';
		$('#list').css('opacity', '0.5');
		$.get('{{dir}}/'+mode+'_search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}

	$(document).ready(function() {

		$("#cfbar").focus();

		$('#cfbar').keyup(delay(function (e) {
		  getSearchData(this.value);
		}, 500));
		

	});


</script>
@endsection
