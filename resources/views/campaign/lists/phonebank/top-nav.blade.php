<!-- <div class="md:hidden block px-10 pb-4 bg-blue-darker h-36" style="">

    <div class="text-2xl pt-6 mb-6 pl-8 relative cursor-pointer text-white">
    
        <img src="/images/cf_logo_white.svg" class="w-12 -mr-4 -mt-1" />
    
        <span class="ml-2 font-thin tracking-wide">campaign</span><span class="">fluency</span>
    </div>

    <div class="text-center">
        <a class="" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            <button class="rounded-lg bg-blue text-white px-4 py-2 text-sm">
                Log Out
            </button>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

</div>

 -->
<div class="px-10 pb-4 bg-blue-darker h-48" style="">


    <div class="mx-auto flex">
        <div class="w-1/5 text-white pt-2">

            @if(!Auth::user()->permissions->guest)
                <a href="/{{ Auth::user()->team->app_type }}" class="text-white hover:text-white">
            @endif

                <div class="text-2xl mt-6 mb-6 pl-8 relative cursor-pointer">
                
                    <img src="/images/cf_logo_white.svg" class="w-12 absolute pin-l pin-t -mt-2 -ml-1" />
                
                    <span class="ml-2 font-thin tracking-wide">campaign</span><span class="">fluency</span>
                </div>

            @if(!Auth::user()->permissions->guest)
                </a>
            @endif

      
            <div class="mt-2 text-grey tracking-wide">
                <div class="">
                    {{ Auth::user()->team->name }}
                </div>
                <div class="">
                    {{ Auth::user()->team->district_name }}
                </div>
                <div class="">
                    <b>{{ number_format(Auth::user()->team->constituents_count,0,'.',',') }}</b> Voters
                </div>
            </div>


            <div class="w-full">
                
            </div>

        </div>

      
        <div class="w-2/5 flex items-center h-16 mt-4">

           

        </div>
        
        <div class="w-2/5 flex justify-end items-center h-16 mt-4">

            @if (session('mocking'))
            
                <a href="/admin/mock/restore" class="mr-4 bg-red text-grey-lightest px-4 py-2 rounded-full hover:text-white border-white border-2">
                    MOCKING
                </a>

            @endif
            
           
            <a class="" href="{{ route('logout') }}"
               onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                <button class="rounded-lg bg-blue text-white px-4 py-2 text-sm">
                    Log Out
                </button>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>


        </div>

    </div>

</div>