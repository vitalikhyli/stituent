@extends('blank')

@section('title')
    
@endsection

@section('style')

<link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">

<link href="/css/main.css" rel="stylesheet" type="text/css">

<style>

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

			<div class="md:flex w-full border-grey-darkest items-center py-6">

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
						<!-- <a href="#pricing" class="hover:text-white">
							Pricing
						</a> -->
						

					</div>

					@if (Auth::user())
				        <!-- Logged in as <b>{{ Auth::user()->name }}</b> -->
				        <a class="text-grey hover:text-white no-underline border-transparent px-6 py-3 rounded-full text-sm tracking-wide" href="{{ route('logout') }}"
	                       onclick="event.preventDefault();
	                                     document.getElementById('logout-form').submit();">
	                        {{ __('Logout') }}
	                    </a>
	                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
	                        @csrf
	                    </form>
<!-- 				        <a class="ml-2 px-3 py-2 border rounded-full hover:text-white whitespace-no-wrap" href="/{{ Auth::user()->team->app_type }}">
				        	Go to Dashboard <i class="fa fa-arrow-right"></i>
				        </a> -->
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
						<!-- <a style="" class="ml-8 bg-blue text-grey-lighter hover:text-white rounded-sm px-5 py-2 shadow-sm border-b border-blue-dark hover:bg-blue-dark whitespace-no-wrap" href="/request-demo">
							Request Demos
						</a> -->
						
					@endif
					
				</div>

			</div>

			<div class="h-32"></div>


		</div>




		<div class="w-full overflow-none h-32" style="background: bottom no-repeat url(/images/swoop.png); background-size: 100%;">
	        
	    </div>
	</div>

	<div class="w-full" style="margin-top: -150px;">
		<div class="mx-auto bg-white md:flex items-center shadow-lg rounded-lg bg-grey-lighter w-3/4 md:w-1/2">

			@if (Auth::user())
		        @include('welcome.user-logged-in')
		    @else
				@yield('auth')
			@endif

		</div>
	</div>



	<!-- include('welcome.office-pricing') -->

	<div class="text-center" style="background: 125% top no-repeat url(/images/cloud.png);">

		<div class="font-black text-black mt-16 text-center text-4xl">
			By The Numbers
		</div>
		<div class="mx-auto w-1/5 text-grey-dark text-center text-lg border-b-2 border-grey pb-2 tracking-wide">
			Community Fluency since 2005
		</div>

		<div class="h-4 border-r-2 mx-auto border-grey w-1"></div>

		<div class="flex">
			<div class="w-1/2"></div>
			<div class="w-1/2 border-l-2 border-grey">

				<div class="border-b-2 border-grey w-3/4 text-right">
					<div class="fancy font-black text-6xl">
						Fifteen
					</div>
					<div class="text-grey-dark text-xl -mt-3">
						Years
					</div>
				</div>
			</div>
		</div>

		<div class="flex">
			<div class="w-1/6"></div>
			<div class="w-1/3" style="margin-right: 1px;">

				<div class="border-b-2 border-grey w-full text-left">
					<div class="fancy font-black text-6xl text-grey-darker">
						<!-- 7,136,865 -->
						<span class="invisible absolute md:relative md:visible">
							Seven million +
						</span>
						<span class="block md:hidden">
							7m+
						</span>
					</div>
					<div class="text-grey-dark text-lg -mt-3">
						<div class="flex items-center">
							
							<div>Registered Voters w/ Geocodes</div>
							<div class="md:ml-2 mr-2 md:mr-0">
								<i class="fa fa-map-marker fa-2x text-blue -pb-4"></i>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="w-1/2 border-l-2 border-grey"></div>
		</div>

		<div class="flex">
			<div class="w-1/2"></div>
			<div class="w-1/2 border-l-2 border-grey">

				<div class="border-b-2 border-grey w-2/3 text-right">
					<span class="fancy text-5xl invisible absolute md:relative md:visible">
						Sixty-eight million
					</span>
					<span class="fancy text-4xl block md:hidden">
						68 million
					</span>
					<div class="text-grey-dark text-lg -mt-3">
						Election Records
					</div>
				</div>
			</div>
		</div>

		<div class="flex">
			<div class="w-1/6 md:w-1/4"></div>
			<div class="w-1/3 md:w-1/4" style="margin-right: 1px;">

				<div class="border-b-2 border-grey w-full text-left">
					<div class="fancy font-black text-5xl text-grey-darker">
						<i>79,387</i>
					</div>
					<div class="text-grey-dark text-lg -mt-3">
						Constituent Cases Handled
					</div>
				</div>
			</div>
			<div class="w-1/2 border-l-2 border-grey"></div>
		</div>

		<div class="flex">
			<div class="w-1/2"></div>
			<div class="w-1/2 border-l-2 border-grey">

				<div class="border-b-2 border-grey w-1/2 text-right">
					<div class="fancy font-black text-5xl">
						<i>384,173</i>
					</div>
					<div class="text-grey-dark text-lg -mt-3">
						Contacts Logged
					</div>
				</div>
			</div>
		</div>

		<div class="flex">
			<div class="w-1/6 md:w-1/3"></div>
			<div class="w-1/3 md:w-1/6" style="margin-right: 1px;">

				<div class="border-b-2 border-grey w-full text-left">
					<div class="fancy font-bold text-3xl text-blue whitespace-no-wrap -ml-8 md:-ml-0">
						(617) 699-4553
					</div>
					<div class="text-grey-dark text-lg -mt-2">
						The number to call!
					</div>
				</div>
			</div>
			<div class="w-1/2 border-l-2 border-grey"></div>
		</div>

		<div class="h-24 border-r-2 mx-auto border-grey w-1"></div>

		<div class="mt-8 text-grey-darker text-center w-full" style="font-size: 6rem;">
			<div class="text-center w-full mx-auto">
				<span class="md:flex-1 text-black fancy font-bold py-6" 
						    style="background: no-repeat bottom url(/images/gold-underline-1.png); background-size: 100%;">
						hundreds
				</span> 
				<span class="md:flex-1 block md:inline" style="font-size: 3rem;">of elections won.</span>
			</div>
			
			<div class="text-center w-full mx-auto">
				<span class="md:flex-1 text-black fancy font-bold py-6" 
						    style="background: no-repeat bottom url(/images/gold-underline-2.png); background-size: 100%;">
						thousands
				</span> 
				<span class="md:flex-1 block md:inline" style="font-size: 3rem;">of constituents served.</span>
			</div>
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
		        P.O. Box 8703<br>
		        Boston, MA 02114
		    </div>

		    <div class="py-8 invisible md:visible w-0 md:w-1/3">
		        <div class="font-light text-xl tracking-wide text-center">
		            &copy; {{ config('app.name') }} {{ date('Y') }}
		        </div>
		    </div>
		  

		     <div class="w-1/2 text-right px-8 md:w-1/3">
		        Contact Us:<br>
		        <a class="no-underline hover:text-white" href="mailto:laz@communityfluency.com"><span class="font-semibold">Lazarus Morrison</span> - Owner</a><br>
		        <a class="no-underline hover:text-white" href="mailto:peri@communityfluency.com"><span class="font-semibold">Peri O'Connor</span> -  Manager</a><br>
		        
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

	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.9.2/dist/alpine.js" defer></script>

	<script type="text/javascript">
		
		document.getElementById("email").focus();
		document.getElementById("name").focus();

	</script>

@endsection


