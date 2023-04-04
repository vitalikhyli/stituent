            <!-- DROPDOWN -->
            <div class="hidden-sm hidden-md hidden-lg hidden-xl dropdown font-sans -mt-2 -mr-4">
              <button class="px-2 py-1 border-2 text-grey-light no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline font-sans rounded-full" type="button" data-toggle="dropdown">
                {{ Auth::user()->first_name }}
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right w-64 text-lg">
                <li><a href="/{{ Auth::user()->team->app_type }}/users/settings">My Profile</a></li>

                @if(Auth::user()->permissions->developer)
                <li class="divider"></li>
                <li>
                    <a href="/admin/acivity">
                        <i class="fa fa-key w-6 mr-2"></i> Admin Activity
                    </a>
                </li>
                @endif

                <li class="divider"></li>

                @foreach(Auth::user()->allteams as $theteam)
                    <li class="{{ (Auth::user()->team->id == $theteam->id) ? 'bg-grey-light' : '' }}">
                        <a href="/admin/change_team/{{ $theteam->id }}">
                            <i class="{{ $theteam->fa_logo() }} w-6 mr-2"></i>
                            {{ $theteam->short_name }}
                        </a>
                    </li>
                @endforeach

                
                <li class="divider"></li>
                <li>
                    <a class="" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>

              </ul>
            </div>
            <!-- / DROPDOWN -->