<div class="flex whitespace-no-wrap">
    <a class="px-3 py-2 mx-2 rounded-full {{ (request()->path() == Auth::user()->team->app_type.'/maps') ? 'bg-blue text-white hover:text-white hover:bg-blue-dark' : '' }}" href="/{{ Auth::user()->team->app_type }}/maps">
        All Activity
    </a>
    <a class="px-3 py-2 mx-2 rounded-full {{ (request()->path() == Auth::user()->team->app_type.'/maps/voters') ? 'bg-blue text-white hover:text-white hover:bg-blue-dark' : '' }}" href="/{{ Auth::user()->team->app_type }}/maps/voters">
        All Voters
    </a>
    <!-- <a class="px-3 py-2 mx-2 rounded-full {{ (request()->path() == Auth::user()->team->app_type.'/maps/groups') ? 'bg-blue text-white hover:text-white hover:bg-blue-dark' : '' }}" href="/{{ Auth::user()->team->app_type }}/maps/groups">
        By Group
    </a> -->
  </div>