<div>

	<div class="legislation" year="{{ $group->created_at->year }}">

		<div class=" text-2xl font-sans border-b-4 pb-2 flex">

			<div class="font-bold">
				<i class="fas fa-tag mr-1"></i> {{ $group->name }}
				<span class="text-grey-dark">({{ $count }})</span>
			</div>

			<div class="text-right flex-grow">

				@if($group->cat->has_position)

					@foreach(['Supports', 'Concerned', 'Opposed', 'Undecided'] as $position)
						<div class="inline px-2 py-1 text-base cursor-pointer mr-2	
									    @if($show_position == $position)
									   		bg-blue text-white
									   	@else
											bg-grey-lighter text-grey-darkest border-b
									   	@endif
									   	"
									   	wire:click="togglePosition('{{ $position }}');">
							{{ $position }}
						</div>
					@endforeach

				@endif

				

				<a href="/office/groups/{{ $group->id }}/edit" class="px-3 py-2 text-sm rounded-full">
					Edit Group
				</a>



			</div>


		</div>


		<div class="flex w-full">
			<div class="w-3/4">
				<div class="py-2 text-grey-dark">
					<div class="p-2">
						<span class="font-medium">Notes:</span> {{ $group->notes }}
					</div>
					
				</div>
				<div class="mr-4 text-blue cursor-pointer pr-4 text-center mt-4">

					<div class="w-1/3">
						<i class="fas fa-user mr-1"></i> Add Person to Group:
					</div>
					<div class="w-1/3">
				    	<input type="text"
				    		   class="border-2 border-rounded border-blue-light p-2 ml-2 font-bold text-grey-darkest"
				    		   wire:model.debounce="add_person_lookup"
						   />
					</div>

			    </div>
			</div>
			<div class="w-1/4">
				<div class="py-2 pl-2">

					<div class="inline">
						<input type="text"
							   class="p-2 border text-base w-48"
							   placeholder="Search"
							   id="search"
							   wire:model.debounce="search"
							   />
					</div>

					@if(Auth::user()->permissions->export)
					    <div class="text-sm py-1 pr-4 mr-4 text-blue cursor-pointer">
							<a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/export">
								<i class="fas fa-file-csv mr-1"></i> Export CSV
							</a>
					    </div>
					@endif

				    <div class="text-sm py-1 pr-4 mr-4 text-blue cursor-pointer
				    			@if($file_mode)
				    				bg-blue-lightest px-2
				    			@endif"
				    	 wire:click="$toggle('file_mode')">
				    	<i class="fas fa-upload mr-1"></i> Add / Manage Files
				    </div>

				    <div class="text-sm py-1 pr-4 mr-4 text-blue cursor-pointer
								@if($show_emails_mode)
				    				bg-blue-lightest px-2
				    			@endif"
				    	 wire:click="$toggle('show_emails_mode')">
				    	<i class="fas fa-envelope mr-1"></i> Email List
				    </div>

				</div>
				
			</div>
		</div>

	</div>

	

	@if($show_emails_mode)
		<div class="flex py-2" wire:key="show_emails_mode_div">

			<div class="p-2 text-right font-bold text-sm flex-shrink whitespace-no-wrap">
				Lacking Emails
				@if($show_position)
					<div class="text-blue">{{ $show_position }}</div>
				@else
					<div class="text-blue">All</div>
				@endif
			</div>
			<div class="p-2 flex-grow">
				<textarea class="border p-2 text-red h-32 w-full">{{ $this->MissingEmailList }}</textarea>
			</div>

			<div class="p-2 text-right font-bold text-sm flex-shrink whitespace-no-wrap">
				Email List
				@if($show_position)
					<div class="text-blue">{{ $show_position }}</div>
				@else
					<div class="text-blue">All</div>
				@endif
			</div>
			<div class="p-2 w-2/3">
				<textarea class="border p-2 text-blue h-32 w-full">{{ $this->EmailList }}</textarea>
			</div>

		</div>
	@endif

	@if($file_mode)

		@livewire('files.upload', ['model' => $group])

	@endif


	@if($add_person)

		<div class="flex" wire:key="add_person_form">
			<div class="flex-grow"></div>
			<div>
				@include('livewire.groups.include-add-person-form')
			</div>
			<div class="flex-grow"></div>
		</div>

	@elseif($people)

		<div class="py-2" wire:key="add_person_lookup">

			@if($add_person_lookup)
				<div class="flex py-1 border-dashed text-sm">

					<div class="flex-shrink">
						<button class="rounded-lg bg-blue hover:bg-red text-white px-2 py-1 text-xs"
								wire:click="addPersonInitial('NEW_{{ base64_encode($add_person_lookup) }}')">
							Create a New Person "{{ $add_person_lookup }}"
						</button>
					</div>

				</div>
			@endif

			@foreach($people as $person)

				<div class="flex py-1 {{ (!$loop->last) ? 'border-b' : '' }} border-dashed text-sm">

					<div class="flex-shrink"
						 wire:click="addPersonInitial('{{ $person->id }}')">
						<button class="rounded-lg bg-blue hover:bg-red text-white px-2 py-1 text-xs">
							Add
						</button>
					</div>

					<div class="px-2 w-1/5 truncate"
						 wire:click="addPersonInitial('{{ $person->id }}')">
						@if(get_class($person) == 'App\Person')
							<span class="rounded-full px-2 py-1 bg-orange-lightest border cursor-pointer">
								<i class="fas fa-user-check text-blue mr-1"></i>
								{{ $person->full_name }}
							</span>
						@else
							<span class="rounded-full px-2 py-1 bg-grey-lightest border cursor-pointer">
								{{ $person->full_name }}
							</span>
						@endif
					</div>

					<div class="px-2 w-3/4 truncate text-grey-dark">
						{{ $person->full_address }}
					</div>

				</div>

			@endforeach

		</div>

	@endif

	@if($instances instanceof \Illuminate\Pagination\LengthAwarePaginator )
		<div class="mb-6">
			{{ $instances->links() }}
		</div>
	@endif

	<div class="" wire:key="instances_div">

		@if($instances->first())

			<div class="flex bg-grey-lighter border-b text-sm font-medium text-grey-darker">
				<div class="w-6 px-2 py-1">&nbsp;</div>
				<div class="w-1/4 px-2 py-1">Person</div>
				<div class="w-1/5 px-2 py-1">Email</div>
				
				@if($group->cat->has_position)
					<div class="w-1/6 px-2 py-1">Position</div>
				@endif

				@if($group->cat->has_title)
					<div class="w-1/5 px-2 py-1">Title</div>
				@endif

				<div class="flex-grow px-2 py-1">Notes</div>
				<div class="w-24 px-2 py-1 text-right">Office</div>
			</div>

			@foreach($instances->whereIn('id', $just_created)
							   ->sortByDesc('created_at') as $instance)

				@livewire('groups.instance', ['instance' => $instance,
											  'was_just_created' => true
											  ], key('instance_'.$instance->id))

			@endforeach

			@foreach($instances->whereNotIn('id', $just_created) as $instance)

				@livewire('groups.instance', ['instance' => $instance], key('instance_'.$instance->id))

			@endforeach

		@endif

	</div>

</div>
