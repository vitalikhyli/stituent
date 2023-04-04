<div class="flex py-2">
    
    <div class="w-1/2 pr-2">

    	<div class="font-light text-xl border-b-4 pb-2">
    		Person to Keep

    		<div class="text-grey-dark font-normal text-sm">
    			Add data from person to remove
    		</div>

    	</div>

    	<div class="py-2">

        	@include('livewire.merge._person-column', ['person' => $keep])

        </div>

    </div>

    <div class="w-1/2 pl-2">

    	<div class="font-light text-xl border-b-4 border-red pb-2">
    		Person to Remove

    		<div class="text-grey-dark font-normal text-sm">
    			Merge this data into the other person to keep
    		</div>

    	</div>

		<div class="py-2">
        	@include('livewire.merge._person-column', ['person' => $remove])
        </div>

    </div>

</div>
