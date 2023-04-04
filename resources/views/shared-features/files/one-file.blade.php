
<div class="table-row hover:bg-orange-lightest w-full">
	<input type="checkbox" class="file-select hidden mr-2" value="{{ $thefile->id }}" />

	<div class="table-cell py-1 w-10 pr-2">
		<a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/edit/{{ $return_string }}">
			<button class="rounded-lg bg-grey-lighter text-xs text-black px-3 py-1">
				Edit
			</button>
		</a>
	</div>

<!-- 	<div class="table-cell py-1 w-10">
		<i class="w-6 fa fa-file text-xl mr-2 text-center text-grey"></i>
	</div> -->

	<div class="table-cell py-1 text-sm w-40 uppercase text-sm text-gray-400">
		<i class="fa fa-folder"></i> {{ $thefile->folder_name }}
	</div>

	<div class="table-cell py-1 text-sm w-1/2">

		<a target="_blank" href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/download" target="new">
		@if(isset($search_v))

			{!! preg_replace("/".preg_quote($search_v)."/i", '<b class="bg-orange-lighter">$0</b>', $thefile->name ) !!}

		@else

			{{ $thefile->name }}

		@endif
		</a>
		<div class="text-gray-500">
			{{ $thefile->description }}
		</div>

		
	</div>
	<div class="table-cell py-1 text-sm w-1/6">
		{{ $thefile->created_at->format('n/j/Y') }}
	</div>

	

	<div class="table-cell py-1 text-sm">
		@foreach ($thefile->cases as $case)
			<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">
				<i class="fa fa-folder"></i> {{ $case->subject }}
			</a>
		@endforeach
		@foreach ($thefile->groups as $group)
			<a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}">
				<i class="fa fa-tag"></i> {{ $group->name }}
			</a>
		@endforeach
	</div>

</div>