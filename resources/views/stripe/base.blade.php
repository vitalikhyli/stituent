@extends('blank')

@section('title')
    
@endsection

@section('style')

	<link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">

	<link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">

	@livewireStyles

	<style>

		.handwriting {
			font-family: 'Architects Daughter', cursive;
		}
		.fancy {
			font-family: 'Dancing Script', cursive;
		}
		#statehouse {
			background-size: 250%;
			background-position: bottom;
			background-repeat: no-repeat;
			background-image: url(/images/statehouse.svg);
			height: 450px;
		}
		@media (min-width: 500px) { 

			#statehouse {
				background-size: 210%;
				height: 500px;
			}
		}
		@media (min-width: 768px) { 

			#statehouse {
				background-size: 180%;
				height: 600px;
			}
			#login-left {
				width: 55%;
			}
			#login-right {
				width: 45%;
			}
		}
		@media (min-width: 1280px) { 

			#statehouse {
				background-size: 120%;
				height: 750px;
			}
		}
		@media (min-width: 1440px) { 

			#statehouse {
				background-size: 120%;
				height: 900px;
			}
		}

	</style>
	
@endsection

@section('main')


<div class="bg-grey-light">

	<div class="bg-cover bg-blue-darker w-full font-sans" style="background-image: url(/images/bg-cover.jpg);">

		<div class="w-3/4 mx-auto relative">

			<div class="md:flex w-full border-b border-grey-darkest items-center py-6">

				<div class="md:w-1/3 text-left text-white h-10">

					<div class="float-right font-serif text-grey-dark text-lg inline md:hidden mt-2">
						(617) 699-4553
					</div>

					<a href="/" class="flex whitespace-no-wrap w-full h-10 hover:text-white">
						
						<img class="w-12 " src="/images/cf_logo_white.svg" />

						<div class="flex text-xl pt-2 -ml-1">
							
							<span class="font-thin">community</span><span class="font-bold">fluency</span>
						</div>
					</a>

					

				</div>
				<div class="md:w-2/3 text-right text-grey">
					
					<div class="float-left uppercase tracking-wide pt-1">
						<a href="https://app.communityfluency.com/#pricing" class="hover:text-white">
							Pricing
						</a>
						<a href="https://campaignfluency.com" class="hidden ml-8 hover:text-white">
							Campaigns
						</a>
					</div>

					@if (Auth::user())
				        Logged in as <b>{{ Auth::user()->name }}</b>
				        <a class="text-grey hover:text-white no-underline border-transparent px-6 py-3 rounded-full text-xs tracking-wide" href="{{ route('logout') }}"
	                       onclick="event.preventDefault();
	                                     document.getElementById('logout-form').submit();">
	                        {{ __('Logout') }}
	                    </a>
	                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
	                        @csrf
	                    </form>
				        <a class="ml-2 px-3 py-2 border rounded-full hover:text-white" href="/{{ Auth::user()->team->app_type }}">
				        	Go to Dashboard <i class="fa fa-arrow-right"></i>
				        </a>
				    @else
					    <div class="invisible font-serif text-grey-dark text-lg md:visible inline mr-4">
							(617) 699-4553
						</div>
						<!-- <a class="mx-6 hover:text-white" href="">
							Universities
						</a> -->

						<div class="block md:hidden"></div>
						
						<!-- <a class="float-left mx-6 hover:text-white" href="/#login" onclick="document.getElementById('email').focus();">
							Login
						</a> -->
						<a style="" class="ml-8 bg-blue text-grey-lighter hover:text-white rounded-sm px-5 py-2 shadow-sm border-b border-blue-dark hover:bg-blue-dark whitespace-no-wrap" href="/request-demo">
							Request Demos
						</a>
						
					@endif
					
				</div>

			</div>

			<div class="h-8"></div>
			@isset($account)

				<div class="text-center text-white">
					<div class="text-4xl text-white text-center p-4 font-bold">

						{{ $account->name }} Billing

					</div>
					<div class="text-sm -mt-4 mb-4 text-gray-500">
						Provided securely by Community Fluency and Stripe
					</div>
				</div>

			@endisset


		</div>




		<div class="w-full overflow-none h-32" style="background: bottom no-repeat url(/images/swoop.png); background-size: 100%;">
	        
	    </div>
	</div>

	<div class="w-full" style="margin-top: -120px;">
		<div class="mx-auto bg-white md:flex items-center shadow-lg rounded-lg bg-grey-lighter w-3/4 md:w-3/5">

			@yield('content')

		</div>
	</div>

	


	<div id="statehouse" class="w-full relative mt-8">

		<div id="crowd" class="absolute pin-b w-full" style="height: 100px; background:transparent url(/images/crowd-strip.png) no-repeat">
				<!-- <img src="/images/crowd-strip.png" class="absolute w-full" /> -->
			</div>
		
	</div>

	<div class="md:h-32 border-t-8 border-black text-grey-light md:flex items-center" style="background-image: url(/images/bg-cover.jpg); background-position: 50% 30%;">

		<div class="md:w-3/4 flex mx-auto items-center text-sm md:text-base">


		    <div class="w-1/2 px-8 md:w-1/3">
		        <span class="font-semibold">Community Fluency</span><br>
		        25 Spruce Dr<br>
		        Ashburnham, MA 01430
		    </div>

		    <div class="py-8 invisible md:visible w-0 md:w-1/3">
		        <div class="font-light text-xl tracking-wide text-center">
		            &copy; {{ config('app.name') }} {{ date('Y') }}
		        </div>
		    </div>
		  

		     <div class="w-1/2 text-right px-8 md:w-1/3">
		        Contact Us:<br>
		        <a class="no-underline hover:text-white" href="mailto:laz@communityfluency.com"><span class="font-semibold">Lazarus Morrison</span> - Owner</a><br>
		        <a class="no-underline hover:text-white" href="mailto:peri@communityfluency.com"><span class="font-semibold">Peri O'Connor</span> -  Partner</a><br>
		        
		    </div>

		    
		</div>

		<div class="pb-8 block md:hidden">
	        <div class="font-light text-xl tracking-wide text-center">
	            &copy; {{ config('app.name') }} {{ date('Y') }}
	        </div>
	    </div>
		
	</div>

</div>



@endsection

@section('javascript')

	@livewireScripts

	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.9.2/dist/alpine.js" defer></script>


@endsection


