@extends('admin.base')

@section('title')
    Queries
@endsection

@section('breadcrumb')


@endsection

@section('style')


@endsection

@section('main')

<div class="text-2xl mb-2 border-b-2 pb-2">
	<div class="">
		<span class="font-thin text-red">Lorde</span><span class="font-medium">Query</span>
	</div>

	<div class="text-grey-darker text-sm italic">
		Improve your database performance by finding what indexes might help.
	</div>

</div>

<div class="flex mb-4 text-sm">

	<div class="px-2 py-1 border-4 mr-4

			   ">
		<a href="?mode=">Unindexed (Type=All)</a>
	</div>

	<div class="bg-white px-2 py-1 border-4 mr-4">
		<a href="?mode=slow">Slow (Time > 200ms)</a>
	</div>

	<div class="bg-white px-2 py-1 border-4 mr-4">
		<a href="?mode=everything">Every Query</a>
	</div>

	<div class="bg-white px-2 py-1 border-4 mr-4">
		<a href="?mode=everything">Clear the Log</a>
	</div>

	<div class="bg-white px-2 py-1 border-4 mr-4">
		<a href="?mode=everything">DB Performance History</a>
	</div>

</div>

<div class="py-2 mb-4 border-4 border-red px-4 py-2 shadow">
	MySQL used an "all" type strategy for the following queries, meaning it searched through all rows until it found what it was looking for. Add indexes to improve performance.
</div>

<div class="table text-xs border-t border-l font-mono">

	<div class="table-row py-1 mb-1">

		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">SQL Hash</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Num Queries</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Type</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Rows</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Possible Keys</div>		
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Key</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Avg Time</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Tables</div>
		<div class="table-cell p-1 border-b-2 border-r bg-grey-lighter">Suggestions</div>

	</div>


	@foreach($queries as $hash => $sqls)

		<div class="table-row py-1 mb-1">

			<div class="table-cell p-1 border-b border-r">
				<div x-data="{ open: false }">
					<div @click="open=!open"
						 class="cursor-pointer text-blue">
						{{ $hash }}
					</div>
					<div x-show="open"
						 x-cloak>
						{{ $sqls->first()->sql }}
					</div>
				</div>

			</div>

			<div class="table-cell p-1 border-b border-r text-red">
				--
			</div>

			<div class="table-cell p-1 border-b border-r mx-2 pr-2 text-right
			">
				@if(!empty(json_decode($sqls->first()->explain)))
					{{ json_decode($sqls->first()->explain)[0]->type }}
				@endif
			</div>

			<div class="table-cell p-1 border-b border-r mx-2 pr-2 text-right
			">
				@if(!empty(json_decode($sqls->first()->explain)))
					{{ json_decode($sqls->first()->explain)[0]->rows }}
				@endif

			</div>

			<div class="table-cell p-1 border-b border-r mx-2 pr-2 text-xs
			">

				@if(!empty(json_decode($sqls->first()->explain)))
					@foreach(explode(',', json_decode($sqls->first()->explain)[0]->possible_keys) as $key)
						<div>
							{{ $key }}
						</div>
					@endforeach
				@endif

			</div>
			
			<div class="table-cell p-1 border-b border-r mx-2 pr-2 text-xs
			">

				@if(!empty(json_decode($sqls->first()->explain)))
					@foreach(explode(',', json_decode($sqls->first()->explain)[0]->key) as $key)
						<div>
							{{ $key }}
						</div>
					@endforeach
				@endif

			</div>



			<div class="table-cell p-1 border-b border-r mx-2 pr-2 text-right
			">{{ number_format($sqls->average('time'),2) }}</div>

			<div class="table-cell p-1 border-b border-r text-blue">
				@foreach(json_decode($sqls->first()->tables) as $table)
					<div>
						{{ $table }}
					</div>
				@endforeach
			</div>

			<div class="table-cell p-1 border-b border-r text-red">
				--
			</div>

			

		</div>

	@endforeach

</div>

@endsection