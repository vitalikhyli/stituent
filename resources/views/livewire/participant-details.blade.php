<tr class="group">
	@if($tag_with)
		<td class="whitespace-no-wrap">

			<button wire:click="toggleTag({{ $tag_with->id }}, '{{ $voter->id }}')" class="px-2 py-1 text-xs mr-2 
				@if($voter->hasTag($tag_with->id)))
					bg-blue text-white 
				@else
					bg-grey-light text-gray-dark 
				@endif
			">
				{{ $tag_with->name }}
			</button>

		</td>
	@endif

	<td class="text-grey whitespace-no-wrap w-4" wire:click="what">
		@if (isParticipant($voter))
			<i class="fa fa-check-circle text-blue mr-1"></i> 
		@endif
		{{ $iteration }}.
	</td>

    <td>
		<a href="/campaign/participants/{{ $voter->id }}/edit">
			{{ $voter->name }}
		</a>
		@if ($voter->archived_at)
			<i class="fa fa-archive hover:text-blue-500" style="cursor:help;" title="Voter is Archived. May be moved or deceased, but was NOT in most recent active voter file."></i>
		@endif
		@if (isParticipant($voter))
			@php
				$participant = getParticipant($voter);
			@endphp
			@if ($participant->tags->count() > 0)
			<div class="">
				<div class="text-xs uppercase whitespace-no-wrap text-blue-dark">
					@foreach ($participant->tags as $tag)
						
						<i class="fa fa-tag"></i>
						{{ $tag->name }}<br>
						
					@endforeach
				</div>
			</div>
			@endif
		@endif

		<div class="pl-4">
			<div class="">{{ $voter->email }}</div>
			<div class="">{{ $voter->readable_phone }}</div>
			@if ($cf_plus_phones)
				<div class="text-green">
					CF+ {{ $cf_plus_phones }} <i class="fa fa-check-circle"></i>
				</div>
			@endif
			@if ($cf_plus_cell)
				<div class="text-orange">
					CF+ CELL {{ $cf_plus_cell }} <i class="fa fa-check-circle"></i>
				</div>
			@endif
			<x-cf-plus-modal :voter="$voter"></x-cf-plus-modal>
			
		</div>
		@if ($edit)

			<input type="text" autocomplete="off" wire:model.debounce.1000ms="participant_email" class="form-control mt-4" placeholder="Email"/>

			<input type="text" autocomplete="off" wire:model.debounce.1000ms="participant_phone" class="form-control mt-4" placeholder="Phone #"/>

		@endif

	</td>

	<td>
		{{ $voter->address_line_street }}<br>
		{{ $voter->address_city_zip }}
	</td>

	<td class="">
		
		@if (isParticipant($voter))

			@foreach ($voter->actions as $action)
				@if (!$action->created_at) 
					@continue
				@endif
				<div class="whitespace-no-wrap">
					{{ $action->created_at->format('n/j') }} - {{ $action->name }}
				</div>

			@endforeach

		@endif

		<button class="text-xs border px-3 py-1 rounded-full cursor-pointer hover:shadow"
				data-toggle="modal" data-target="#add_action_{{ $voter->id }}">
			Add Action
		</button>

		<div id="add_action_{{ $voter->id }}" class="modal fade" role="dialog">
		  <div class="modal-dialog">

		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title text-black">Add Action to <b>{{ $voter->name }}</b></h4>
		      </div>
		      <div class="modal-body">
		        <p>
		        	@if (isParticipant($voter))

		        		@if ($voter->actions->count() > 0)
		        			<div class="uppercase border-b">PREVIOUS ACTIONS</div>
			        		
			        		<div class="p-2">
								@foreach ($voter->actions as $action)
									@if (!$action->created_at) 
										@continue
									@endif

									<div class="">
										<b>{{ $action->created_at->format('n/j/Y') }} - {{ $action->name }}</b> - {{ $action->details }}
									</div>

								@endforeach
							</div>
						@endif

					@endif

					<div class="uppercase border-b font-bold text-blue mt-8">ACTION TO ADD</div>

					@livewire('participant-action', 
							 ['voter_or_participant' => $voter],
							 key($voter->id.'_action_'.Str::random(10)))


		        </p>
		      </div>
		    </div>

		  </div>
		</div>
		
	</td>

	<td class="text-right border-l w-1/3" wire:loading.class="opacity-75">
		<div class="flex w-full text-center h-5">
			
		

			@if (isParticipant($voter))

				<!-- <div class="w-1/6 text-xs text-grey cursor-pointer" wire:click="clearSupport">
					Clear
				</div> -->
				<div class="w-1/6 text-xs text-grey cursor-pointer">
					
				</div>
				@php
					$participant = getParticipant($voter);
					$participant_support = $participant->support();
				@endphp
				
				@for ($i=1; $i<6; $i++)
					<div class="relative w-1/6">
						@if ($participant_support == $i)
							
							<div style="padding-top: 6px; margin-top: -6px; margin-left: 4px;" class="{{ getSupportClass($i) }} text-white rounded-full mx-auto absolute w-8 h-8 center cursor-pointer" wire:click="setSupport({{ $i }})">
								{{ $i }}
							</div>
						@else
							<div class="cursor-pointer" wire:click="setSupport({{ $i }})">
								{{ $i }}
							</div>
						@endif
					</div>
					
				@endfor
				
			@else
				<div class="w-1/6 text-xs text-grey cursor-pointer">
					
				</div>
				@for ($i=1; $i<6; $i++)
					<div class="w-1/6 cursor-pointer" wire:click="setSupport({{ $i }})">
						{{ $i }}
					</div>
				@endfor
				
			@endif



			<div class="text-right">
				@if ($edit)
					<i class="fa fa-times hover:text-blue cursor-pointer ml-2 mt-1 w-12" wire:click="toggleEdit"></i>
				@else
					<i class="fa fa-edit hover:text-blue cursor-pointer ml-2 mt-1 w-12" wire:click="toggleEdit"></i>
				@endif
			</div>

		
			
		</div>
		@if ($edit)
			<textarea wire:model.debounce.1000ms="notes" rows=3 class="form-control p-2 mr-2 mt-4" placeholder="Current Campaign Notes"></textarea>
		@else

			@if ($notes)
				<div class="italic m-2 mt-4 text-left text-black">
					{{ $notes }}
				</div>
			@endif

		@endif

	</td>

</tr>