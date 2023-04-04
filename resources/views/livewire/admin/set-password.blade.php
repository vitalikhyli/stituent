<div class="flex">

	<div class="p-1">

	    <button wire:click="generatePassword()" type="button" class="rounded-lg bg-grey-lighter text-grey-darkest px-2 py-1 whitespace-no-wrap">
	    	{{ ($proposed_password) ? 'Do It Again' : 'Generate Password' }}
	    </button>

	</div>


    <div>

	    @if($changed)

			<div class="p-1">

				<i class="fas fa-check-circle text-red"></i>

				Changed to
				<input value="{{ $proposed_password }}" type="text" class="border-b border-blue p-1 font-medium font-mono text-blue" id="the_password" size="20"/>

<!-- 	   			<button onclick="copyToClipboard()" type="button" class="mr-1 rounded-lg bg-blue text-white px-2 py-1 font-normal my-1 px-3">
	    			<i class="fas fa-clipboard"></i> Copy
	    		</button> -->

	    	</div>
	

	    @elseif($proposed_password)

			<div wire:loading.remove class="p-1">

			    <button wire:click="setPassword()" type="button" class="rounded-lg bg-blue text-white px-2 py-1">
			    	Set As:
			    </button>

			    <input wire:model="proposed_password" type="text" class="border p-1 font-medium font-mono" size="20" />

			</div>

	    @endif

	</div>

</div>
