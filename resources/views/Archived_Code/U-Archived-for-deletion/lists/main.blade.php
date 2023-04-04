@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Searches
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Searches', 'reports_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">
			<div class="text-2xl font-sans">

	<a href="{{dir}}/search/new">
	<button type="submit" formaction="{{dir}}/search/new" class="rounded-lg bg-blue text-white px-4 py-2 text-base ml-4 float-right">
		New Search
	</button>
	</a>

				Saved Searches
			</div>
		</div>
	</div>

	
@if(isset($list_options) &&($list_options->count() >0))

<table class="w-full cursor-pointer">

	@foreach($list_options as $thelist)
	<tr class="border-b hover:bg-orange-lightest clickable" data-href="{{dir}}/search/{{ $thelist->id }}/edit">

		<td class="p-2 w-24" valign="top">
            <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                Edit
            </button>
		</td>

		<td class="p-2" valign="top">
			{{ $thelist->name}}
		</td>

	</tr>
	@endforeach

</table>
@endif


@if(isset($list_options_archived) &&($list_options_archived->count() >0))

<div class="border-b-4 border-red mt-8">Archived</div>

<table class="w-full cursor-pointer">

	@foreach($list_options_archived as $thelist)
	<tr class="border-b hover:bg-orange-lightest clickable" data-href="{{dir}}/search/{{ $thelist->id }}/edit">

		<td class="p-2 w-24" valign="top">
            <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                Edit
            </button>
		</td>

		<td class="p-2" valign="top">
			{{ $thelist->name}}
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
