<div id="left-nav" class="hidden-xs w-1/5 text-lg font-sans">

    <ul class="list-reset pr-8 w-full text-base">

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office">
                <i class="w-6 text-center fa fa-home text-xl mr-2 text-center"></i> @lang('Home')
            </a>
        </li>


        @if(Auth::user()->permissions->developer)
<!-- 
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/thoughts">
                    <i class="w-6 text-center fas fa-comment text-xl mr-2 text-center"></i> @lang('YOUR Thoughts')
                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
            </li> -->

        @endif



        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            @lang('District')
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/constituents">
                <i class="w-6 fas fa-user-circle text-xl mr-2 text-center"></i> @lang('Constituents')
            </a>
        </li>


        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/cases/">
                <i class="w-6 fa fa-folder-open text-xl mr-2 text-center"></i> @lang('Cases')
            </a>
        </li>

        @if (Auth::user()->team->shared_cases)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/shared-cases">
                    <i class="w-6 fa fa-handshake text-xl mr-2 text-center"></i> @lang('Shared') @lang('Cases') 
                   <!--  <span class="text-red">
                        <i class="fa fa-star"></i> <span class="text-red uppercase font-bold text-xs">New!</span>
                    </span> -->
                </a>
            </li>
        @endif

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/groups">
                <i class="fas fa-tag text-xl mr-2 w-6 text-center"></i>
                @lang('Groups')
            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/office/organizations">
                <i class="w-6 fas fa-hotel text-xl mr-2 text-center text-grey-dark"></i> @lang('Organizations')
                <span class="text-red">
                    <i class="fa fa-star"></i> <span class="text-red uppercase font-bold text-xs">New!</span>
                </span>
            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/files/">
                <i class="w-6 fa fa-copy text-xl mr-2 text-center"></i> @lang('Files')
            </a>
        </li>

        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            @lang('Notes')
        </div>
  
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/contacts">
                <i class="fas fa-edit text-xl mr-2 w-6 text-center"></i> All Notes
            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/followups/pending">
                <i class="fas fa-hand-point-right text-xl w-6 mr-2 text-center"></i> Follow Ups

               
                <span class="{{ (Auth::user()->outstandingFollowUps()->count() > 0) ? '' : 'hidden' }} mt-1 ml-1 px-3 text-xs bg-red rounded-full text-white shadow float-right" id="outstandingfollowups">
                    {{ Auth::user()->outstandingFollowUps()->count() }}
                </span>

            </a>
        </li>


        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/emails">
                <i class="fa fa-envelope text-xl w-6 mr-2 text-center"></i> Bulk Emails
            </a>
        </li>

        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            @lang('Metrics and Reports')
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/metrics/engagement">
                <i class="fa fa-link text-xl w-6 mr-2 text-center"></i> @lang('Engagement')
            </a>
        </li>
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/maps">
                <i class="fa fa-map text-xl w-6 mr-2 text-center"></i> Maps
            </a>
        </li>
        
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/exports">
                <i class="far fa-file-excel text-xl w-6 mr-2 text-center"></i> Export Constituents
            </a>
        </li>

        @if(Auth::user()->permissions->developer)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/jurisdictions">
                    <i class="fas fa-puzzle-piece text-xl w-6 mr-2 text-center"></i> Jurisdictions
                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
            </li>
        @endif

        
        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            @lang('My Account')
        </div>


        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/users/settings">
                <i class="w-6 fas fa-cog text-xl mr-2 text-center"></i> @lang('My Preferences')
            </a>
        </li>

        @if(Auth::user()->team->db_slice)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/link-voters">
                    <i class="w-6 fas fa-link text-xl mr-2 text-center"></i> @lang('Link Voters')

                    <div class="text-xs float-right pt-1">
                        {{ number_format(\App\Person::where('team_id', Auth::user()->team->id)->whereNull('voter_id')->count()) }}
                    </div>

                </a>
            </li>
        @endif

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/office/emails/master">
                <i class="far fa-list-alt text-xl mr-2 w-6 text-center"></i> Master Email List
            </a>
        </li>

        @if(Auth::user()->permissions->admin)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/users/team">
                    <i class="w-6 fa fa-users text-xl mr-2 text-center"></i> @lang('Team &amp; Settings')
                    <span class="text-xs text-blue uppercase float-right mt-1">Admin</span>
                </a>
            </li>
        @endif

        @if(Auth::user()->permissions->developer)
            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/office/useruploads">
                    <i class="w-6 fas fa-upload text-xl mr-2 text-center"></i> @lang('Upload Data')
                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
            </li>

        @endif

        @if(Auth::user()->permissions->developer)
            <li class="opacity-50 border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/docs/{{ Auth::user()->team->app_type }}">
                    <i class="w-6 text-center fa fa-tv text-xl mr-2"></i> Docs & Videos
                    <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
                </a>
            </li>
        @endif

       
    </ul>
</div>