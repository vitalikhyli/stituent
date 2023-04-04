@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Open Cases
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Search Result', 'searchresult', 'level_1') !!}

@endsection

@section('style')

@endsection

@section('main')

<div class="border-b-4 border-blue mb-4 pb-2 w-full text-2xl">

		<a href="{{dir}}/search/{{ $search->id }}/edit">
			<button type="button" class="text-base mr-2 float-right bg-blue text-white rounded-lg px-4 py-2">
				Back to "{{ $search->name }}"
			</button>
		</a>

		Result of Search "{{ $search->name }}"
</div>


@if(isset($output) &&($output->count() >0))

	@if($output instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<nav class="-mt-2 aria-label="Page navigation example">
			{{ $output->appends(request()->except('page'))->links() }} 
		</nav>
	@endif
	
	<table class="w-full text-grey-darkest cursor-pointer">
		@foreach($output as $thevoter)
		<tr class="clickable border-b hover:bg-orange-lightest" data-href="{{dir}}/constituents/{{$thevoter->id}}">
			<td class="p-1">
				{{ $thevoter->last_name}}
			</td>
			<td class="p-1">
				{{ $thevoter->first_name}}
			</td>
			<td class="p-1">
				{{ $thevoter->address_number}} {{ $thevoter->address_street}} {{ $thevoter->address_aptno}}
			</td>
			<td class="p-1">
				{{ $thevoter->address_city}}, {{ $thevoter->address_state }} {{ substr($thevoter->address_zip,0,5)}}
			</td>
		</tr>
		@endforeach
	</table>

@endif

<br />
<br />
@endsection

@section('javascript')

<script type="text/javascript">
	
    $(".clickable").click(function() {
        window.location = $(this).data("href");
    });

</script>


@endsection
