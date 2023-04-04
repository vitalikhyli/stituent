<div class="flex float-right text-base">

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/groups">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/groups') ? 'bg-blue-darker text-white' : '' }}">
			Current  {{ (isset($current_total)) ? '('.$current_total.')' : '' }}
	</div>
	</a>

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/groups/archived">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/groups/archived') ? 'bg-blue-darker text-white' : '' }}">
			Archived {{ (isset($archived_total)) ? '('.$archived_total.')' : '' }}
	</div>
	</a>


</div>