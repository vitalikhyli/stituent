@extends('app')

@push('styles')

<style>

    #main-bg {
       background: rgb(255,255,255);
background: linear-gradient(353deg, rgba(255,255,255,1) 0%, rgba(200,205,230,1) 73%, rgba(209,215,245,1) 100%);
}

  [x-cloak] {
      display: none;
  }


</style>

@endpush


@section('content')

<div>
  <nav class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <a href="/candidates">
                <img class="h-10 w-10" src="/images/logo-2.svg" alt="Logo"> 
            </a>
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">

              @include('nav')

            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center md:ml-6">

<!--             <button class="p-1 border-2 border-transparent text-gray-400 rounded-full hover:text-white focus:outline-none focus:text-white focus:bg-gray-700" aria-label="Notifications">
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
              </svg>
            </button> -->

            <!-- Profile dropdown -->
            <div class="ml-3 relative">


              <div class="flex">

                <div class="p-4 pt-6 text-gray-200">
                    Welcome,
                    @if(Auth::check())
                        {{ Auth::user()->nameOrEmail }}
                    @else
                        {{ Magic_Email()->user->nameOrEmail }}!
                    @endif
                </div>
                <div class="p-4">
                    <a href="/logout">
                        <button class="rounded-lg bg-gray-700 text-gray-100 px-4 py-1">Log Out</button>
                    </a>
                </div>

              </div>
              <!--
                Profile dropdown panel, show/hide based on dropdown state.

                Entering: "transition ease-out duration-100"
                  From: "transform opacity-0 scale-95"
                  To: "transform opacity-100 scale-100"
                Leaving: "transition ease-in duration-75"
                  From: "transform opacity-100 scale-100"
                  To: "transform opacity-0 scale-95"
              -->

            </div>
          </div>
        </div>

        @include('nav-mobile')



      </div>
    </div>
 
     </nav>

  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="leading-tight text-gray-900">
        @yield('breadcrumb')
      </h1>
    </div>
  </header>
  <main>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Replace with your content -->
      @yield('main')
      <!-- /End replace -->
    </div>
  </main>
</div>


@endsection