<div class="w-full" x-data="{ switchOn: false }">

	<label for="{{ $property }}"
			x-on:click="switchOn = !switchOn"
			class="w-full font-normal">

		<input type="checkbox"
			   wire:model="{{ $property }}"
			   id="{{ $property }}"
			   class="hidden" />
	

	<div x-show="switchOn">


		<div class="text-right rounded-full border-2 w-full bg-blue border-blue">
			<div class="inline rounded-full h-8 w-8 px-2 py-1 bg-white border-blue border-2 cursor-pointer">
			</div>
		</div>

	</div>

	<div x-show="!switchOn">

		<div class="text-left rounded-full border-2 w-full bg-grey-light">
			<div class="inline rounded-full h-8 w-8 px-2 py-1 bg-white border-2 cursor-pointer">
			</div>
		</div>

	</div>

	</label>
		

</div>



<!-- https://github.com/livewire/livewire/issues/1505 -->
<!-- 
<div class="w-full"
	 x-data="{ switchOn: @entangle($property) }"
	 >

	<div x-show="switchOn" @click="switchOn = false">

		<div class="text-right rounded-full border-2 w-full bg-blue border-blue">

			<button class="rounded-full h-8 w-8 px-2 py-1 bg-white border-blue border-2 cursor-pointer">
			</button>

		</div>

	</div>

	<div x-show="!switchOn" @click="switchOn = true">

		<div class="text-left rounded-full border-2 w-full bg-grey-light">

			<button class="rounded-full h-8 w-8 px-2 py-1 bg-white border-2 cursor-pointer">
			</button>

		</div>

	</div>
		
</div> -->

