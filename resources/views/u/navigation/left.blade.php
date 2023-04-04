<div id="left-nav" class="w-1/5 flex text-lg font-sans">

    <ul class="list-reset pr-8 w-full text-base">
        <li class="border-transparent rounded-full px-4 mb-1 p-1">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u">
                <i class="w-6 text-center fa fa-home mr-2 text-center text-grey-dark"></i> @lang('Home')
            </a>
        </li>


        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline est pb-1 border-b border-grey">
            @lang('Community')
        </div>

       <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline hover:text-blue text-grey-dark" href="/u/constituents">
                <i class="w-6 fas fa-user-circle text-xl mr-2 text-center text-grey-dark"></i> @lang('Constituents')
            </a>
        </li>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/entities">
                <i class="w-6 fas fa-hotel text-xl mr-2 text-center text-grey-dark"></i> @lang('Organizations')
            </a>
        </li>
        

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/groups">
                <i class="fas fa-tag text-xl mr-2 w-6 text-center text-grey-dark"></i>
                @lang('Groups')
            </a>
        </li>

        @if(Auth::user()->team->pilot)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
                <a class="no-underline  hover:text-blue text-grey-dark" href="/u/community-benefits">
                    <i class="w-6 text-center fa fa-dollar-sign mr-2 text-center text-grey-dark"></i> @lang('Community Benefits')
                </a>
            </li>
        @endif

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/files/">
                <i class="w-6 fa fa-copy text-xl mr-2 text-center text-grey-dark"></i> @lang('Files')
            </a>
        </li>

        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline est pb-1 border-b border-grey">
            @lang('Interactions')
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/cases/">
                <i class="w-6 fa fa-folder-open text-xl mr-2 text-center text-grey-dark"></i> @lang('Cases')
            </a>
        </li>
  
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/contacts">
                <i class="far fa-comments text-xl mr-2 w-6 text-center text-grey-dark"></i> All Notes
            </a>
        </li>
 
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/followups/pending">
                <i class="fas fa-hand-point-right text-xl w-6 mr-2 text-center text-grey-dark"></i> Follow Ups

               
                <span class="{{ (Auth::user()->outstandingFollowUps()->count() > 0) ? '' : 'hidden' }} ml-1 px-2 mb-1 p-1 text-xs bg-red rounded-full text-white shadow float-right" id="outstandingfollowups">
                    {{ Auth::user()->outstandingFollowUps()->count() }}
                </span>

            </a>
        </li>


    <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline est pb-1 border-b border-grey">
            @lang('Metrics and Reports')
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/metrics/engagement">
                <i class="fa fa-link text-xl w-6 mr-2 text-center text-grey-dark"></i> @lang('Engagement')
            </a>
        </li>
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/maps">
                <i class="fa fa-map text-xl w-6 mr-2 text-center text-grey-dark"></i> @lang('Maps')
            </a>
        </li>
 
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/exports">
                <i class="far fa-file-excel text-xl w-6 mr-2 text-center text-grey-dark"></i> Export @lang('Constituents')
            </a>
        </li>
    
        <div class="uppercase mt-4 mb-2 text-sm no-underline est pb-1 border-b border-grey tracking-wide uppercase text-sm text-grey-dark">
            @lang('Admin')
        </div>
        
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/users/settings">
                <i class="w-6 fas fa-cog text-xl mr-2 text-center text-grey-dark"></i> @lang('My Preferences')
            </a>
        </li>

        @if(
            (Auth::user()->permissions->admin) ||
            (Auth::user()->permissions->developer)
            )
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
            <a class="no-underline  hover:text-blue text-grey-dark" href="/u/users/team">
                <i class="w-6 fa fa-users text-xl mr-2 text-center text-grey-dark"></i> @lang('Team &amp; Settings')
            </a>
            <span class="text-xs text-blue uppercase float-right mt-1">Admin</span>
        </li>
        @endif

        @if(Auth::user()->permissions->developer)
            <li class="border-transparent rounded-full px-4 mb-1 py-2 overflow-x-hidden whitespace-no-wrap uppercase text-sm text-grey-dark">
                <a class="no-underline  hover:text-blue text-grey-dark" href="/docs/{{ Auth::user()->team->app_type }}">
                    <i class="w-6 text-center fa fa-tv text-xl mr-2 text-grey-dark"></i> Docs & Videos

                </a>
                <span class="text-xs text-blue uppercase float-right mt-1">Dev</span>
            </li>
        @endif
        

    </ul>
</div>