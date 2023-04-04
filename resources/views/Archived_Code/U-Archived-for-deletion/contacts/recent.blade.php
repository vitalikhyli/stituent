@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Open Cases
@endsection

@section('breadcrumb')


    {!! Auth::user()->Breadcrumb('Recent Contacts', 'recent_index', 'level_1') !!}


@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Notes, Contacts and Call-Logs
			</div>
		</div>
	</div>

@if($contacts->count() <= 0)

	No follow ups right now!

@else

@if($contacts instanceof \Illuminate\Pagination\LengthAwarePaginator)
	<div class="float-right">
		{{ $contacts->links() }}
	</div>
@endif

<table class="w-full">
	<tr class="border-b border-grey-dark bg-grey-lighter font-semibold uppercase text-sm">

		<td class="p-2 w-1/6">
			Date
		</td>
		<td class="p-2">
			Connected To
		</td>
		<td class="p-2">
			Notes
		</td>

	</tr>

@foreach($contacts as $item)
	<tr class="border-b hover:bg-orange-lightest cursor-pointer">


		<td class="p-2">
			{{ \Carbon\Carbon::parse($item->created_at)->format("D, M j Y") }}
		</td>

		<td class="p-2 w-1/3">
			@if($item->case_id)
					<a href="{{dir}}/cases/{{ $item->case_id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black uppercase">
						<i class="far fa-folder-open mr-2"></i>
						{{ substr(\App\WorkCase::find($item->case_id)->subject,0,30) }}
					</button>
					</a>
			@else
				@foreach($item->people as $theperson)
					<a href="{{dir}}/constituents/{{ $theperson->id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black">
						<i class="far fa-user mr-2"></i>
						{{ $theperson->full_name }}
					</button>
					</a>
				@endforeach
			@endif
		</td>

		<td class="p-2 w-2/3">
			@if($item->subject)
				<div class="font-semibold">{{ $item->subject }}</div>
			@endif
				{{ $item->notes }}
		</td>
	</tr>
@endforeach
</table>

@endif

<br />
<br />
@endsection

@section('javascript')


@endsection
