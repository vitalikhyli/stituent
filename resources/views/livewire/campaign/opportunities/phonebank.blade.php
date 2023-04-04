<div>

	<div class="text-2xl font-bold border-b-4 pb-2">
		<span class="text-blue capitalize">
			{{ $opp->type }} /
		</span>
		{{ $opp->name }}
	</div>

	<!------------------------------------------/ /-------------------------------------->

	<div class="mt-2">

		<div class="text-xl font-bold">
			Basic Info

			@if($opp->name && $opp->starts_at)
				<i class="fas fa-check-circle text-blue ml-1 text-base"></i>
			@endif

		</div>

		<div class="pl-8 border-l-4">
		
			<div class="flex mt-2">

				<div class="w-24 pr-2 pt-1">
					Name:
				</div>

				<div class="pr-2 w-3/5">
					<input type="text"
						   class="border-2 bg-white p-2 w-full"
						   placeholder="Name"
						   wire:model.debounce="name"
						   id="name" />

				</div>

			</div>

			<div class="flex mt-2">

				<div class="w-24 pr-2 pt-1">
					Starts At:
				</div>

				<div class="pr-2">
					<input type="text"
						   class="border-2 bg-white p-2"
						   placeholder="0000-00-00 00:00"
						   wire:model.debounce="starts_at"
						   id="starts_at" />

				</div>

				<div class="w-24 pr-2 pt-1 pl-2">
					Ends At:
				</div>

				<div class="pr-2">
					<input type="text"
						   class="border-2 bg-white p-2"
						   placeholder="0000-00-00 00:00"
						   wire:model.debounce="ends_at"
						   id="ends_at" />

				</div>
			</div>

		</div>

	</div>

	<!------------------------------------------/ /-------------------------------------->

	@include('livewire.campaign.opportunities.includes.choose-list')

	<!------------------------------------------/ /-------------------------------------->

	<div class="mt-4">

		<div class="text-xl font-bold">
			Script

			@if($opp->script)
				<i class="fas fa-check-circle text-blue ml-1 text-base"></i>
			@endif

		</div>

		<div class="flex mt-2 pl-8 border-l-4">

			<textarea type="text"
				   class="border-2 bg-white p-2 w-full h-32"
				   placeholder="Write your phonebanking script here"
				   wire:model="script">
				   </textarea>
		</div>

	</div>

	<!------------------------------------------/ /-------------------------------------->
	
	@include('livewire.campaign.opportunities.includes.choose-volunteers')


</div>
