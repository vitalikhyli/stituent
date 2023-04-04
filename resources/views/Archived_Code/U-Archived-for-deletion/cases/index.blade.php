@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Cases
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb("Cases", 'cases_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Cases <span class="text-grey-dark">&amp;</span> Files
			</div>
		</div>
		<div class="w-1/2">
			<div class="float-right">

				<?php 
					function urlLook($u, $a, $b) {
						return (!empty(strpos(url()->current(),$u))) ? $a : $b;
					}
				?>

				<div class="flex text-sm">

					<div class="border-r-2 pr-2 pl-2 flex">
						<a class="mx-1" href="{{dir}}/cases/everything/everything">
						<div class="flex-1 flex-initial rounded-lg whitespace-no-wrap px-2 py-1 text-grey-darker {{ urlLook('/everything/everything','bg-blue-darker text-white','') }}">
								All Cases
						</div>
						</a>
					</div>

					<div class="border-r-2 flex pl-2 pr-2">

						<a class="mx-1" href="{{dir}}/cases{{ urlLook('/mine','/mine','/all') }}/open">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/open','bg-blue-darker text-white','') }}">
								Open
						</div>
						</a>

						<a class="mx-1" href="{{dir}}/cases{{ urlLook('/mine','/mine','/all') }}/resolved">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/resolved','bg-blue-darker text-white','') }}">
								Resolved
						</div>
						</a>

					</div>

					<div class="flex pl-2">

						<a class="mx-1" href="{{dir}}/cases/all{{ urlLook('/open','/open','/resolved') }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/all','bg-blue-darker text-white','') }}">
								Team
						</div>
						</a>

						<a class="mx-1" href="{{dir}}/cases/mine{{ urlLook('/open','/open','/resolved') }}">
						<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/mine','bg-blue-darker text-white','') }}">
								Mine
						</div>
						</a>

					</div>

				</div>

			</div>
		</div>
	</div>


	@if($cases->count() <= 0)

		<div class="p-4 font-bold w-full">No cases to show.</div>

	@else

	<div class="text-center pb-4">
	<form autocomplete="off">
	<input id="search" autocomplete="off" type="text" placeholder="&#xf002;    @lang('Search Cases &amp; Files')" style="font-family:Font Awesome\ 5 Free, Arial" data-toggle="dropdown" class="w-1/2 appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
	</div>
	</form>




	<div id="list" class="mt-4 mb-4 w-full">
		@include('university.cases.list-cases')
	</div>

	@endif



<br />
<br />





	</div>


@endsection

@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {

		if (v.trim() == '') { $v = 'ryan'; }

		var scope = '{{ urlLook('/mine','/mine','/all') }}';
		var resolved = '{{ urlLook('/open','/open','/resolved') }}';

		$.get('{{dir}}/cases_search'+scope+resolved+'/'+v, function(response) {
			$('#list').html(response);
		});
	}


	$(document).ready(function() {

		$("#search").focus();

		$("#search").keyup(function(){
			getSearchData(this.value);
		});

		$("#search").blur(function(){
			getSearchData(this.value);
		});

	});

</script>
@endsection
