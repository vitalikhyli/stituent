@extends('campaign.base')

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')
    <a href="/u"> @lang('Home')</a> > &nbsp;<b> @lang('Constituents')</b>
@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex">
	<div class="text-3xl font-sans w-2/3">
		 @lang('Look Up Voters')

		<span class="capitalize text-grey-dark">
		@if (isset($category))
			- category: {{ $category->name }}
		@endif

		@if (isset($group))
			- tag: {{ $group->name }}
		@endif
		</span>

	</div>

	<div class="w-1/3 text-right">
		<div class="float-right">
			<ul class="nav nav-pills">
				<li class="nav-item ">
					<a class="mx-1" href="/campaign/constituents">
						@lang('Linked')
					</a>
				</li>
				<li class="nav-item">
					<a class="mx-1" href="/campaign/constituents_universe">
						@lang('Universe')
					</a>
				</li>
				<li class="nav-item">
					<a class="mx-1" href="/campaign/constituents_all">
						@lang('All')
					</a>
				</li>

			</ul>
		</div>
	</div>
</div>

<div class="mt-4 text-center border-t pt-4">
	<input id="search" type="text" placeholder="&#xf002;    @lang('Name Lookup')" style="font-family:Font Awesome\ 5 Free, Arial" data-toggle="dropdown" class="w-1/2 appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 focus:font-bold text-lg" />
</div>


	<div class="mt-4 w-full">
		@include('campaign.constituents.list')
	</div>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {

	$("#search").keyup(function(){
		getSearchData(this.value);

	});
	function getSearchData(v) {
		var mode = '{{ (isset($mode_all)) ? 'constituents_all' : 'constituents' }}';
		$.get('/campaign/'+mode+'_search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}
	$("#search").focus();
});
</script>
@endsection
