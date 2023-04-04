<div id="pricing" x-data="{ price_frequency: 'quarterly' }" class="">
  <div class="pt-12 px-4 sm:px-6 lg:px-8 lg:pt-20">
    <div class="text-center">
      <h2 class="text-lg leading-6 font-semibold text-blue-600 uppercase tracking-wider">
        Pricing
      </h2>
      <p class="mt- text-3xl leading-9 font-extrabold text-blue-600 sm:text-4xl sm:leading-10 lg:text-5xl lg:leading-none">
        Transparent pricing for your seat,<br>
        <b>no upcharges</b>
      </p>
      <p class="mt-3 max-w-4xl mx-auto text-xl leading-7 text-gray-600 sm:mt-5 sm:text-2xl sm:leading-8">
        We offer 2 primary payment schedules, with a discount if you sign up for annual billing. <br>
        <span class="text-3xl font-bold text-red-500">*</span>For smaller/larger districts or special races, please call Peri at 617.699.4553
      </p>
    </div>
  </div>

  <div class="px-8">
    <div class="flex mt-8 border-blue-800 border-2 bg-white cursor-pointer rounded-full sm:mx-auto w-full sm:w-1/2 md:w-1/3 lg:w-1/4 text-center">
      <div :class="{ 'font-bold text-white bg-blue-800': price_frequency === 'monthly' }" @click="price_frequency = 'monthly'" class="w-1/3 py-2 px-4 transition-colors duration-500 rounded-full">
        Monthly
      </div>
      <div :class="{ 'font-bold text-white bg-blue-800': price_frequency === 'quarterly' }" @click="price_frequency = 'quarterly'" class="w-1/3 py-2 px-4 transition-colors duration-500 rounded-full">
        Quarterly
      </div>
      <div :class="{ 'font-bold text-white bg-blue-800': price_frequency === 'annual' }" @click="price_frequency = 'annual'" class="w-1/3 relative py-2 px-4 transition-colors duration-500 rounded-full">
        Annually
        <div class="absolute text-xs text-white bg-red pin-t pin-r px-2 py-1 rounded-full -mt-3 -ml-2">
          Save $$
        </div>
      </div>
    </div>
  </div>

  <div class="absolute bg-blue-800 w-full h-64 mt-64">
  </div>

  <div class="mt-16 pb-12 lg:mt-20 lg:pb-20">
    <div class="relative z-0">
      <div class="absolute inset-0 h-5/6 lg:h-2/3"></div>
      <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative lg:grid lg:grid-cols-7">
          <div class="mx-auto max-w-md lg:mx-0 lg:max-w-none lg:col-start-1 lg:col-end-3 lg:row-start-2 lg:row-end-3">
            <div class="h-full flex flex-col rounded-lg shadow-lg overflow-hidden lg:rounded-none lg:rounded-l-lg">
              <div class="flex-1 flex flex-col">
                <div class="bg-white px-6 py-10">
                  <div>
                    <h3 class="text-center text-2xl leading-8 font-medium text-gray-900" id="tier-hobby">
                      12k+
                    </h3>
                    <div class="mt-4 flex items-center justify-center">

                      <span x-show="price_frequency == 'monthly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          80
                        </span>
                      </span>
                      <span x-show="price_frequency == 'monthly'" class="text-xl leading-7 font-medium text-gray-500">
                        /month
                      </span>

                      

                      <span x-show="price_frequency == 'quarterly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          240
                        </span>
                      </span>
                      <span x-show="price_frequency == 'quarterly'" class="text-xl leading-7 font-medium text-gray-500">
                        /quarter
                      </span>

                      <span x-show="price_frequency == 'annual'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          800
                        </span>
                      </span>
                      <span x-show="price_frequency == 'annual'" class="text-xl leading-7 font-medium text-gray-500">
                        /year
                      </span>
                    </div>
                    <div x-show="price_frequency == 'monthly'" class="text-xl text-center leading-7 font-medium text-gray-500">
                        with autopay subscription
                      </div>
                  </div>
                </div>
                <div class="flex-1 flex flex-col justify-between border-t-2 border-gray-100 p-6 bg-gray-200 sm:p-10 lg:p-6 xl:p-10">
                  <ul>
                    <li class="flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Complete voter history
                      </p>
                    </li>
                    <li class="mt-4 flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Unlimited users
                      </p>
                    </li>
                    <li class="mt-4 flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      Campaign features included
                    </p>
                  </li>
                    <li class="mt-4 flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Ward/Precinct mapping tools
                      </p>
                    </li>
                  </ul>
                  <div class="mt-8">
                    <div class="rounded-lg shadow-md">
                      <a href="/request-demo" class="block w-full text-center rounded-lg border border-transparent bg-white px-6 py-3 text-base leading-6 font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150" aria-describedby="tier-hobby">
                        Start your trial
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-10 max-w-lg mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-start-3 lg:col-end-6 lg:row-start-1 lg:row-end-4">
            <div class="relative z-10 rounded-lg shadow-xl">
              <div class="pointer-events-none absolute inset-0 rounded-lg border-2 border-blue-600"></div>
              <div class="absolute inset-x-0 top-0 transform translate-y-px">
                <div class="flex justify-center transform -translate-y-1/2">
                  <span class="inline-flex rounded-full bg-blue-600 px-4 py-1 text-sm leading-5 font-semibold tracking-wider uppercase text-white">
                    Choose Your District Size
                  </span>
                </div>
              </div>
              <div class="bg-white rounded-t-lg px-6 pt-12 pb-10">
                <div>
                  <h3 class="text-center text-3xl leading-9 font-semibold text-gray-900 sm:-mx-6" id="tier-growth">
                    30k+
                  </h3>
                  <div class="mt-4 flex items-center justify-center">
                    <span x-show="price_frequency == 'monthly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900 sm:text-6xl">
                      <span class="mt-2 mr-2 text-4xl font-medium">
                        $
                      </span>
                      <span class="font-extrabold">
                        110
                      </span>
                    </span>
                    <span x-show="price_frequency == 'monthly'" class="text-2xl leading-8 font-medium text-gray-500">
                      /month
                    </span>

                    <span x-show="price_frequency == 'quarterly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900 sm:text-6xl">
                      <span class="mt-2 mr-2 text-4xl font-medium">
                        $
                      </span>
                      <span class="font-extrabold">
                        330
                      </span>
                    </span>
                    <span x-show="price_frequency == 'quarterly'" class="text-2xl leading-8 font-medium text-gray-500">
                      /quarter
                    </span>

                    <span x-show="price_frequency == 'annual'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900 sm:text-6xl">
                      <span class="mt-2 mr-2 text-4xl font-medium">
                        $
                      </span>
                      <span class="font-extrabold">
                        1,200
                      </span>
                    </span>
                    <span x-show="price_frequency == 'annual'" class="text-2xl leading-8 font-medium text-gray-500">
                      /year
                    </span>
                  </div>
                  <div x-show="price_frequency == 'monthly'" class="text-xl text-center leading-7 font-medium text-gray-500">
                        with autopay subscription
                      </div>
                </div>
              </div>
              <div class="border-t-2 border-gray-100 rounded-b-lg pt-10 pb-8 px-6 bg-gray-200 sm:px-10 sm:py-10">
                <ul>
                  <li class="flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      Unlimited access to your district voter file
                    </p>
                  </li>
                  <li class="mt-4 flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      Unlimited users
                    </p>
                  </li>

                  <li class="mt-4 flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      Campaign features included
                    </p>
                  </li>
                  <li class="mt-4 flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      District mapping tools
                    </p>
                  </li>
                  <li class="mt-4 flex items-start">
                    <div class="flex-shrink-0">
                      <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </div>
                    <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                      State House specific community
                    </p>
                  </li>
                  
                </ul>
                <div class="mt-10">
                  <div class="rounded-lg shadow-md">
                    <a href="/request-demo" class="block w-full text-center rounded-lg border border-transparent bg-blue-600 px-6 py-4 text-xl leading-6 font-medium text-white hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150" aria-describedby="tier-growth">
                      Start your trial
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-10 mx-auto max-w-md lg:m-0 lg:max-w-none lg:col-start-6 lg:col-end-8 lg:row-start-2 lg:row-end-3">
            <div class="h-full flex flex-col rounded-lg shadow-lg overflow-hidden lg:rounded-none lg:rounded-r-lg">
              <div class="flex-1 flex flex-col">
                <div class="bg-white px-6 py-10">
                  <div>
                    <h3 class="text-center text-2xl leading-8 font-medium text-gray-900" id="tier-scale">
                      100k+
                    </h3>
                    <div class="mt-4 flex items-center justify-center">
                      <span x-show="price_frequency == 'monthly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          210
                        </span>
                      </span>
                      <span x-show="price_frequency == 'monthly'" class="text-xl leading-7 font-medium text-gray-500">
                        /month
                      </span>

                      <span x-show="price_frequency == 'quarterly'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          630
                        </span>
                      </span>
                      <span x-show="price_frequency == 'quarterly'" class="text-xl leading-7 font-medium text-gray-500">
                        /quarter
                      </span>

                      <span x-show="price_frequency == 'annual'" class="px-3 flex items-start text-6xl leading-none tracking-tight text-gray-900">
                        <span class="mt-2 mr-2 text-4xl font-medium">
                          $
                        </span>
                        <span class="font-extrabold">
                          2,400
                        </span>
                      </span>
                      <span x-show="price_frequency == 'annual'" class="text-xl leading-7 font-medium text-gray-500">
                        /year
                      </span>
                    </div>
                    <div x-show="price_frequency == 'monthly'" class="text-xl text-center leading-7 font-medium text-gray-500">
                        with autopay subscription
                      </div>
                  </div>
                </div>
                <div class="flex-1 flex flex-col justify-between border-t-2 border-gray-100 p-6 bg-gray-200 sm:p-10 lg:p-6 xl:p-10">
                  <ul>
                    <li class="flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Complete voter history
                      </p>
                    </li>
                    <li class="mt-4 flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Unlimited users, campaign features
                      </p>
                    </li>
                    <li class="mt-4 flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Advanced organization tools
                      </p>
                    </li>
                    <li class="mt-4 flex items-start">
                      <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </div>
                      <p class="ml-3 text-base leading-6 font-medium text-gray-500">
                        Designed for remote work
                      </p>
                    </li>
                  </ul>
                  <div class="mt-8">
                    <div class="rounded-lg shadow-md">
                      <a href="/request-demo" class="block w-full text-center rounded-lg border border-transparent bg-white px-6 py-3 text-base leading-6 font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:shadow-outline transition ease-in-out duration-150" aria-describedby="tier-scale">
                        Start your trial
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
