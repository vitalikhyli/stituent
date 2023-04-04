<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/images/favicon.png" />

        <title>CommunityFluency | Constituent Service Platform</title>

        <!-- Fonts -->

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

        <!-- Styles -->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" />

        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.min.js" defer></script>


        <style>
            body {
                color: #999;
            }

            .bg-blue-600 {
                background-color: rgb(5,134,200);
            }
            .bg-blue-500 {
                background-color: rgb(56, 152, 210);

            }
            .text-blue-600 {
              color: rgb(5,134,200);
                /*color: rgba(62,142,224,1);*/
                /*color: rgb(56, 152, 210);*/
                /*color: rgb(56,107,161);*/
            }
            .text-blue-800 {
                color: rgb(13,68,122);
            }
            .bg-blue-900 {
                background-color: rgb(13,68,122);
            }

        </style>
    </head>
    <body>


        <div class="" style="">
  
          <div class="bg-gradient-to-bl from-gray-50 to-gray-200">
            <div class="relative overflow-hidden">
              <!-- <div class="absolute inset-y-0 h-full w-full" aria-hidden="true">
                <div class="relative h-full">
                  <svg class="absolute right-full transform translate-y-1/3 translate-x-1/4 md:translate-y-1/2 sm:translate-x-1/2 lg:translate-x-full" width="404" height="784" fill="none" viewBox="0 0 404 784">
                    <defs>
                      <pattern id="e229dbec-10e9-49ee-8ec3-0286ca089edf" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                      </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#e229dbec-10e9-49ee-8ec3-0286ca089edf)"></rect>
                  </svg>
                  <svg class="absolute left-full transform -translate-y-3/4 -translate-x-1/4 sm:-translate-x-1/2 md:-translate-y-1/2 lg:-translate-x-3/4" width="404" height="784" fill="none" viewBox="0 0 404 784">
                    <defs>
                      <pattern id="d2a68204-c383-44b1-b99f-42ccff4e5365" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                      </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#d2a68204-c383-44b1-b99f-42ccff4e5365)"></rect>
                  </svg>
                </div>
              </div> -->

              <div x-data="{ open: false}" class="relative pt-6 pb-16 sm:pb-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6">
                  <nav class="relative flex items-center justify-between sm:h-10 md:justify-center" aria-label="Global">
                    <div class="flex items-center flex-1 md:absolute md:inset-y-0 md:left-0">
                      <div class="flex items-center justify-between w-full md:w-auto">
                        
                        <a href="/" class="flex whitespace-no-wrap w-full h-10 hover:text-blue-500 text-blue-600">
            
                          <img class="w-12 h-12 -mt-2" src="/images/cf_logo_blue.svg">

                          <div class="flex text-2xl -ml-1">
                            
                            <span class="font-thin">community</span><span class="font-bold">fluency</span>
                          </div>
                        </a>
                        <div class="-mr-2 flex items-center md:hidden">
                          <button @click="open = true" type="button" class="bg-gray-50 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="main-menu" aria-haspopup="true" x-bind:aria-expanded="open">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" x-description="Heroicon name: outline/menu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="hidden md:flex md:space-x-10">
                    
                      
                        <a href="/features" class="font-medium text-gray-500 hover:text-gray-900">Features</a>
                      
                        <a href="/login#pricing" class="font-medium text-gray-500 hover:text-gray-900">Pricing</a>

                        <a href="https://campaignfluency.com" class="font-medium text-gray-500 hover:text-gray-900">Campaigns</a>
                      
                      
                    </div>
                    <div class="hidden md:absolute md:flex md:items-center md:justify-end md:inset-y-0 md:right-0">
                      <span class="inline-flex rounded-md shadow">
                        <a href="/login" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:text-blue-500">
                          Log in
                        </a>
                      </span>

                      <span class="inline-flex rounded-md shadow ml-4">
                        <a href="/register" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md bg-blue-600 text-white hover:bg-blue-500">
                          Free Trial
                        </a>
                      </span>
                    </div>
                  </nav>
                </div>

                <div class="absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden" x-show="open" x-description="Mobile menu, show/hide based on menu open state." x-transition:enter="duration-150 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="duration-100 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" style="display: none;">
                  <div class="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="px-5 pt-4 flex items-center justify-between">
                      <div>
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-blue-600.svg" alt="">
                      </div>
                      <div class="-mr-2">
                        <button @click="open = false" type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                          <span class="sr-only">Close main menu</span>
                          <svg class="h-6 w-6" x-description="Heroicon name: outline/x" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
                        </button>
                      </div>
                    </div>
                    <div role="menu" aria-orientation="vertical" aria-labelledby="main-menu">
                      <div class="px-2 pt-2 pb-3 space-y-1" role="none">
                    
                        
                          <a href="/features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50" role="menuitem">Features</a>
                        
                          <a href="/pricing" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50" role="menuitem">Pricing</a>

                          <a href="/nightcap" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50" role="menuitem">NightCap®</a>
                        
                        
                      </div>
                      <div role="none">
                        <a href="/login" class="block w-full px-5 py-3 text-center font-medium text-blue-600 bg-gray-50 hover:bg-gray-100 hover:text-blue-700" role="menuitem">
                          Log in
                        </a>

                        <a href="/register" class="block w-full px-5 py-3 text-center font-medium text-blue-600 bg-gray-50 hover:bg-gray-100 hover:text-blue-700" role="menuitem">
                          Free Trial
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-16 mx-auto max-w-7xl px-4 sm:mt-24 sm:px-6">
                  <div class="text-center">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                      <span class="block">Simplified constituent service</span>
                      <span class="block text-blue-600">for elected officials</span>
                    </h1>
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                      We provide you with the tools you need to make the most of your time.<br> Organized call logs, cases, & district database built for <b>your remote workflow</b>.
                    </p>
                  </div>
                </div>
              </div>
              <div x-data="{ video: '1' }">
                  <div class="hidden sm:flex text-center w-5/6 mx-auto font-bold text-lg text-gray-400 cursor-pointer">

                    @foreach (\App\Video::all() as $video)

                        <div class="group flex-1 px-4 py-2 hover:text-blue-800 transition duration-500" :class="{ 'text-blue-800': video === '{{ $video->id }}' }" @click="video = '{{ $video->id }}'">
                            <span class="text-sm">{{ $video->length }}</span>
                            <br>
                            {{ $video->name }}<br>
                            <i class="-mt-3 fa fa-caret-down fa-3x opacity-0 group-hover:opacity-100" :class="{ 'opacity-100': video === '{{ $video->id }}' }"></i>
                        </div>
                        
                    @endforeach
                  </div>

                  <div class="block sm:hidden ml-8 font-bold text-lg text-gray-400 cursor-pointer">

                    @foreach (\App\Video::all() as $video)

                        <div class="group flex items-center hover:text-blue-800 transition duration-500" :class="{ 'text-blue-800': video === '{{ $video->id }}' }" @click="video = '{{ $video->id }}'">
                            <i class="mr-4 fa fa-caret-down fa-2x opacity-0 group-hover:opacity-100" :class="{ 'opacity-100': video === '{{ $video->id }}' }"></i>
                            
                            {{ $video->name }}
                            <span class="text-sm ml-2">{{ $video->length }}</span>
                            
                        </div>
                        
                    @endforeach
                  </div>

                  

                  <div class="relative">
                    <div class="absolute inset-0 flex flex-col" aria-hidden="true">
                      <div class="flex-1"></div>
                      <div class="flex-1 w-full bg-blue-900"></div>
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6">
                      <!-- <img class="relative rounded-lg shadow-lg" src="https://tailwindui.com/img/component-images/top-nav-with-multi-column-layout-screenshot.jpg" alt="App screenshot"> -->

                      @foreach (\App\Video::all() as $video)

                        <div class="hidden lg:block relative" x-show="video === '{{ $video->id }}'">
                          <iframe class="rounded-lg shadow-lg border-2 mx-auto border-gray-300 bg-gray-300" src="https://player.vimeo.com/video/{{ $video->vimeo_id }}" width="1152" height="700" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                      </div>

                      <div class="hidden sm:block lg:hidden relative" x-show="video === '{{ $video->id }}'">
                          <iframe class="rounded-lg shadow-lg mx-auto" src="https://player.vimeo.com/video/{{ $video->vimeo_id }}" width="100%" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                      </div>

                      <div class="block sm:hidden relative" x-show="video === '{{ $video->id }}'">
                          <iframe class="rounded-lg shadow-lg mx-auto" src="https://player.vimeo.com/video/{{ $video->vimeo_id }}" width="100%" height="285" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                      </div>
                        

                    @endforeach
                      

                    </div>
                  </div>
              </div>
            </div>
            <div class="bg-blue-900">
              <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                <h2 class="text-center text-gray-400 text-sm font-semibold uppercase tracking-wide">Trusted by over 100 Senators, Representatives, aides, and Universities in MA.
                <br>
                Speak to us directly at <span class="text-white">617.699.4553</span></h2>
                <div class="mt-8 flex w-full text-center text-gray-400">
                  <div class="mx-auto">

                    <a href="/"><img class="w-1/2 mx-auto" src="/images/cf_logo_white.svg" alt="Community Fluency"></a><br>
                    &copy; {{ date('Y') }}
                  </div>
                  <!-- <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1">
                    <img class="h-12" src="https://tailwindui.com/img/logos/mirage-logo-gray-400.svg" alt="Mirage">
                  </div>
                  <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1">
                    <img class="h-12" src="https://tailwindui.com/img/logos/statickit-logo-gray-400.svg" alt="StaticKit">
                  </div>
                  <div class="col-span-1 flex justify-center md:col-span-3 lg:col-span-1">
                    <img class="h-12" src="https://tailwindui.com/img/logos/transistor-logo-gray-400.svg" alt="Transistor">
                  </div>
                  <div class="col-span-2 flex justify-center md:col-span-3 lg:col-span-1">
                    <img class="h-12" src="https://tailwindui.com/img/logos/workcation-logo-gray-400.svg" alt="Workcation">
                  </div> -->
                </div>
              </div>
            </div>
          </div>

        </div>

    </body>
</html>
