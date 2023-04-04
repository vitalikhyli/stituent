@extends('u.base')

@section('title')
    Metrics : History
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('History', 'history_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2 text-2xl ">
		People in My Database
	</div>


    <div id="graph" class="overflow-x-scroll">
        <?php extract($graph_a, EXTR_OVERWRITE); ?>
        @include('elements.graph')
    </div>

    <div class="flex border-b mb-4 pb-2 text-2xl mt-8">
        Open Cases
    </div>


    <div id="graph" class="overflow-x-scroll">
        <?php extract($graph_b, EXTR_OVERWRITE); ?>
        @include('elements.graph')
    </div>



	@if(false)
	<table class="mt-8">
	@foreach(App\HistoryItem::where('team_id',Auth::user()->team->id)->orderBy('created_at', 'desc')->get() as $item)
		<tr>
			<td class="text-grey-dark p-1">{{ \Carbon\Carbon::parse($item->created_at)->format("m/j") }}</td>
			<td class="p-1"><div style="width:{{ $item->num_people *4 }}px;" class="text-sm text-right hover:bg-orange-dark cursor-pointer p-1 bg-blue rounded text-white">{{ $item->num_people }}</div></td>
		</tr>
	@endforeach
	</table>
	@endif

<br />
<br />
@endsection

@section('javascript')


@endsection
