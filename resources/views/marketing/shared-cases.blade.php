@extends('marketing')

@section('title')
    Shared Cases
@endsection


@section('main')

<div class="">
  <div class="bg-cover overflow-hidden bg-gradient-to-bl from-blue-700 to-blue-900">
  	
    <div class="relative hidden lg:block lg:absolute lg:inset-0">
      <svg class="absolute top-0 left-1/2 transform translate-x-64 -translate-y-8" width="640" height="784" fill="none" viewBox="0 0 640 784">
        <defs>
          <pattern id="9ebea6f4-a1f5-4d96-8c4e-4c2abf658047" x="118" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
          </pattern>
        </defs>
        <rect y="72" width="640" height="640" class="text-gray-50 opacity-25" fill="#000" />
        <rect x="118" width="404" height="784" fill="url(#9ebea6f4-a1f5-4d96-8c4e-4c2abf658047)" class="opacity-25" />
      </svg>
    </div>
    <div x-data="{ open: false }" class="relative pt-6 pb-16 md:pb-20 lg:pb-24 xl:pb-32">
      <nav class="relative max-w-screen-xl mx-auto flex items-center justify-between px-4 sm:px-6">
        <div class="flex items-center flex-1">
          <div class="flex items-center justify-between w-full md:w-auto">
            <a class="whitespace-no-wrap flex text-white text-xl" href="/" style="margin-top: -2px;">
              <img class="h-8 w-auto sm:h-10 -mt-1" src="/images/cf_logo_white.svg" alt="" />
              <div class=" -ml-3 mt-1">
                  <span class="ml-2 font-thin tracking-wide">community</span><span class="font-bold">fluency</span>
              </div>
            </a>
            <div class="-mr-2 flex items-center md:hidden">
              <button @click="open = true" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </div>
          </div>
          <div class="hidden md:block md:ml-10 md:pl-4">
            <!-- <a href="#" class="font-medium text-gray-200 hover:text-white focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">Product</a> -->
            <a href="#features" class="ml-10 font-medium text-gray-200 hover:text-white focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">Features</a>
            <!-- <a href="#" class="ml-10 font-medium text-gray-200 hover:text-white focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">About Us</a> -->
            <a href="https://communityfluency.com/" class="ml-10 font-medium text-gray-200 hover:text-white focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">Constituent Service</a>
          </div>
        </div>


        <div class="text-2xl font-bold text-white mr-6 hidden md:block">
          <i class="fas fa-phone mr-2"></i>
          <span>(617) 699-4553</span>
        </div>

        <div class="hidden md:block text-right">
          <span class="inline-flex rounded-md shadow-md">
            <span class="inline-flex rounded-md shadow-xs">
              <a href="https://communityfluency.com/request-demo" class="inline-flex items-center px-4 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                Schedule a Demo
              </a>
            </span>
          </span>
        </div>
      </nav>


      <!--
        Mobile menu, show/hide based on menu open state.

        Entering: "duration-150 ease-out"
          From: "opacity-0 scale-95"
          To: "opacity-100 scale-100"
        Leaving: "duration-100 ease-in"
          From: "opacity-100 scale-100"
          To: "opacity-0 scale-95"
      -->
      <div x-show="open" x-description="Mobile menu, show/hide based on menu open state." x-transition:enter="duration-150 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="duration-100 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden">
        <div class="rounded-lg shadow-md">
          <div class="rounded-lg bg-white shadow-xs overflow-hidden">
            <div class="px-5 pt-4 flex items-center justify-between">
              <div>
                <img class="h-8 w-auto" src="/images/cf_logo_white.svg" alt="" />
              </div>
              <div class="-mr-2">
                <button @click="open = false" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <div class="px-2 pt-2 pb-3">
              <!-- <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">Product</a> -->
              <a href="#" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">Features</a>
              <a href="#" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">About Us</a>
              <a href="#" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 focus:outline-none focus:text-gray-900 focus:bg-gray-50 transition duration-150 ease-in-out">Constituent Service</a>
            </div>
            <div>
              <a href="#" class="block w-full px-5 py-3 text-center font-medium text-blue-600 bg-gray-50 hover:bg-gray-100 hover:text-blue-700 focus:outline-none focus:bg-gray-100 focus:text-blue-700 transition duration-150 ease-in-out">
                Log in
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8 mx-auto max-w-screen-xl px-4 sm:mt-12 sm:px-6 md:mt-20 xl:mt-24">
      	<!-- <div class="h-16"></div> -->
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        	
          <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
            <div class="text-sm font-semibold uppercase tracking-wide text-gray-500 sm:text-base lg:text-sm xl:text-base">
              
            </div>
            <h2 class="mt-1 text-4xl tracking-tight leading-10 font-extrabold text-gray-300 sm:leading-none sm:text-6xl lg:text-5xl xl:text-6xl">
              Collaborate with
              <br/>
              <span class="text-white">Shared Cases</span>
            </h2>
            <p class="mt-3 text-sm text-white sm:mt-5 sm:text-lg lg:text-base xl:text-lg">
              Being an elected official and a public servant means you are part of a larger community. Whether it's an overlapping district or just a helping hand, we let you work seamlessly with other offices.
            </p>
            <div class="mt-5 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">



              <!-- <p class="mt-3 text-sm leading-5 text-gray-200">
                We care about the protection of your data. Read our
                <a href="#" class="font-medium text-gray-900 underline">Privacy Policy</a>.
              </p> -->
            </div>
          </div>
          <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
            
            <div x-data="{ open: false }" class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
              <button @click="open = true" class="relative block w-full rounded-lg overflow-hidden focus:outline-none focus:shadow-outline">
                <img class="w-full" src="/images/marketing/shared-cases.png" alt="Screenshot of a campaign map" />
                <div class="absolute inset-0 w-full h-full flex items-center justify-center">
                  <svg class="h-20 w-20 text-blue-500" fill="currentColor" viewBox="0 0 84 84">
                    <circle opacity="0.9" cx="42" cy="42" r="42" fill="white" />
                    <path d="M55.5039 40.3359L37.1094 28.0729C35.7803 27.1869 34 28.1396 34 29.737V54.263C34 55.8604 35.7803 56.8131 37.1094 55.9271L55.5038 43.6641C56.6913 42.8725 56.6913 41.1275 55.5039 40.3359Z" />
                  </svg>
                </div>
              </button>
              @include('components.video-modal', ['video_id' => '473191108'])
            </div>
          </div>
        </div>
      </div>

      <div class="w-full text-center text-3xl mt-8 sm:mt-32 sm:text-4xl tracking-wide text-white font-black">
        <span class="pb-2">How does it work?</span><br>
        <span class="text-base text-gray-100 font-bold display-block pt-4 border-t-4">
          Two or more offices share notes, contacts, files, and status.
        </span>
      </div>

      <div class="sm:-mt-16 flex w-11/12 sm:w-2/3 mx-auto text-center text-xl text-white pt-8">

        <div class="w-1/2 sm:w-1/5">
          <img class="w-2/3" src="/images/marketing/bruce-wayne.png" />
        </div>
        <div class="w-3/5">
          
        </div>

        <div class="w-1/2 sm:w-1/5 relative">
          <div class="absolute right-0 bottom-0 w-3/4">
            <img class="" src="/images/marketing/selina-kyle.png" />
          </div>
        </div>

      </div>

      <div class="flex w-11/12 sm:w-2/3 mx-auto text-center text-xl text-white">

        <div class="w-1/2 sm:w-1/5">
          <div class="px-4 py-2 bg-blue-900 border-4 whitespace-no-wrap">
            Rep. Bruce Wayne
            <div class="text-sm text-gray-400 uppercase -mt-1">
              House District 12
            </div>
          </div>
        </div>
        <div class="w-3/5">
          
        </div>
        <div class="w-1/2 sm:w-1/5">
          <div class="px-4 py-2 bg-blue-900 border-4 whitespace-no-wrap">
            Sen. Selina Kyle
            <div class="text-sm text-gray-400 uppercase -mt-1">
              Senate District 3
            </div>
          </div>
        </div>

      </div>

  	  <div class="flex w-11/12 sm:w-2/3 mx-auto text-center text-xl text-white">

  	  	<div class="w-1/5">
  	  		<div class="w-full">
  		  		<div class="border-l-4 h-16 mx-auto w-0"></div>
  		  	</div>
  	  	</div>
  	  	<div class="w-3/5">
  	  		
  	  	</div>
  	  	<div class="w-1/5">
  	  		<div class="w-full">
  		  		<div class="border-l-4 h-16 mx-auto w-0"></div>
  		  	</div>
  	  	</div>

  	  </div>

  	  <div class="flex w-11/12 sm:w-2/3 mx-auto text-center text-xl text-white -mt-1">

  	  	<div class="w-1/5">
  	  		<div class="w-full">
  		  		<div class="border-t-4 ml-auto w-1/2"></div>
  		  	</div>
  	  	</div>
  	  	<div class="w-3/5 border-t-4 ">
  	  		
  	  	</div>
  	  	<div class="w-1/5">
  	  		<div class="w-full">
  		  		<div class="border-t-4 mr-auto w-1/2"></div>
  		  	</div>
  	  	</div>

  	  </div>

  	  <div class="flex w-11/12 w-2/3 mx-auto text-center text-xl text-white">

  	  	<div class="w-1/5">

  	  	</div>
  	  	<div class="w-3/5">
  	  		<div class="w-full">
  		  		<div class="mx-auto border-l-4 h-16 w-0"></div>
  		  	</div>
  	  	</div>
  	  	<div class="w-1/5">

  	  	</div>

  	  </div>

  	  <div class="flex w-11/12 w-2/3 mx-auto text-center text-xl text-white">

  	  	<div class="w-1/3">

  	  	</div>
  	  	<div class="w-2/3 sm:w-1/3">
  	  		<div class="w-full">
  		  		<div class="mx-auto border-4 px-4 py-2">
  		  			Oswald Cobblepot
  		  			<div class="text-sm text-gray-400 uppercase -mt-1">
  			  			Shared Constituent
  			  		</div>
  		  		</div>
  		  	</div>
  	  	</div>
  	  	<div class="w-1/3">

  	  	</div>

  	  </div>
      <div class="flex w-11/12 w-2/3 mx-auto text-center text-xl text-white">

          <div class="w-1/5">

          </div>
          <div class="w-3/5">
            <div class="w-full">
              <div class="mx-auto border-l-4 h-16 w-0"></div>
            </div>
          </div>
          <div class="w-1/5">

          </div>

        </div>

    </div>
  </div>
</div>

<div class="w-11/12 sm:w-2/3 mx-auto z-50">
	<div class="w-full bg-white mx-auto z-10 p-2 -mt-16 sm:-mt-32 shadow-lg">
    <img src="/images/marketing/case-page.png" />
	</div>

</div>



<div class="p-16 text-center text-2xl text-blue-900 sm:w-2/3 mx-auto">
  <div class="mx-auto sm:w-1/5">
    <img class="" src="/images/FS_logo_blue_large.png" />
  </div>
  Community Fluency is a complete <span class="font-black">constituent service platform</span>
  <br>and voter database for elected officials in Massachusetts.
</div>

<div class="text-center p-8">
  <a class="text-white text-2xl font-bold uppercase bg-blue-500 hover:bg-blue-700 rounded-full px-8 py-4" href="/request-demo">
    Request a Demo
  </a>
</div>

<div class="">
  <img src="/images/statehouse.svg" />
</div>

@endsection