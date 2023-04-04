<div class="text-center p-8 mx-auto">

					          


        Welcome back, <b>{{ Auth::user()->name }}!</b>
        <div class="mt-6">
            @if(Auth::user()->team->app_type == 'office')
                <a href="/home" class="hover:bg-blue-dark bg-blue text-grey-lighter hover:text-white no-underline border px-6 py-3 rounded-full text-sm tracking-wide">
                    Go To Dashboard
                </a>
            @endif
      	  @if(Auth::user()->team->app_type == 'campaign')
                <a href="/campaign" class="hover:bg-blue-dark bg-blue text-grey-lighter hover:text-white no-underline border px-6 py-3 rounded-full text-sm tracking-wide">
                    Go To Dashboard
                </a>
            @endif
            @if(Auth::user()->team->app_type == 'u')
                <a href="/u" class="hover:bg-blue-dark bg-blue text-grey-lighter hover:text-white no-underline border px-6 py-3 rounded-full text-sm tracking-wide">
                    Go To Dashboard
                </a>
            @endif
            
            <div class="mt-6">
                <a class="hover:text-blue no-underline border-transparent px-6 py-3 rounded-full text-xs tracking-wide" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>