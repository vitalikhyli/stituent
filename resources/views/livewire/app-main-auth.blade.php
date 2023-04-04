<div class="p-8 text-center">
	@if (Auth::check())

		<div class="text-xs font-bold mb-8">
			Enter this pin on your device:
		</div>

		<div class="text-7xl font-bold tracking-widest">
			{{ Auth::user()->current_app_pin }}
		</div>

		

		<div class="text-sm mt-8">
			You are logged in as <b>{{ Auth::user()->name }}</b>
			<div class="uppercase font-bold">
				{{ Auth::user()->account->name }}
			</div>
			<div class="mt-4 text-gray-200">
				Not you? 
				<span class="underline cursor-pointer" wire:click="logout()">Logout</span>
			</div>
		</div>

	@else

		<div class="text-xs font-bold mb-8">
			Log in to get your pin:
		</div>

		<input type="text" name="email" class="border bg-transparent px-2 py-2 text-white text-center rounded mb-2" wire:model="email" placeholder="Email" />
    	<input type="password" name="password" class="border bg-transparent px-2 py-2 text-white text-center rounded" placeholder="Password" wire:model="password" />

    	<button wire:click="login()" 
    				@if (!$email || !$password)
	    				disabled class="rounded bg-gray-400 text-white uppercase font-bold shadow px-4 py-2 mt-2 w-48"
	    			@else
	    				class="rounded bg-white hover:text-blue-600 text-blue-500 uppercase font-bold shadow px-4 py-2 mt-2 w-48"
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