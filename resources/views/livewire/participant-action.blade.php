<div class="w-full p-4 bg-white">

	<div class="flex items-center w-full mb-4">
		<div class="w-1/6">
			Top 3:
		</div>

		@foreach ($top3_actions as $top3_action)

			<div wire:click="clickAction('{{ $top3_action }}')" class="bg-blue text-white rounded-full cursor-pointer opacity-50 
			@if ($new_action.$existing_action == $top3_action)
				opacity-100
				hover:bg-blue
			@else
				hover:bg-blue-dark
			@endif
			
			px-2 py-1 mx-2">
				{{ $top3_action }}
			</div>

		@endforeach
	</div>

	<div class="flex items-center w-full">
		<div class="w-1/2">
			<select wire:model="existing_action" class="border-2 py-2 px-4">
				<option value="">~ Existing Action ~</option>
				@foreach ($team_actions as $team_action)
					<option value="{{ $team_action }}">
						{{ $team_action }}
					</option>
				@endforeach
			</select>
		</div>
		@if (!$existing_action)
			<div class="w-1/4 -ml-4 mr-2" style="width: 10%;">
				OR
			</div>
			<div class="w-1/4 text-right -ml-4">
				<input type="text" placeholder="New Action Name" wire:model="new_action" class="border-2 py-2 px-4" />
			</div>
		@endif
	</div>
	<div class="w-full mt-4">
		<textarea wire:model="action_details" placeholder="Details" rows="3" class="p-2 border-2 w-full"></textarea>
	</div>
	<div class="w-full mt-4 text-center">
		
			<button style="transition: none;" type="button" wire:click="addAction()" onclick="$('#add_action_{{ $voter->id }}').modal('hide');" class="py-2 px-4 bg-blue rounded-full 
			 @if ($new_action || $existing_action)
			 	block
			 @else
			 	hidden
			 @endif
			 text-white hover:bg-blue-dark">
				Add New Action to {{ $voter->name }}
			</button>

			<button style="transition: none;" type="button" disabled class="cursor-disabled 
			 @if ($new_action || $existing_action)
			 	hidden
			 @else
			 	block opacity-50
			 @endif
			 py-2 px-4 bg-blue rounded-full text-white">
				Add New Action to {{ $voter->name }}
			</button>

	</div>
</div>