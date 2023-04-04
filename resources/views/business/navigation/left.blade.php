<div id="left-nav" class="hidden-xs w-1/5 text-lg font-sans">


    <ul class="list-reset pr-8 w-full text-base">

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business">
                <i class="w-6 text-center fa fa-home text-xl mr-2 text-center"></i> @lang('HQ')
            </a>
        </li>


        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            Entities
        </div>


       <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/clients">
                <i class="w-6 fas fa-users text-xl mr-2 text-center"></i> Clients

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Models\Business\SalesEntity::thisTeam()->where('client', true)->first())
                        {{ number_format(\App\Models\Business\SalesEntity::thisTeam()->where('client', true)->count()) }}
                    @endif
                </div>

            </a>
        </li>

       <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/prospects">
                <i class="w-6 fas fa-money-check-alt text-xl mr-2 text-center"></i> Prospects

                <div class="float-right text-xs text-blue pt-1">
                    @if(\App\Models\Business\SalesEntity::thisTeam()->where('client', '!=', true)->first())
                        {{ number_format(\App\Models\Business\SalesEntity::thisTeam()->where('client', '!=', true)->count()) }}
                    @endif
                </div>

            </a>
        </li>


        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            Organization
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/patterns">
                <i class="fas fa-list-ul text-xl mr-2 w-6 text-center"></i> Patterns
            </a>
        </li>
  
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/salesteams">
                <i class="fas fa-people-carry text-xl mr-2 w-6 text-center"></i> Sales Teams
            </a>
        </li>
 
  
        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/goals">
                <i class="fas fa-bullseye text-xl mr-2 w-6 text-center"></i> My Goals
            </a>
        </li>


        <!--====================================================================-->
        
        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            Tools
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/emails">
                <i class="fas fa-envelope text-xl mr-2 w-6 text-center"></i> Bulk Emailer
            </a>
        </li>

        <!--====================================================================-->

        <div class="uppercase mt-4 mb-2 text-sm no-underline text-grey-darkest pb-1 border-b border-grey">
            My Account
        </div>

        <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
            <a class="no-underline text-grey-dark hover:text-blue" href="/business/users/settings">
                <i class="w-6 fas fa-cog text-xl mr-2 text-center"></i> My Preferences
            </a>
        </li>

        
        @if(Auth::user()->permissions->admin)
            <li class="border-transparent rounded-full px-4 mb-1 p-1 overflow-x-hidden whitespace-no-wrap">
                <a class="no-underline text-grey-dark hover:text-blue" href="/business/users/team">
                    <i class="w-6 fa fa-users text-xl mr-2 text-center"></i> Team &amp; Settings
                </a>
            </li>
        @endif

        <!--====================================================================-->

  

       
    </ul>
</div>