@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Organizations')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	Organizations

@endsection

@section('style')

	<style>

		table.tight > tbody > tr > td {
			padding: 2px 8px;
		}
	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		<a href="/{{ Auth::user()->team->app_type }}/organizations/new/" class="float-right text-base text-white rounded-full bg-blue px-4 py-2 hover:text-white hover:bg-blue-dark">
			Add Organization
		</a>
		 <b>{{ $entities->count() }} Organizations</b><span class="text-xl">
	</div>


</div>


<div class="mt-4 pt-4 flex">
	<div class="w-2/3 text-center">

		<div class="p-6 text-left">
			Here you can view all organizations and relationships with {{ Auth::user()->team->name }}. 

		</div>

		<!-- <form autocomplete="off" autocomplete="false">
			<input type="hidden" autocomplete="false" />
			<input type="username" style="position:absolute; top:-50px;" />

			<input id="cfbar" 
				   type="text" 
				   placeholder="@lang('Name Lookup')" 
				   style="font-family:Font Awesome\ 5 Free, Arial" 
				   class="w-1/2 appearance-none rounded-full px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />
		</form> -->
	</div>
	<div class="w-1/3">
		<table class="table tight bg-grey-lightest text-sm border-2">
			@foreach ($entities->groupBy('type') as $typename => $summary_entities)
				<tr>
					<td>
						@if ($typename)
							{{ $typename }}
						@else
							<i>No Type</i>
						@endif
					</td>
					<td class="text-right font-bold">{{ $summary_entities->count() }}</td>
				</tr>
			@endforeach
		</table>

	</div>


</div>


	<div class="mt-4 w-full text-grey-darker">
		@include('shared-features.entities.list')
	</div>

@endsection

@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {
		$.get('/{{ Auth::user()->team->app_type }}/entities/search/'+v, function(response) {
			$('#list').replaceWith(response);
		});
	}

	$(document).ready(function() {

		$("#cfbar").focus();
		
		$("#cfbar").keyup(function(){
			getSearchData(this.value);
		});

		$(document).on('change', '.edit-type', function() {
			var form = $(this).closest('form');
			form.submit();
		});

		$(document).on('click', '.new-type-show', function() {
			var form = $(this).closest('form');
			form.find('.new-type').show();
			form.find('.edit-type').hide();
			form.find('.new-type-show').hide();
			form.find('.new-type input').focus();
			//alert();
		});
		$(document).on('click', '.new-type-hide', function() {
			var form = $(this).closest('form');
			form.find('.new-type').hide();
			form.find('.edit-type').show();
			form.find('.new-type-show').show();
			//alert();
		});


	});


</script>
@endsection
