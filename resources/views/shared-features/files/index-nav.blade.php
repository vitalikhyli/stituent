<div class="flex float-right text-base">

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/files/list/all">
	<div class="rounded-lg text-sm px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/files/list/all') ? 'bg-blue-darker text-white' : '' }}">
			All 
	</div>
	</a>

	@if (Auth::user()->app_type == 'office')
		<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/files/list/cases">
		<div class="rounded-lg text-sm px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/files/list/cases') ? 'bg-blue-darker text-white' : '' }}">
				By Case
		</div>
		</a>

		<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/files/list/groups">
		<div class="rounded-lg text-sm px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/files/list/groups') ? 'bg-blue-darker text-white' : '' }}">
				By Group
		</div>
		</a>

		<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/files/list/constituents">
		<div class="rounded-lg text-sm px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/files/list/constituents') ? 'bg-blue-darker text-white' : '' }}">
				By Constituent
		</div>
		</a>
	@endif

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/files/list/directories">
	<div class="rounded-lg text-sm px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/files/list/directories') ? 'bg-blue-darker text-white' : '' }}">
			By Folder
	</div>
	</a>
</div>

