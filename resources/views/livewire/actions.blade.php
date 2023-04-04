<div class="-ml-4 px-4 bg-grey-lighter rounded-lg mb-4">
	<div class="pt-2 {{ ($participant->actions()->first()) ? 'border-b-2 pb-2' : '' }} border-grey-light text-grey-darkest text-base font-bold mt-4 mb-2">
		@if ($adding_new)
			<div wire:click="toggleNew()" class="text-sm text-blue rounded-full hover:text-blue-dark float-right cursor-pointer">
				Hide New
			</div>
		@else
			<div wire:click="toggleNew()" class="text-sm text-blue rounded-full hover:text-blue-dark float-right cursor-pointer">
				Add New
			</div>
		@endif
		Actions ({{ $participant->actions->count() }})
	</div>

	@if ($adding_new)
		<div class="w-full p-4 bg-white">
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
			<div class="w-full mt-4 text-right">
				
					<button style="transition: none;" type="button" wire:click="addAction()" class="py-2 px-4 bg-blue rounded-full 
					 @if ($new_action || $existing_action)
					 	block
					 @else
					 	hidden
					 @endif
					 text-white hover:bg-blue-dark">
						Add New Action to {{ $participant->name }}
					</button>

					<button style="transition: none;" type="button" disabled class="cursor-disabled 
					 @if ($new_action || $existing_action)
					 	hidden
					 @else
					 	block opacity-50
					 @endif
					 py-2 px-4 bg-blue rounded-full text-white">
						Add New Action to {{ $participant->name }}
					</button>

			</div>
		</div>
	@endif

	<table class="text-grey-darker text-sm w-full">
		@foreach ($participant->actions()->latest()->get() as $action) 
			<tr>
				<td class="p-2 border-b align-top">{{ $participant->actions->count() + 1 - $loop->iteration }}.</td>
				<td class="p-2 border-b align-top whitespace-no-wrap">
					@if ($action->created_at)
					{{ $action->created_at->format('n/j/y g:ia') }}
					@endif
				</td>
				<td class="p-2 border-b align-top"><b>{{ $action->name }}</b> - {{ $action->details }}</td>
				<td class="p-2 border-b align-top text-red-lighter">
					<i class="fa fa-times cursor-pointer hover:text-red" onclick="confirm('Are you sure you want to delete this Action?') || event.stopImmediatePropagation()" wire:click="delete({{ $action->id }})"></i>
				</td>
			</tr>
		@endforeach
	</table>


</div>
