<div class="px-10 pb-4 bg-blue-darker h-48" style="">
<!-- id="top-bg"  -->

    <div class="mx-auto flex">
        <div class="w-1/5 text-white pt-2">

            <a href="/{{ Auth::user()->team->app_type }}" class="text-white hover:text-white">
            
                <div class="text-2xl mt-6 mb-4 pl-8 relative">
                
                    <img src="/images/cf_logo_white.svg" class="w-12 absolute pin-l pin-t -mt-2 -ml-1" />
                
                    <span class="ml-2 font-thin tracking-wide">campaign</span><span class="">fluency</span>
                </div>


            </a>

      
            <div class="text-grey-lightest tracking-wide">
                <div class="mb-1">
                    <a href="/home">
                        <div class="text-lg text-grey-lightest">
                            {{ Auth::user()->team->name }}
                        </div>
                    </a>
                    <div class="">
                        {{ Auth::user()->team->district_name }}
                    </div>
                </div>

                <div class="border-l-2 pl-2 border-grey-darker">
                    
                    <div class="text-orange-lighter flex">
                        <div class="w-2/5 text-right pr-2">{{ number_format(Auth::user()->team->unarchived_count) }}</div> Voters 
                    </div>

                    <div class="text-blue-lighter flex">
                        <div class="w-2/5 text-right pr-2">{{ number_format(Auth::user()->team->participants()->count()) }}</div> Participants
                    </div>

                    @if(Auth::user()->team->participants()->newThisWeek()->first())
                        <div class="text-grey text-xs flex">
                            <div class="w-2/5 text-right pr-2">+</div>{{ number_format(Auth::user()->team->participants()->newThisWeek()->count()) }} new this week!
                        </div>
                    @endif
                </div>
            </div>


            <div class="w-full">
                
            </div>

        </div>

      
        <div class="w-2/5 flex items-center h-16 mt-4">

           
            <div class="dropdown my-6 w-3/5 text-grey hover:text-white flex bg-blue-darkest">

                <i class="fa fa-search mt-3 ml-3"></i>

                <input id="main-lookup-input" type="text" placeholder="Lookup name / email" style="font-family:Arial, Font Awesome\ 5 Free" data-toggle="dropdown" autocomplete="off" class="w-full text-grey-light appearance-none px-6 py-3 bg-transparent border-grey-dark focus:border-blue-light hover:text-white" />
                
                <div id="main-lookup" class="whitespace-no-wrap hidden mt-1 absolute z-10 bg-white border-2 shadow-lg p-2 mt-10 " style="min-width:600px;"></div>   

            </div>


        </div>
        
        <div class="w-2/5 flex justify-end font-serif items-center h-16 mt-4">

            @if (session('mocking'))
            
                <a href="/admin/mock/restore" class="mr-4 bg-red text-grey-lightest px-4 py-2 rounded-full hover:text-white border-white border-2">
                    MOCKING
                </a>

            @endif
            
            <a target="_blank" href="https://candidatefyi.com?special-invite={{ base64_encode(base64_encode(Auth::user()->email)) }}" class="font-sans mr-4 text-grey-lightest px-4 py-2 rounded-full hover:text-white">
                    <span class="text-white bg-red p-1 text-xs uppercase">New</span>
                    Invited to <b>CandidateFYI!</b> 
                </a>

           
            @include('elements.logindropdown')

        </div>

    </div>

</div>