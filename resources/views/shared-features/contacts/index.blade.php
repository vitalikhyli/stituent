@extends(Auth::user()->team->app_type.'.base')

@section('title')
    All Notes
@endsection

@section('breadcrumb')


    <a href="/{{ Auth::user()->team->app_type }}">Home</a>
    > All Notes


@endsection

@section('style')

	<style>

		.pagination {
			margin: 0px;
		}
		.pagination>li>a, 
		.pagination>li>span {
			border: 0;
		}

	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-1/3">
			<div class="text-2xl font-sans">
				All @lang('Constituent') Notes
			</div>
		</div>
		<div class="w-1/3">
			
		</div>
	</div>

	<div class="text-center my-8">
		<form action="" method="GET">
			
			<input required class="border px-4 py-2 rounded-l-full" type="text" name="keyword" placeholder="Search within all notes" value="{{ request('keyword') }}" />
			<button type="submit" class="bg-blue hover:bg-blue-dark text-white px-4 py-2 rounded-r-full">
				Search
			</button> 
			@if (request('keyword'))
				<a class="text-sm text-red-light ml-4" href="/{{ Auth::user()->team->app_type }}/contacts">
					Clear
				</a>
			@endif
		</form>
		
	</div>

@if(!$contacts->first())

	<div class="text-grey-dark">
		No notes yet.
	</div>

@else

<div class="w-full mb-4">

	@if($contacts instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<div class="w-full flex text-grey-dark">
			<div class="w-1/4 text-left p-2">
				Showing <span class="font-bold text-black">{{ $contacts->firstItem() }}-{{ $contacts->lastItem() }}</span> of 
				<span class="font-bold text-black">{{ $contacts->total() }}</span>
			</div>
			<div class="w-1/2 text-center">
				{{ $contacts->links() }}
			</div>
			<div class="w-1/4 text-right p-2">
				Page <span class="text-black font-bold">{{ $contacts->currentPage() }}</span> of <span class="text-black font-bold">{{ $contacts->lastPage() }}</span>
			</div>
		</div>
	@endif
</div>

<table class="w-full">
	<tr class="border-b border-grey-dark bg-grey-lighter font-semibold uppercase text-sm">
		<td></td>
		<td></td>
		<td class="py-2 w-1/6">
			Date
		</td>
		<td class="py-2 w-1/6">
			User
		</td>
		<td class="py-2">
			Connected To
		</td>
		<td class="py-2 w-1/2">
			Notes
		</td>

	</tr>

@foreach($contacts as $item)
	<tr class="border-b hover:bg-orange-lightest cursor-pointer text-sm align-top">

		<td class="w-1 p-1 text-grey">
			{{ $loop->index + $contacts->firstItem() }}.
		</td>

		<td class="w-1 p-1 text-grey">
			<a href="/{{ Auth::user()->team->app_type }}/contacts/{{ $item->id }}/edit">
				<button class="rounded-lg bg-blue text-xs uppercase text-white px-4 py-1 mr-2">Edit</button>
			</a>
		</td>

		<td class="p-1 text-grey-dark whitespace-no-wrap">
			{{ \Carbon\Carbon::parse($item->created_at)->format("D, M j Y") }}
		</td>

		<td class="p-1 text-grey-dark inline-flex">

			<div class="w-6">
				@if ($item->private)
					<i class="w-6 fa fa-lock text-blue mr-1" alt="This note is private."></i>
				@endif
			</div> 

			{{ $item->user->username }}
		</td>

		<td class="p-1 whitespace-no-wrap">
			@if($item->case)
				<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $item->case->id }}">
				<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-lg m-1 px-2 py-1 text-sm text-blue">
					{{ $item->case->shortened_subject }}
					<i class="fa fa-folder-open ml-2"></i>
				</button>
				</a>
			@else
				@foreach($item->people as $theperson)
					<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 pr-3 py-1 text-sm text-grey-darkest">
						<i class="fa fa-user mr-2"></i>
						{{ $theperson->full_name }}
					</button>
					</a>
				@endforeach
			@endif
		</td>

		<td class="p-1 w-1/2">
			@if($item->subject)
				<span class="font-semibold">{{ $item->subject }} > </span>
			@endif
				{{ $item->notes }}
		</td>
	</tr>
@endforeach
</table>

<div class="w-full mt-8">
	@if($contacts instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<div class="text-center">
			{{ $contacts->links() }}
		</div>
	@endif
</div>

@endif

<br />
<br />
@endsection

@section('javascript')


@endsection
