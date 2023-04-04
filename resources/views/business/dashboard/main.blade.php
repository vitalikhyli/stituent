@extends('business.base')

@section('title')
    @lang('Home')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b>
@endsection

@section('style')

	@include('shared-features.calendar.style')

	<style>
		.dot {
		  height: 24px;
		  width: 24px;
		  border-radius: 50%;
		}
	</style>

@endsection



@section('main')


<div class="flex w-full">

	<div class="flex-1 pb-8 text-grey-dark w-1/2 mr-4">
		

		@include('business.dashboard.prospects')

	</div>


	<div class="flex-1 pb-8 text-grey-dark w-1/2 ml-4">
		

		@include('business.dashboard.goals')

		@include('business.dashboard.checkins')


	</div>

</div>




@endsection

@section('javascript')
		
	<script type="text/javascript">

		$(document).ready(function() {
			$("#cfbar").focus();

			$("#cfbar").focusout(function(){
				window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
			});
			
			$("#cfbar").keyup(function(){
				getSearchData(this.value);
			});

		});

		function getSearchData(v) {
			if (v == '') {
				$('#list').addClass('hidden');
			}
			$.get('/office/dashboard_search/'+v, function(response) {
				if (response == '') {
					$('#list').addClass('hidden');
				} else {
					$('#list').html(response);
					$('#list').removeClass('hidden');
				}
			});
		}
		
	</script>

@endsection
