<div class="flex">

	<div class="text-2xl pt-6 pr-4 text-grey-dark font-bold w-48">
		<div class="border-r-4">
			1. Account
		</div>
	</div>

	<div class="p-6">

    	<button wire:click="accountModeNew()"
    			class="rounded-lg {{ ($account_mode == 'new') ? 'bg-blue text-white' : 'bg-grey text-grey-lightest' }} px-4 py-2">
    		New Account
    	</button>

    	<button wire:click="accountModeExisting()"
    			class="rounded-lg {{ ($account_mode == 'existing') ? 'bg-blue text-white' : 'bg-grey text-grey-lightest' }} px-4 py-2">
    		Existing Account
    	</button>

    	@if($account_mode == 'existing' || $account_id)

			<div class="mt-2">

    			<div class="py-2">

    				Existing Account:

    				<select wire:model="account_id">

						<option value="">
							-- CHOOSE ACCOUNT --
						</option>
						
    					@foreach($available_accounts as $account_option)

    						<option value="{{ $account_option->id }}">
    							@if($account_option->state)
    								{{ strtoupper($account_option->state) }} | 
    							@else
    								-- | 
    							@endif
    							{{ $account_option->name }}
    						</option>

    					@endforeach

    				</select>

				</div>

			</div>

    	@endif

    	@if($account_mode == 'new')

    		<div class="mt-2">

    			<div class="py-2">

    				New Account:

    				<input type="text"
    					   wire:model="new_account_name"
    					   class="border p-2 w-64" />

    				State:

    				<input type="text"
    					   wire:model="new_account_state"
    					   class="border p-2 w-32" />

    			</div>

    			@if($new_account_name || $new_account_state)

	    			<div class="p-2 px-4 border-2 flex bg-grey-lightest">

	    				<div class="py-1 text-xl">
	    					<span class="font-bold">{{ $new_account_name }}</span>
	    					@if($new_account_state)
	    						in <span class="font-bold">{{ $new_account_state }}</span>
	    					@endif
	    				</div>

						@if($new_account_name && $new_account_state)
		    				<div class="flex-grow text-right pt-2">
						    	<button wire:click="createAccount()"
						    			class="rounded-lg bg-blue text-white px-2 py-1 text-sm ml-2">
						    		Save + Continue
						    	</button>
						    </div>
						@endif

	    			</div>

    			@endif

    		</div>

    	@endif


	    @if($account)

	    	<div class="mt-4 text-xl font-bold text-blue">

	    		<i class="fas fa-check-circle"></i> {{ $account->name }}

	    	</div>

	    @endif


    </div>

</div>