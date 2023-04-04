<div class="mt-1">


	@foreach($categories as $cat)

		<div wire:click="toggleOpen({{ $cat->id }});"
			 class="cursor-pointer font-medium border-b-2 bg-blue-lightest px-3 py-1 mb-1 hover:shadow-lg hover:border-grey-darker {{ (!$cat->groups->first()) ? 'opacity-50' : '' }}">
			@if(!$category_is_open[$cat->id])
		 		<i class="fas fa-plus-circle mr-1 text-grey-darker"></i>
		 	@else
		 		<i class="fas fa-minus-circle mr-1 text-grey-darker"></i>
		 	@endif
			{{ $cat->name }}

			 @if($cat->num_selected)
				 <span class="float-right text-blue">
				 	<i class="fas fa-check-circle text-blue"></i>
				 	{{ $cat->num_selected }}
				 </span>
			 @endisset

		</div>


		<div class="{{ (!$category_is_open[$cat->id]) ? 'hidden' : '' }}  ">

				@if($cat->chosen)

					<div class="bg-grey-lightest p-2 border-b-2 mx-2">

					    @foreach($cat->chosen as $chosen)

							<div class="mt-1 ml-2 flex border-grey-lighter w-full"
								 wire:key="group_div-chosen_{{ $chosen->group_id }}_{{ $chosen->position }}">

								<label for="selected_groups_{{ $chosen->group_id }}_{{ $chosen->position }}" class="font-normal truncate w-full">

									<input id="selected_groups_{{ $chosen->group_id }}_{{ $chosen->position }}" 
										   type="checkbox" 
										   wire:model.debounce="selected_groups.{{ $chosen->group_id }}.{{ $chosen->position }}" 
										   wire:key="group-chosen_{{ $chosen->group_id }}_{{ $chosen->position }}"
										   multiple="multiple" 
										   value=true />

											{{ $chosen->name }}

									<span class="text-xs truncate text-grey-dark ml-1">
										{{ $chosen->position_name }}
									</span>

								</label>

							</div>

						@endforeach
					</div>
				@endif


			@foreach ($cat->groups as $group)

				<div class="mt-1 ml-2 flex border-grey-lighter w-full">

					@if(!$cat->has_position)

						<div>
							<label for="selected_groups.{{ $group->id }}.main" class="font-normal truncate">
								<input id="selected_groups.{{ $group->id }}.main" type="checkbox" wire:model="selected_groups.{{ $group->id }}.main" value=true />
								{{ $group->name }}
							</label>
						</div>

					@else

						<div class="w-1/2 truncate">
							<label for="selected_groups.{{ $group->id }}.main" class="font-normal truncate">
								<input id="selected_groups.{{ $group->id }}.main" type="checkbox" wire:model="selected_groups.{{ $group->id }}.main" value=true />
								{{ $group->name }}
							</label>
						</div>

						<div class="mx-1">
							<label for="selected_groups.{{ $group->id }}.support" class="font-normal truncate">
								<input id="selected_groups.{{ $group->id }}.support" type="checkbox" wire:model="selected_groups.{{ $group->id }}.support" value=true /> Sup
							</label>
						</div>

						<div class="mx-1">
							<label for="selected_groups.{{ $group->id }}.oppose" class="font-normal truncate">
								<input id="selected_groups.{{ $group->id }}.oppose" type="checkbox" wire:model="selected_groups.{{ $group->id }}.oppose" value=true /> Opp
							</label>
						</div>


					@endif

				</div>



			@endforeach

		</div>

	@endforeach
</div>