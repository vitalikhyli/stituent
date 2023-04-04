            <!-- DROPDOWN -->
            <div class="hidden-xs dropdown font-sans">
              <button class="px-6 py-3 border-2 text-grey-light no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline font-sans rounded-full" type="button" data-toggle="dropdown">
                {{ Auth::user()->name }}
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right w-64 text-lg text-base">

                @if (Auth::user()->permissions->admin)
                    @if (Auth::user()->team->account->paid_through_date)
                    <li>
                        @if (Auth::user()->team->account->paid_through_date > \Carbon\Carbon::today())
                            <div class="px-2 text-sm text-center text-green">
                                Your account is paid through 
                                <b>{{ Auth::user()->team->account->paid_through_date->format('n/j/Y') }}</b>
                                ({{ Auth::user()->team->account->paid_through_date->diffForHumans() }})
                            </div>
                        @else
                            <div class="px-2 text-sm text-center">
                                You have an outstanding invoice 
                                <b>{{ Auth::user()->team->account->paid_through_date->format('n/j/Y') }}</b>
                                ({{ Auth::user()->team->account->paid_through_date->diffForHumans() }})
                            </div>
                        @endif
                    </li>
                    @endif

                    
                    <a href="/stripe/payment-options" class="hover:text-white text-sm hover:bg-blue-dark m-2 bg-blue text-white rounded-full px-3 py-2" type="submit">Pay Online</a>
                @endif

                <li><a href="/{{ Auth::user()->team->app_type }}/users/settings">My Profile</a></li>

                @if(Auth::user()->permissions->developer)
                <li class="divider"></li>
                <li class="text-base">
                    <a href="/admin/home">
                        <i class="fa fa-key w-6 mr-2"></i> Admin
                    </a>
                </li>
                @endif

                <li class="divider"></li>

                @foreach(Auth::user()->allteams as $theteam)
                    <li class="{{ (Auth::user()->team->id == $theteam->id) ? 'bg-grey-light' : '' }} text-base">
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