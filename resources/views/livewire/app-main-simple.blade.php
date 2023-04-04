
<div class="relative bg-white lg:overflow-hidden lg:h-screen">
  <div class="max-w-7xl mx-auto">
    <div class="relative z-10 pb-2 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32 lg:h-screen">
      <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
        <polygon points="50,0 100,0 50,100 0,100" />
      </svg>

      <div>
        <div class="relative pt-6 px-4 sm:px-6 lg:px-8">
          <nav class="relative flex items-center justify-between sm:h-10 lg:justify-start" aria-label="Global">
            <div class="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
              <div class="flex items-center justify-between w-full md:w-auto">
                <a href="#">
                  <span class="sr-only">Community Fluency App</span>
                  <img class="h-10 w-auto sm:h-12" src="/images/logo-words.png">
                </a>
                
              </div>
            </div>
          </nav>
        </div>

        <!--
          Mobile menu, show/hide based on menu open state.

          Entering: "duration-150 ease-out"
            From: "opacity-0 scale-95"
            To: "opacity-100 scale-100"
          Leaving: "duration-100 ease-in"
            From: "opacity-100 scale-100"
            To: "opacity-0 scale-95"
        -->

      <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
        <div class="sm:text-center lg:text-left">
          <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
            <span class="block text-blue-700 xl:inline">Connect</span>
            <span class="block xl:inline">your device</span>
          </h1>
          <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">Download the app, then permanently register your device here to never log in on your phone again.</p>
          
          <div class="hidden lg:block">
	          <p class="mt-16 text-base text-black font-bold sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-16 md:text-xl lg:mx-0">Don't have the app on your phone yet?</p>

	          <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
	            <div class="rounded-md shadow">
	              <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">iPhone App Store</a>
	            </div>
	            <div class="mt-3 sm:mt-0 sm:ml-3">
	              <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">Android App Store</a>
	            </div>
	          </div>
	      </div>

        </div>
      </main>
    </div>
  </div>

  <div class="hidden lg:block">
	  <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gradient-to-tr from-blue-500 to-blue-900 text-white">
	  	<div class="flex items-center">
	  		<div class="w-1/2 mx-auto mt-60">
			    @include('livewire.app-main-auth')
			</div>
		</div>
	  </div>
	</div>

	<div class="lg:hidden block relative text-white bg-white py-2">

		<div class="bg-gradient-to-tr from-blue-500 to-blue-900 text-white">
			<img src="/images/white-angle-top.png" />

			<div class="w-3/4 mx-auto">
				@include('livewire.app-main-auth')
			</div>

			<img src="/images/white-angle-bottom.png" />

		</div>
	</div>

  <div class="block lg:hidden p-8">
	          <p class="mt-16 text-base text-black font-bold sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-16 md:text-xl lg:mx-0">Don't have the app on your phone yet?</p>

	          <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
	            <div class="rounded-md shadow">
	              <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">iPhone App Store</a>
	            </div>
	            <div class="mt-3 sm:mt-0 sm:ml-3">
	              <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">Android App Store</a>
	            </div>
	          </div>
	      </div>
</div>
