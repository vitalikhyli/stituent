@extends('campaign.ostrich.shell')

@section('title')
  
  Log In

@endsection

@section('header')

	Log In

@endsection

@section('main')

<div class="mb-4">

	@if($errors)

		@foreach($errors as $error)

			<div class="text-white bg-red-600 px-4 py-2">
				{{ $error }}
			</div>

		@endforeach

	@endif

<div class="min-h-screen flex items-center justify-center bg-black py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-sm w-full space-y-8 p-10 border border-gray-700">
    <div>

      <a href="/ostrich">

      	<div class="text-center">
            <img class="h-8 w-auto sm:h-10 -mt-1 inline" src="/images/cf_logo_white.svg" alt="" />
            <div class=" -ml-3 mt-2 inline text-white text-xl">
                <span class="ml-2 font-thin tracking-wide">campaign</span><span class="font-bold">fluency</span>
            </div>
        </div>

    	  <div class="text-center text-4xl font-thin mt-2 text-gray-700">
            O S T R I C H
        </div>

      </a>

    </div>

    @if(session('link_sent') !== null)
	    <div class="text-gray-800 bg-yellow-200 rounded px-4 py-2 text-center">
	    	Link has been sent!
	    </div>
    @endif

    <form class="mt-8 space-y-6 " action="/ostrich/send-link" method="POST">

    	@csrf

      <!-- <input type="hidden" name="remember" value="true"> -->

      <div class="rounded-md shadow-sm -space-y-px">
        <div>
          <label for="email-address" class="sr-only">Email address</label>
          <input id="email-address" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
        </div>
      </div>

      <div class="hidden rounded-md shadow-sm -space-y-px">
        <div>
          <label for="name" class="sr-only">Name</label>
          <input id="name" name="name" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
        </div>
      </div>      

<!--       <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
          <label for="remember_me" class="ml-2 block text-sm text-gray-900">
            Remember me
          </label>
        </div>

        <div class="text-sm">
          <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
            Forgot your password?
          </a>
        </div>
      </div> -->

      <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <!-- Heroicon name: solid/lock-closed -->
            <svg class="h-5 w-5 text-gray-100 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
          </span>
          Send Login Link
        </button>
      </div>
    </form>
  </div>
</div>


</div>


@endsection('content')