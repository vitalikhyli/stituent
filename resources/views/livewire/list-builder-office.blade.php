<div class="flex text-grey-dark">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['linked'] == 'yes')
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Linked Only
					</div>
				@else
					Linked Only
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm">
				<div class="w-1/6">
					<label class="font-normal">
						<input wire:model="input.linked" type="radio" value="no"/> No
					</label>
				</div>
				<div class="w-1/6">
					<label class="font-normal">
						<input wire:model="input.linked" type="radio" value="yes"/> 
						@if ($input['linked'] == 'yes')
							<span class="text-blue font-bold">Yes</span>
						@else
							Yes
						@endif
					</label>
				</div>

				<div class="w-4/6 italic">
					Any voter with a Contact, Group, Case, etc
				</div>
			</div>
		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if ($input['master_email'] == 'yes')
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Master Email List
					</div>
				@else
					Master Email List
				@endif

				
			</div>
			<div class="w-2/3 flex pt-1 text-sm">
				<div class="w-1/6">
					<label class="font-normal">
						<input wire:model="input.master_email" type="radio" value="no"/> No
					</label>
				</div>
				<div class="w-1/6">
					<label class="font-normal">
						<input wire:model="input.master_email" type="radio" value="yes"/> 
						@if ($input['master_email'] == 'yes')
							<span class="text-blue font-bold">Yes</span>
						@else
							Yes
						@endif
					</label>
				</div>

				<div class="w-4/6 italic">

				</div>
			</div>
		</div>

		<div class="flex pt-1 w-full">
			<div class="w-1/3 relative uppercase text-sm pt-2">
				@if (count($input['groups']) > 0)
					<div class="absolute pin-l -ml-6 text-base text-blue-light">
						<i class="fa fa-check-circle"></i>
					</div>
					<div class="font-bold text-blue">
						Groups
					</div>
				@else
					Groups
				@endif

				
			</div>
			<div class="w-2/3" wire:ignore>
				@foreach ($categories as $category)
					<div class="flex items-center pb-1 text-sm">
						<div class="w-1/3">
							{{ $category->name }}
						</div>
						<div class="w-2/3">
							<select id="select2-category-{{ $category->id }}" wire-select-model="input.categories.{{ $category->id }}" class="select2" multiple="multiple">
								<option value=""></option>
								@foreach ($category->groups->sortBy('name') as $group)
									<option value="{{ $group->id }}">
										{{ $group->name }}
										@if ($group->archived_at)
											(Archived)
										@endif
									</option>
								@endforeach
							</select>
						</div>
					</div>
				@endforeach
			</div>
		</div>

		@if (count($input['groups']) > 0)

			@foreach ($input['groups'] as $group)
					
				<div class="flex text-sm mt-1">
					<div class="w-1/3 uppercase flex">
						<div class="w-1/5">
						</div>
						<div class="w-4/5 border-b">
							{{ $group->name }}
						</div>
					</div>
					<div class="w-2/3 flex border-b text-grey">
			
						<div class="w-1/6 text-sm">
							<label class="font-normal ml-2">
								<input wire:model="input.groups_position.{{ $group->id }}" checked="checked" type="radio" value="all"/> All
							</label>
						</div>
						<div class="w-5/6 flex flex-wrap">
							@foreach ($group->positions() as $position)
							<div class="w-1/4">
								<label class="font-normal">
									<input wire:model="input.groups_position.{{ $group->id }}" type="radio" value="{{ $position }}"/> {{ ucwords($position) }}
								</label>
							</div>
							@endforeach
						</div>
					</div>
				</div>

			@endforeach

		@endif

	</div>
</div>