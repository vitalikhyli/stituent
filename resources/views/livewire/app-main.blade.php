<div class="bg-gradient-to-tr from-blue-500 via-purple-700 to-blue-900 overflow-hidden">

	<div class="lg:flex items-center h-screen">

		<div class="lg:w-1/2 min-h-128 text-white font-sans text-left mx-auto">
			<div class="mx-auto w-full lg:w-3/4 p-8">

				<div class="p-2 font-bold text-3xl uppercase">
					Register your device
				</div>
				<div class="p-2 border
							@if ($on_phone)
								border-transparent opacity-80
							@else
								border-purple-400
							@endif
							">
					<div class="flex items-center">
						<div class="pr-2 text-2xl">
							@if ($on_phone)
								<i class="fa fa-check-circle"></i>
							@else
								<i class="fa fa-arrow-right"></i>
							@endif
							
						</div>
						<div class="p-2">
							Step 1: <b>Download</b> the new Community Fluency App
						</div>
					</div>
					<div class="ml-12 text-sm">
						<a class="mr-2" href="">iPhone Store</a>
						
						<a class="mr-2" href="">Android Store</a>
						
						
					</div>
				</div>
				<div class="p-2">
					Step 2: <b>Log in here</b> with your credentials
				</div>
				<div class="p-2">
					Step 3: Enter the <b>5-digit code</b> into your phone
				</div>
			</div>
		</div>

		<div class="lg:w-1/2 pt-8 lg:pr-32">

			
			<div class="h-48 lg:h-1"></div>
			<div class="text-white w-72 mx-auto relative" style="height:600px;">

				

				<div class="opacity-60 bg-gradient-to-bl from-blue-900 to-blue-800 absolute -ml-10 -mt-9 border-2 border-red" style="width:350px; height:670px;">
					
				</div>

				<div class="absolute -ml-28 -mr-72 -mt-16">
					<img class="" src="/images/phone-hand-transparent.png" />
				</div>



				<div class="absolute font-sans w-full text-center">

					<div class="mb-8 text-sm border-b border-gray-500 mx-2 pb-1">
						Community Fluency Device Registration
					</div>

					<div class="
								@if ($on_phone)
									opacity-50
								@endif
								">

						<div class="mx-auto font-bold border-4 rounded-full h-12 w-12 pt-1 text-2xl">
							
							@if ($on_phone)
								<i class="absolute text-5xl fa fa-check text-white -mt-2 -ml-4"></i>
							@else
								1
							@endif
						</div>

						<div class="font-bold uppercase text-sm mt-2 mb-2">
							Download the App
						</div>

						@if (!$on_phone)

							<div class="text-center w-full">
								<button wire:click="onPhone()" class="rounded bg-blue-500 hover:bg-blue-600 text-white uppercase font-bold shadow px-4 py-2 w-48">
									Yes, Done
								</button>
							</div>
						@endif
					</div>

					<div class="
								@if (!$on_phone)
									opacity-20
								@elseif($approved)
									opacity-50
								@endif

							   ">

						<div class="mt-8 mx-auto font-bold border-4 rounded-full h-12 w-12 pt-1 text-2xl">
							@if ($approved)
								<i class="absolute text-5xl fa fa-check text-white -mt-2 -ml-4"></i>
							@else
								2
							@endif
						</div>

						<div class="font-bold uppercase text-sm mt-2 mb-4">
							Log In here
						</div>

						<div class="text-center text-gray-200">

							@if (Auth::user())
						    	Logged in as <span class="text-white font-bold">{{ Auth::user()->name }}</span>.

						    	<div class="my-2">
						    		{{ Auth::user()->account->name }}
						    	</div>
						    	
						    	@if ($approved)

									<button wire:click="logout()" class="rounded-full text-gray-200 font-bold px-3 py-2 text-xs">
										Not me - Logout
									</button>

								@else
									<div class="text-center w-full mt-4">
										<button wire:click="approved()" class="rounded bg-blue-500 hover:bg-blue-600 text-white uppercase font-bold shadow px-4 py-2 w-48">
											Yes, this is me
										</button>
										
									</div>

									<div class="text-xs mt-2 text-gray-300 hover:text-white">
							    		Not you? <span class="cursor-pointer" wire:click="logout()">Logout</span>
							    	</div>

								@endif
						    @else
						    	<input type="text" name="email" class="border bg-transparent px-2 py-2 text-white text-center rounded mb-2" wire:model="email" placeholder="Email" />
						    	<input type="password" name="password" class="border bg-transparent px-2 py-2 text-white text-center rounded" placeholder="Password" wire:model="password" />

						    	<button wire:click="login()" 
						    				@if (!$email || !$password)
							    				disabled class="rounded bg-gray-400 text-white uppercase font-bold shadow px-4 py-2 mt-2 w-48"
							    			@else
							    				class="rounded bg-blue-500 hover:bg-blue-600 text-white uppercase font-bold shadow px-4 py-2 mt-2 w-48"
							    			@endif
						    			>
									Login
								</button>

						    	@if ($attempted)
						    		<div class="text-red text-sm mt-2">
						    			Credentials not found.<br>
						    			Please try again.
						    		</div>
						    	@endif

						    @endif

						</div>
					</div>

					<div class="
							@if (!$on_phone)
								opacity-20
							@elseif (!$approved)
								opacity-20
							@endif
						   ">

						<div class="mt-8 mx-auto font-bold border-4 rounded-full h-12 w-12 pt-1 text-2xl">
							3
						</div>

						<div class="font-bold uppercase text-sm mt-2 mb-4">
							Enter on Phone:
						</div>

					</div>
					
				    
				</div>

			    

		    </div>

		</div>
    </div>
</div>
