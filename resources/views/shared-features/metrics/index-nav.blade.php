<div class="flex float-right text-base">

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/metrics/engagement">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/metrics/engagement') ? 'bg-blue-darker text-white' : '' }}">
			Constituents  {{ (isset($current_total)) ? '('.$current_total.')' : '' }}
	</div>
	</a>

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/metrics/cases">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/metrics/cases') ? 'bg-blue-darker text-white' : '' }}">
			Cases {{ (isset($archived_total)) ? '('.$archived_total.')' : '' }}
	</div>
	</a>

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/metrics/contacts">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/metrics/contacts') ? 'bg-blue-darker text-white' : '' }}">
			Contacts {{ (isset($archived_total)) ? '('.$archived_total.')' : '' }}
	</div>
	</a>

</div>