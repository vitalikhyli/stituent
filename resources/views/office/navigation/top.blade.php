<div id="top-bg" class="hidden-xs px-10 pb-4 h-48" style="">

    <div class="mx-auto flex">
        <div class="w-1/5 text-white pt-2">

            <a href="/{{ Auth::user()->team->app_type }}" class="text-white hover:text-white">
            

                <div class="text-2xl mt-6 mb-6 pl-8 relative">
                
                    <img src="/images/cf_logo_white.svg" class="w-12 absolute pin-l pin-t -mt-2 -ml-1" />
                
                    <span class="ml-2 font-thin tracking-wide">community</span><span class="">fluency</span>
                
                </div>


            </a>

      
            <div class="cursor-pointer leading-tight tracking-wide font-light mt-2">
                <div class="font-medium">
                    {{ Auth::user()->team->name }}
                </div>
                <div class="">
                    {{ Auth::user()->team->district_name }}
                </div>
                <div class="">
                    <b>{{ number_format(Auth::user()->team->constituents_count,0,'.',',') }}</b> 
                   {{ \Illuminate\Support\Str::plural('Constituent', Auth::user()->team->constituents_count) }}
                </div>
            </div>

        </div>

        <div class="w-2/5 flex items-center h-16 mt-4">

            <div class="dropdown my-6 w-3/5">

                <input id="main-lookup-input" type="text" placeholder="Lookup Name, Email, Group, etc" style="font-family:Arial, Font Awesome\ 5 Free;background-color: rgba(0, 0, 0, 0.09);" data-toggle="dropdown" autocomplete="off" class="w-full text-grey-light appearance-none px-6 py-3 border-grey shadow-inner" />

                <div id="main-lookup" class="whitespace-no-wrap hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="min-width:600px;"></div>   

            </div>

        </div>
        
        <div class="w-2/5 flex justify-end font-serif items-center h-16 mt-4">

            @if (session('mocking'))
            
                <a href="/admin/mock/restore" class="mr-4 bg-red text-grey-lightest px-4 py-2 rounded-full hover:text-white border-white border-2">
                    MOCKING
                </a>

            @endif

            
           
            @include('elements.logindropdown')

        </div>

    </div>

</div>

<div class="hidden-sm hidden-md hidden-lg hidden-xl px-10 pb-4 bg-blue-darker h-64" style="">
<!-- <div id="top-bg" class="px-10 pb-4 bg-blue h-48" style="">
 -->
    <div class="mx-auto flex">
        <div class="w-1/2 sm:w-1/5 text-white pt-2">

            <a href="/office" class="text-white hover:text-white">
            
                

                <div class="text-base mt-6 mb-6 pl-2 relative">
                    <img src="/images/cf_logo_white.png" class="w-12 absolute pin-l pin-t -mt-4 -ml-5" />
                    <span class="ml-2 font-thin tracking-wide">community</span><span class="">fluency</span>
                </div>

            </a>

      
            

        </div>

        <div class="w-1/2 flex justify-end font-serif items-center h-16 mt-4 text-sm">
           
            @include('elements.logindropdown-mobile')

        </div>
    </div>
    <div class="text-white flex w-full leading-tight tracking-wide font-light mt-2 text-center">


        <div class="mx-auto text-xl">
            <i class="fa fa-bars"></i>
        </div>
         

    </div>

</div>