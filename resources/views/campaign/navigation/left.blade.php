
<div id="left-nav" class="w-1/5 items-center text-lg font-sans">


    <ul class="list-reset pr-8 w-full text-base">

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign">
                <i class="w-6 text-center fa fa-home text-xl mr-2"></i> @lang('Campaign HQ')
            </a>
        </li>

    </ul>

        <div class="text-white text-base leading-tight mr-8 mt-4">

            @if(Auth::user()->campaign_current())

                <div class="flex">

                    @php

                        $days =\Carbon\Carbon::now()->startOfDay()->diffInDays(Auth::user()->campaign_current()->election_day);

                    @endphp

                    @if($days == 0)

                        <div class="font-sans px-3 py-2 text-grey-darker font-bold border-t-2 border-transparent whitespace-no-wrap">Election Day is Here!</div>

                    @elseif(\Carbon\Carbon::parse(Auth::user()->campaign_current()->election_day)->isFuture())

                        @foreach(str_split($days) as $char)
                            <div class="font-mono font-bold text-blue border-t-4 border-l border-grey-dark px-2 py-2 bg-white shadow-lg">{{ $char }}</div>
                        @endforeach

                        <div class="font-sans px-3 py-2 text-grey-darker font-bold border-t-2 border-transparent whitespace-no-wrap">{{ Illuminate\Support\Str::plural('Day', $days) }} Remaining</div>

                    @endif

                </div>

            @else

                No active campaign

            @endif

        </div>

        <ul class="list-reset pr-8 w-full text-base mt-4">

        @if(Auth::user()->permissions->developer)

            <!-- <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/ostrich/login-user">
                    <i class="w-6 text-center fas fa-mobile-alt text-xl mr-2"></i> Campaign App
                    <span class="float-right px-1 bg-yellow-lighter text-xs border">New</span>
                </a>
            </li>

            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/volunteers-new">
                    <i class="w-6 text-center fas fa-hands-helping text-xl mr-2"></i> Volunteers
                    <span class="float-right px-1 bg-yellow-lighter text-xs border">New</span>
                </a>
            </li>

            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/opportunities">
                    <i class="w-6 text-center fas fa-walking text-xl mr-2"></i> Opportunities
                    <span class="float-right px-1 bg-yellow-lighter text-xs border">New</span>
                </a>
            </li> -->

        @endif

        <!--====================================================================-->

        <div class="uppercase mb-4 text-base no-underline text-grey-darkest pb-1 border-b border-grey">
            
        </div>

<!--         <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/participants">
                <i class="w-6 text-center fa fa-users text-xl mr-2"></i> @lang('Participants')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Participant::thisTeam()->first())
                        {{ number_format(\App\Participant::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>

            </a>
        </li> -->

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/voters">
                <!-- <i class="w-6 text-center fas fa-vote-yea text-xl mr-2"></i>  -->
                <i class="w-6 text-center fa fa-users text-xl mr-2"></i> @lang('Voters')

                <div class="float-right text-xs text-blue pt-1">
                    {{ number_format(Auth::user()->team->unarchived_count) }}
                </div>

            </a>
        </li>


       <!--  <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/link-voters">
                <i class="w-6 fas fa-link text-xl mr-2 text-center"></i> @lang('Link Voters')

                <div class="text-xs float-right pt-1">
                    {{ number_format(\App\Participant::where('team_id', Auth::user()->team->id)->whereNull('voter_id')->count()) }}
                </div>

            </a>
        </li> -->


        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/lists">
                <i class="w-6 text-center fa fa-list text-xl mr-2"></i> @lang('Lists')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\CampaignList::thisTeam()->first())
                        {{ number_format(\App\CampaignList::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>


            </a>
        </li>


        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/volunteers">
                <i class="w-6 text-center fas fa-hands-helping text-xl mr-2"></i> @lang('Volunteers')

                <div class="float-right text-xs text-blue pt-1">

                    {{ number_format(\App\Participant::thisTeam()->volunteers()->count(),0,',','.') }}
 

                </div>

            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/mapping">
                <i class="w-6 text-center fas fa-map-pin text-xl mr-2"></i> @lang('Lawn Signs')

                <div class="float-right text-xs text-blue pt-1">
                    {{ number_format(\App\Participant::thisTeam()->volunteers(['volunteer_lawnsign'])->count(),0,',','.') }}
                </div>

            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/actions">
                <i class="w-6 fas fa-hand-paper text-xl mr-2 text-center"></i> @lang('Actions')

                <div class="float-right text-xs text-blue pt-1">
                    {{ number_format(\App\Action::where('team_id', Auth::user()->team->id)->count()) }}
                </div>
            </a>
        </li>

        

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/tags">
                <i class="w-6 text-center fa fa-tag text-xl mr-2"></i> @lang('Tags')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Tag::thisTeam()->first())
                        {{ number_format(\App\Tag::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>

            </a>
        </li>


        <!-- <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/special">
                <i class="w-6 text-center fa fa-star text-xl mr-2"></i> @lang('Special Pages')

                <div class="float-right text-xs text-blue pt-1">
                    {{ \App\SpecialPage::where('app', 'campaign')->count() }}
                </div>
            </a>
        </li> -->

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/special/household-members">
                <i class="w-6 text-center fa fa-home text-xl mr-2"></i> @lang('Household Targets')

                <div class="float-right text-xs text-blue pt-1">
                    
                </div>
            </a>
        </li>

        

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/donations">
                <i class="w-6 text-center fa fa-dollar-sign text-xl mr-2"></i> @lang('Contributions')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Donation::thisTeam()->first())
                        {{ number_format(\App\Donation::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>


            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/events">
                <i class="w-6 text-center fa fa-cocktail text-xl mr-2"></i> @lang('Events')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\CampaignEvent::thisTeam()->first())
                        {{ number_format(\App\CampaignEvent::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>


            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/web-forms">
                <i class="w-6 text-center fas fa-window-restore text-xl mr-2"></i> @lang('Web Signups')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\WebForm::thisTeam()->first())
                        {{ number_format(\App\WebSignup::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>


            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/files/">
                <i class="w-6 fa fa-copy text-xl mr-2 text-center"></i> @lang('Files')
            </a>
            <span class="text-red">
                        <i class="fa fa-star"></i> <span class="text-red uppercase font-bold text-xs">New!</span>
                    </span>

            <div class="float-right text-xs text-blue pt-1">

                    {{ number_format(\App\WorkFile::thisTeam()->count(),0,',','.') }}
                    

                </div>
        </li>

       <!--  <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/questionnaires">
                <i class="w-6 text-center fas fa-poll-h text-xl mr-2"></i> @lang('Questionnaires')

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Questionnaire::thisTeam()->first())
                        {{ number_format(\App\Questionnaire::thisTeam()->count(),0,',','.') }}
                    @endif
                </div>


            </a>
        </li> -->


        


        <!--====================================================================-->


        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            @lang('My Account')
        </div>

        @if(Auth::user()->team->db_slice)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/link-voters">
                    <i class="w-6 fas fa-link text-xl mr-2 text-center"></i> @lang('Link Voters')

                    <div class="float-right text-xs text-blue pt-1">
                        {{ number_format(\App\Participant::where('team_id', Auth::user()->team->id)->whereNull('voter_id')->count()) }}
                    </div>

                </a>
            </li>
        @endif

        

        <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/users/settings">
                <i class="w-6 fas fa-cog text-xl mr-2 text-center"></i> @lang('My Preferences')
            </a>
        </li>

        

            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/paste">
                    <i class="w-6 fas fa-paste text-xl mr-2 text-center"></i> @lang('Paste Tool') 
                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Beta</span>
            </li>

        


        

            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/useruploads">
                    <i class="w-6 fas fa-upload text-xl mr-2 text-center"></i> @lang('Upload Data')
                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Beta</span>
            </li>

        
        
        @if(Auth::user()->permissions->admin)
            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/campaign/users/team">
                    <i class="w-6 fa fa-users text-xl mr-2 text-center"></i> @lang('Team &amp; Settings')
                    <span class="text-xs text-blue uppercase float-right mt-1">Admin</span>
                </a>
            </li>
        @endif

        @if(Auth::user()->permissions->developer)
            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap opacity-50">
                <a class="no-underline text-grey-dark hover:text-blue" href="/docs/{{ Auth::user()->team->app_type }}">
                    <i class="w-6 text-center fa fa-tv text-xl mr-2"></i> Docs & Videos
                    <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
                </a>
            </li>
        @endif

    </ul>
</div>