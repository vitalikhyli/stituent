<div class="flex float-right">

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/emails">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/emails') ? 'bg-blue-darker text-white' : '' }}">
			@lang('All Emails')
	</div>
	</a>

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/emails/queued">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/emails/queued') ? 'bg-blue-darker text-white' : '' }}">
			@lang('Queued') {{ ($queue_total) ? '('.$queue_total.')' : '' }}
	</div>
	</a>

	@if(Auth::user()->permissions->developer)
		<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/emails/queued-rows">
		<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/emails/queued-rows') ? 'bg-red-dark text-white' : '' }}">
				DEV: Queued Rows
		</div>
		</a>
	@endif

	<a class="mx-1 whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}/emails/completed">
	<div class="rounded-full px-4 py-2 text-grey-darker {{ (request()->path() == Auth::user()->team->app_type.'/emails/completed') ? 'bg-blue-darker text-white' : '' }}">
			@lang('Completed') {{ ($completed_total) ? '('.$completed_total.')' : '' }}
	</div>
	</a>

</div>