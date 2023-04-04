<div class="bg-black px-10 pb-4 bg-white border-b-4 border-blue" style="background:url('/images/bg-cover.jpg');background-position: 100% 50%;">

    <div class="mx-auto flex">

        <div class="w-1/6 text-blue pt-2">

            <a href="/docs/{{ $section }}" class="text-blue">

                <div class="text-2xl mt-8 mb-6 pl-8 relative whitespace-no-wrap">

                    <img src="/images/cf_logo_white.png" class="w-16 absolute pin-l pin-t -mt-5 -ml-2" />
                   
                    <span class="ml-2 font-thin tracking-wide text-blue-lighter">community fluency <span class="font-extrabold uppercase text-xl text-blue-lightest">Docs</span>
                    
                </div>
                    
            </a>

        </div>


        <div class="w-1/5 flex items-center h-16 mt-4">
            
        </div>

        <div class="xs:hidden sm:visible w-4/5 flex justify-end font-serif items-center h-16 mt-4">

            @if(Auth::user())

                <a href="/{{ Auth::user()->team->app_type }}">
                    <button class="px-6 py-3 border-2 text-grey-light no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline font-sans rounded-full mr-3" type="button">
                        Back to <b>{{ Auth::user()->team->name }}</b>
                    </button>
                </a>

                @include('elements.logindropdown')

            @else

                <a href="/">
                    <button class="px-6 py-3 border-2 text-grey-light no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline font-sans rounded-full" type="button">
                        Go to Login
                    </button>
                </a>

            @endif

        </div>

    </div>

</div>

<div class="text-grey text-sm p-2 text-right">
    <!-- <span class="text-blue">Docs:</span> -->
    <a href="/docs/campaign" class="{{ ($section == 'campaign') ? 'text-black' : 'text-grey ' }} ">Campaign</a> | 
    <a href="/docs/office" class="{{ ($section == 'office') ? 'text-black' : 'text-grey ' }} ">Office</a> | 
    <a href="/docs/u" class="{{ ($section == 'u') ? 'text-black' : 'text-grey ' }} ">Community Relations</a>
</div>