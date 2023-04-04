<div>
		<div class="rounded-t-lg bg-blue-700 text-white px-4 py-2 md:w-1/4 text-center">
			Constituent Service Features
		</div>
		<div class="w-full flex flex-col justify-center items-center mb-10">
			  <div class="w-full mx-auto relative"
			       x-data="{ activeSlide: 1, slides: [1, 2, 3, 4] }"
			       >
			    <!-- Slides -->
			    <template x-for="slide in slides" :key="slide">
			      <div
			         x-show="activeSlide === slide"
			         class="flex items-center bg-black text-white">
			         
			        <!-- <span class="w-12 text-center" x-text="slide"></span> -->
			        <!-- <span class="text-teal-300">/</span> -->
			        <!-- <span class="w-12 text-center" x-text="slides.length"></span> -->

			        <!------------------------------[ SLIDE ]---------------------------------->
				    <div class="md:flex w-full"
				    	 :class="{ 
			              'block': activeSlide === 1,
			              'hidden': activeSlide !== 1 
			          	  }">
			        	<div class="md:w-3/5 md:p-8 md:px-12 p-6 px-10 text-white">
							<div class="mb-4 md:text-left text-center text-3xl sm:text-4xl font-bold leading-none">
								Maps
							</div>
							<div class="text-xl mt-2 text-grey-100">
								Every time you log a contact, add to a group, or start a case, it goes on your color-coded Activity Map. Watch your district take shape over time, and click any pin to see details.
							</div>
						</div>
			        	<div class="md:w-2/5 md:pl-4">
			        		<img src="/images/map-sample.png" class="" />
						</div>
			        </div>

			        <!------------------------------[ SLIDE ]---------------------------------->
				    <div class="md:flex w-full"
				    	 :class="{ 
			              'block': activeSlide === 2,
			              'hidden': activeSlide !== 2 
			          	  }">
			        	<div class="md:w-3/5 md:p-8 md:px-12 p-6 px-10 text-white">
							<div class="mb-4 md:text-left text-center text-3xl sm:text-4xl font-bold leading-none">
								Cases & Reports
							</div>
							<div class="text-xl mt-2 text-grey-100">
								Your staff works hard to enter data -- but what then? Now you can export lists, run case reports, see weekly call logs, or check how many total people your office has engaged with.
							</div>
						</div>
			        	<div class="md:w-2/5 md:pl-4">
			        		<img src="/images/export-sample.png" class="" />
						</div>
			        </div>

			        <!------------------------------[ SLIDE ]---------------------------------->
				    <div class="md:flex w-full"
				    	 :class="{ 
			              'block': activeSlide === 3,
			              'hidden': activeSlide !== 3 
			          	  }">
			        	<div class="md:w-3/5 md:p-8 md:px-12 p-6 px-10 text-white">
							<div class="mb-4 md:text-left text-center text-3xl sm:text-4xl font-bold leading-none">
								Shared Cases
							</div>
							<div class="text-xl mt-2 text-grey-100">
								Collaborate with other offices on constituent service. Resolve issues faster. Share contacts, notes, files and more.
							</div>
						</div>
			        	<div class="md:w-2/5 md:pl-4">
			        		<img src="/images/marketing/shared-cases.png" class="" />
						</div>
			        </div>

			        <!------------------------------[ SLIDE ]---------------------------------->
			        <div class="md:flex w-full"
				    	 :class="{ 
			              'block': activeSlide === 4,
			              'hidden': activeSlide !== 4 
			          	  }">
			        	<div class="md:w-3/5 md:p-8 md:px-12 p-6 px-10 text-white">
							<div class="mb-4 md:text-left text-center text-3xl sm:text-4xl font-bold leading-none">
								See the site in action
							</div>
							<div class="text-xl mt-2 text-grey-100">
								Want an overview of our features, and an idea of the workflow? Check out this video.<br><br>
								If you want something more personal, call Peri at 617.699.4553 and schedule a Live demo for you and your office!
							</div>
						</div>
			        	<div class="md:w-2/5 md:pl-4">
			        		<div class="m-4 rounded-lg bg-cover" x-data="{ open: false }" style="background-image:url(/images/dashboard-video.png)">

								<div class="relative w-full flex items-center cursor-pointer text-grey-light hover:text-white h-16 md:h-64" @click="open = true">


									<div class="absolute z-10 text-center w-full" >
										<div class="py-3 px-2 mx-auto w-3/4 md:w-1/2" style="background: black;">
											<i class="fa fa-play text-xl"></i> &nbsp;WATCH DEMO
										</div>
									</div>

									<div class="absolute opacity-25 w-full  h-24 md:h-64" style="background: black;">
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
			        </div>

			      </div>

			    </template>
			    
			    <!-- Prev/Next Arrows -->
			    <div class="absolute inset-0 flex">
			      <div class="flex items-center justify-start w-1/2">
			        <button 
			          class="bg-blue-100 text-blue-500 hover:text-blue-500 font-bold rounded-full w-12 h-12 -ml-6 shadow-lg"
			          x-on:click="activeSlide = activeSlide === 1 ? slides.length : activeSlide - 1">
			          &#8592;
			         </button>
			      </div>
			      <div class="flex items-center justify-end w-1/2">
			        <button 
			          class="bg-blue-100 text-blue-500 hover:text-blue-500 font-bold rounded-full w-12 h-12 -mr-6 shadow-lg"
			          x-on:click="activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1">
			          &#8594;
			        </button>
			      </div>        
			    </div>

			    <!-- Buttons -->
			    <div class="absolute w-full flex items-center justify-center px-4">
			      <template x-for="slide in slides" :key="slide">
			        <button
			          class="flex-initial w-4 h-4 mt-4 mx-2 mb-0 rounded-full overflow-hidden transition-colors duration-200 ease-out hover:bg-teal-600 hover:shadow-lg"
			          :class="{ 
			              'bg-blue-600': activeSlide === slide,
			              'bg-blue-300': activeSlide !== slide 
			          }" 
			          x-on:click="activeSlide = slide"
			        ></button>
			      </template>
			    </div>

			</div>
			  
		</div>

		</div>