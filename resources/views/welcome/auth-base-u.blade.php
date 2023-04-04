@extends('blank')

@section('title')
    
@endsection

@section('style')

<link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" /> -->

<style>

	@keyframes rise {
	  0% {
	    /*margin-top:25px;*/
	    opacity:0;
	  }
	  100% {
	    /*margin-top:0px;*/
	    opacity:100;
	  }
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

<!-- 	<div class="bg-cover bg-blue-darker w-full font-sans" style="background-image: url('https://images.pexels.com/photos/164338/pexels-photo-164338.jpeg');"> -->

	<div class="bg-cover bg-red-darker w-full font-sans" style="background-image: url('images/u-benches.jpg');">

		<div class="md:w-3/4 mx-auto relative">

			<div class="md:flex w-full shadow-lg border-white items-center p-6" style="background: rgba(100, 0, 0, 0.8);">

				<div class="md:w-1/2 text-left text-white h-10">

					<!-- <div class="float-right font-serif text-white text-lg inline md:hidden mt-2">
						(617) 699-4553
					</div> -->

					<div class="flex whitespace-no-wrap w-full h-10">
						
						<img class="w-12 " src="/images/cf_logo_white.svg" />

						<div class="flex text-xl pt-2 -ml-1">
							
							<span class="font-thin">community</span><span class="font-bold">fluency</span>
						</div>
					</div>

				</div>
				<div class="md:w-1/2 text-right text-grey">
					

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
					    <div class="invisible font-serif text-white text-lg md:visible inline mr-4">
							(617) 699-4553
						</div>


						<div class="block md:hidden"></div>
						

						<a href="#" data-target="#human-modal" data-toggle="modal">
							<button class="bg-blue-dark text-grey-lighter hover:text-white rounded-sm px-5 py-2 hover:bg-white hover:text-red-dark whitespace-no-wrap border-grey border rounded shadow text-sm">
								Try It Now - No Login Required
							</button>
						</a>
						
					@endif
					
				</div>

			</div>

			<div class="md:h-8"></div>

			<div class="w-full md:flex items-center">

				<div class="md:w-1/2 md:pr-16">
					
					<div class="border-b border-white text-3xl sm:text-5xl text-red-darker font-bold leading-tight relative p-6 shadow" style="background: rgba(255, 255, 255, 0.9);animation:rise 1s;">


						<div class="text-red-darkest">
							Community Relations Software
							<span class="md:text-3xl text-xl font-serif italic">for</span>
								<span class="text-red-darker">Colleges and Universities</span>
						</div>


						<div class="text-base text-red-darker mt-4 leading-loose font-normal">
							Community Fluency is a team-centered online contact and casework database for the <b>external relations and community relations departments</b> who need to communicate with residents, officials, local partners and community groups.

							<span class="ml-2">
								<a href="" class="text-blue-dark font-bold">Learn More <i class="fas fa-arrow-right ml-1"></i></a>
							</span>

						</div>

					</div>




				</div>

				<div class="md:w-1/2 md:p-8 pr-0 mt-8 md:mt-0" x-data="{ open: false }">

					<div class="relative w-full flex items-center cursor-pointer text-grey-light hover:text-white h-16 md:h-64" @click="open = true">

						

						<div class="absolute z-10 text-center w-full" >
							<div class="py-3 px-2 mx-auto w-1/2 md:w-1/2" style="background: black;">
								<i class="fa fa-play text-xl"></i> &nbsp;WATCH 2-MIN DEMO
							</div>
						</div>

						<div class="absolute opacity-50 w-full md:rounded-lg h-24 md:h-64" style="background: black;">
							<!-- <iframe src="https://player.vimeo.com/video/369723722" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen class="w-full" id="video"></iframe> -->
						</div>

					</div>

					    <template x-if="open">
					    	<div class="fixed pin z-50 overflow-auto pt-16 md:flex items-center" style="background: rgba(0, 0, 0, 0.75);">
					    	
						    	<div class="absolute pin-t pin-r text-white z-100 m-8 cursor-pointer" @click="open=false">
									<i class="fa fa-close fa-3x"></i>
						    	</div>

								<iframe src="https://player.vimeo.com/video/369723722" width="75%" height="75%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen class="rounded-lg mx-auto" id="video"></iframe>
							</div>
						</template>


				</div>

			</div>

			<div class="h-8"></div>


		</div>




		<div class="w-full overflow-none h-32" style="background: bottom no-repeat url(/images/swoop.png); background-size: 100%;">
	        
	    </div>
	</div>


	<div class="w-full" style="margin-top: -120px;">
		<div class="mx-auto bg-white md:flex shadow-lg md:rounded-lg bg-grey-lighter w-3/4 md:w-1/2 w-full">

			@if (Auth::user())
		        @include('welcome.user-logged-in')
		    @else
				@yield('auth')
			@endif

		</div>
	</div>

	
@if(env('APP_ENV') != 'production')	

<div class="pt-20 text-center w-full flex flex-wrap" style="background: 125% top no-repeat url(/images/cloud.png);">

	<div class="md:w-1/6">
	</div>

	<div class="md:w-1/3 w-full md:mr-6">

		<div class="md:rounded-t-lg w-1/2 inline shadow-lg">

			<div class="md:rounded-t-lg font-bold bg-red-darker text-white p-2 text-3xl fancy">
				$625 / month
			</div>

			<div class="p-2" style="background: rgba(255, 255, 255, 0.7);">
				
				<div class="text-red-darker p-2 text-3xl font-black uppercase mb-4">
					Essential
				</div>

				<div class="text-left text-lg px-4 text-grey-dark">
			       
			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Up to 5 Users
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>

			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Cases and Contacts
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>

			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Community Benefits and PILOTs
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>



				</div>

			</div>
			
		</div>

	</div>

	<div class="md:w-1/3 w-full md:ml-6 pb-10">

		<div class="md:rounded-t-lg w-1/2 inline">


			<div class="md:rounded-t-lg font-bold bg-red-darker text-white p-2 text-3xl fancy">
				$825 / month
			</div>

			<div class="p-2 shadow-lg" style="background: rgba(255, 255, 255, 0.7);">
				
				<div class="text-red-darker p-2 text-3xl font-black uppercase mb-2">
					Enterprise 
				</div>

<!--                 <div class="text-sm text-black text-left mb-6 px-4">
                    Everything from Essential, and more:
                </div> -->

				<div class="text-left text-lg px-4 text-grey-dark">

			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Unlimited Users
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>

			       
			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Customization
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>


			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Email Blasts
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>

			        <div class="flex mb-4">
			            <i class="fa fa-plus text-green text-lg pr-2 text-xl"></i>
			            <div>
			                <div class="text-black font-bold">
			                    Party Registration
			                </div>
			                <div class="text-sm text-grey-dark">
			                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			                </div>
			            </div>
			        </div>

				</div>

			</div>

		</div>

	</div>

	<div class="md:w-1/6">
	</div>

</div>
@endif


	<div class="py-4 border-t-8 border-black bg-red-darker text-grey-light md:flex items-center">

		<div class="md:w-3/4  inline-flex mx-auto items-center text-sm md:text-base">


		    <div class="w-1/2 px-8 md:w-1/3 align-top inline-block">
		        <div class="font-medium uppercase text-sm text-red-lighter">
		        	Community Fluency
		        </div>
		        P.O. Box 8703<br>
		        Boston, MA 02114
		    </div>

		    <div class="py-8 invisible md:visible w-0 md:w-1/3 text-center align-top inline-block">

		    	<div class="font-medium uppercase text-sm text-red-lighter">
		    		We Also Provide Tools for:
		    	</div>

		      	<div class="">
		      		<a href="/" class="hover:text-white">
		      			Constituent Service
		      		</a> |
		      		<a href="/campaigns" class="hover:text-white">
		      			Campaigns
			      	</a> 
			      	<!-- |
		      		<a href="/" class="hover:text-white">
		      			Nonprofits
			      	</a> -->
			     </div>

		    </div>
		  

		     <div class="w-1/2 text-right px-8 md:w-1/3 align-top inline-block">
		        <div class="font-medium uppercase text-sm text-red-lighter">Contact Us:</div>

		        <div>
		        	<a class="no-underline hover:text-white" href="mailto:laz@communityfluency.com">
		        		Lazarus Morrison - Owner
		        	</a>
		        </div>

		        <div>
		        	<a class="no-underline hover:text-white" href="mailto:peri@communityfluency.com">
		        		Peri O'Connor - Manager
		        	</a>
		    	</div>
		        
		    </div>

		</div>

		<div class="pb-8 block md:hidden">
	        <div class="font-light text-xl tracking-wide text-center">
	            &copy; {{ config('app.name') }} {{ date('Y') }}
	        </div>
	    </div>
		
	</div>

</div>


@include('welcome.human-modal')


@endsection

@section('javascript')

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.9.2/dist/alpine.js" defer></script>

	<script type="text/javascript">
		
		document.getElementById("email").focus();
		
		$(document).ready(function() {
			
			$('#human-modal').on('shown.bs.modal', function () {
			    $('#answer').focus();
			})  

		});

	</script>

@endsection


