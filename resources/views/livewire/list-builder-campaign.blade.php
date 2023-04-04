<div class="flex text-grey-dark">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['support'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Support Level
					</div>
				@else
					Support Levels
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap">

				<div class="pr-6">
					<label class="font-normal">
						<input type="checkbox" wire:model="input.support" value="1" /> 
						<span class="px-2 py-1 bg-green text-white rounded-full">1</span> 
					</label>
				</div>
				<div class="pr-6">
					<label class="font-normal">
						<input type="checkbox" wire:model="input.support" value="2" /> 
						<span class="px-2 py-1 bg-yellow-dark text-white rounded-full">2</span> 
					</label>
				</div>
				<div class="pr-6">
					<label class="font-normal">
						<input type="checkbox" wire:model="input.support" value="3" /> 
						<span class="px-2 py-1 bg-orange text-white rounded-full">3</span> 
					</label>
				</div>
				<div class="pr-6">
					<label class="font-normal">
						<input type="checkbox" wire:model="input.support" value="4" /> 
						<span class="px-2 py-1 bg-red text-white rounded-full">4</span> 
					</label>
				</div>
				<div class="pr-6">
					<label class="font-normal">
						<input type="checkbox" wire:model="input.support" value="5" /> 
						<span class="px-2 py-1 bg-red-dark text-white rounded-full">5</span> 
					</label>
				</div>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['any_actions'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Any of these Actions
					</div>
				@else
					Any of these Actions
				@endif

			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.any_actions" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach (\App\Action::thisTeam()->select('name')->groupBy('name')->get()->sortBy('name') as $action)
						<option value="{{ $action->name }}">{{ $action->name }}</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['recent_activity'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Recent Activity
					</div>
				@else
					Recent Activity
				@endif

			</div>
			<div class="w-2/3 pt-1 text-sm whitespace-no-wrap">

				<div class="" wire:ignore>
					<select wire-select-model="input.recent_activity" class="select2">
						<option value=""></option>
						<option value="1">One Day</option>
						<option value="2">Two Days</option>
						<option value="3">Three Days</option>
						<option value="4">Four Days</option>
						<option value="5">Five Days</option>
						<option value="7">1 Week</option>
						<option value="10">Ten Days</option>
						<option value="14">2 Weeks</option>
						<option value="30">1 Month</option>
						<option value="90">3 Months</option>
						<option value="{{ \Carbon\Carbon::now()->startOfYear()->diffInDays(\Carbon\Carbon::now()) }}">This Year</option>
						<option value="365">1 Year</option>
					</select>
				</div>
				
					<div class="mt-2 flex">

						<div class="w-1/2" wire:ignore>
							<select wire-select-model="input.recent_activity_actions" 
									class="select2" multiple="multiple">
								<option value=""></option>
								@foreach(\App\Action::thisTeam()->select('name')->groupBy('name')->get()->sortBy('name') as $action)
									<option value="{{ $action->name }}">{{ $action->name }}</option>
								@endforeach
							</select>
						</div>

						<div class="w-1/2 mx-2 my-1">
							<label for="recent_activity_include">
							<input id="recent_activity_include" type="radio" value="1" wire:model="input.recent_activity_include" />
								Include
							</label>

							<label for="recent_activity_exclude">
							<input id="recent_activity_exclude" type="radio" value="0" wire:model="input.recent_activity_include" />
								Exclude
							</label>
						</div>
					</div>
	
				

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['tags'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Any Tags
					</div>
				@else
					Any Tags
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.tags" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($tags as $tag)
						<option value="{{ $tag->id }}">{{ $tag->name }}</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['all_tags'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						All Tags
					</div>
				@else
					All Tags
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.all_tags" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($tags as $tag)
						<option value="{{ $tag->id }}">{{ $tag->name }}</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['imports'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Imports
					</div>
				@else
					Imports
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.imports" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($imports as $import)
						<option value="{{ $import->id }}">{{ $import->name }}</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full border-t border-dashed mt-2 border-blue">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['within_lists_any'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Within ANY List
					</div>
				@else
					Within ANY List
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.within_lists_any" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($subtract_lists as $within_list)
						<option value="{{ $within_list->id }}">
							{{ $within_list->name }} ({{ $within_list->static_count }})
						</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['within_lists_all'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Within ALL Lists
					</div>
				@else
					Within ALL Lists
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.within_lists_all" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($subtract_lists as $within_list)
						<option value="{{ $within_list->id }}">
							{{ $within_list->name }} ({{ $within_list->static_count }})
						</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['subtract_lists'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Subtract Lists
					</div>
				@else
					Subtract Lists
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.subtract_lists" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($subtract_lists as $subtract_list)
						<option value="{{ $subtract_list->id }}">
							{{ $subtract_list->name }} ({{ $subtract_list->static_count }})
						</option>
					@endforeach
				</select>

			</div>

		</div>


		

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['add_lists'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Add Lists
					</div>
				@else
					Add Lists
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.add_lists" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach ($subtract_lists as $add_list)
						<option value="{{ $add_list->id }}">
							{{ $add_list->name }} ({{ $add_list->static_count }})
						</option>
					@endforeach
				</select>

			</div>

		</div>




		@if(Auth::user()->permissions->developer)

<pre class="hidden">
	{{ print_r($input['exclude_volunteers']) }}
</pre>

		<div class="flex pt-1 w-full border-t border-dashed mt-2 border-blue">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['exclude_volunteers'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Exclude Volunteers
					</div>
				@else
					Exclude Volunteers
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>

				<select wire-select-model="input.exclude_volunteers" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach (App\Participant::getVolunteerColumns($english = true) as $field => $label)
						<option value="{{ $field }}">
								{{ $label }}
						</option>
					@endforeach
				</select>

			</div>

		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">

				@if ($input['include_volunteers'])
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Only Volunteers
					</div>
				@else
					Only Volunteers
				@endif

			</div>


			<div class="w-2/3 flex pt-1 text-sm whitespace-no-wrap" wire:ignore>


				<select wire-select-model="input.include_volunteers" class="select2" multiple="multiple">
					<option value=""></option>
					@foreach (App\Participant::getVolunteerColumns($english = true) as $field => $label)
						<option value="{{ $field }}">
								{{ $label }}
						</option>
					@endforeach
				</select>

			</div>

		</div>

		@endif

	</div>


</div>