@isset($current_room)

<div id="basechat-room-id" room-id="{{ $current_room->id }}">
</div>

<div class="w-full px-6">
	<div class="float-right text-base text-grey mt-2 text-right">
		<span class="font-bold text-lg">
			{{ $current_room->member_count }}
		</span> Members
		<br>
		<div class="text-xs hover:text-grey-light cursor-pointer tracking-base text-grey-dark pt-4"
			 data-toggle="modal" data-target="#edit-room-internal">
			Edit
		</div>
	</div>
	<div class="text-left text-3xl font-bold tracking-wide mt-2 relative text-black-extra">
		#{{ $current_room->slug }}
		<div class="absolute text-white" style="left: 3px; top: -3px;">
			#{{ $current_room->slug }}
		</div>
		
	</div>
</div>

<div class="px-4">

	<div class="text-xs text-grey-dark p-2">
			
			
			@if ($current_room->teams()->count() > 0)
				@foreach ($current_room->teams()->get() as $team)
					<b>
						<span class="room-user hover:text-white cursor-pointer transition">
							{{ $team->name }}
						</span>
					</b> (
					@foreach ($team->users as $teamuser)
						<span class="room-user hover:text-white cursor-pointer transition">
							{{ $teamuser->name }}&nbsp;
						</span>
					@endforeach
					)
				@endforeach
			@endif
			
			
			@if ($current_room->users()->count() > 0)
				@foreach ($current_room->users()->get() as $user)
					<span class="room-user hover:text-white cursor-pointer transition">
						{{ $user->name }}&nbsp;
					</span>
				@endforeach
			@endif
			
			
		</div>

	<div class="relative mb-8">
		<form id="basechat-send" action="/basechat/rooms/{{ $current_room->id }}/send-message" method="POST">
			@csrf
			<textarea id="basechat-input" 
				   	  name="message"
				   	  class="w-full border-2 border-b-0 m-0 border-grey-darkest text-lg p-4 text-grey"
				   	  rows="3"
					  required
				   	  style="background: #111;"></textarea>
			<div class="flex text-base text-grey">
				<button class="w-1/3 border-2 border-r-0 border-grey-darkest px-4 -mt-2 py-2 hover:bg-grey-darkest hover:text-white">Add File...</button>
				<button class="w-2/3 border-2 border-grey-darkest px-4 -mt-2 py-2 
							   hover:bg-grey-darkest hover:text-white"
						type="submit">
					Send
				</button>
			</div>
		</form>

	</div>
</div>

<div id="edit-room-internal" class="modal fade text-grey-dark text-sm" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form class="basechat-room-form" action="/basechat/rooms/{{ $current_room->id }}/save" method="POST">

					@csrf
					<input type="hidden" name="access_level" value="internal" />
					<input type="hidden" name="external" value="0" />
					<input type="hidden" name="direct" value="0" />

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Edit Internal Room</h4>
					</div>
					<div class="modal-body p-6">

						<div class="text-lg font-bold mt-2">
							1. Room Name
						</div>
						
						<input type="text" 
							   name="name" 
							   class="border-2 p-4 text-lg w-full" 
							   autocomplete="off" 
							   required
							   value="{{ $current_room->name }}" 
							   placeholder="Room Name" />


						<div class="text-lg font-bold mt-8">
							2. Who will have access to this room?
						</div>

						<div class="flex">
							<div class="w-1/2 py-4">
								<input id="access_type_team" 
									   type="radio" 
									   @if ($current_room->teams()->count() > 0)
									   	checked="checked" 
									   @endif
									   name="access_type" 
									   value="team" 
									   onchange="$('.internal-users-list').toggle();"
									/>
								<label for="access_type_team">All {{ Auth::user()->team->name }}</label>
							</div>
							<div class="w-1/2 py-4">
								<input id="access_type_user" 
									   type="radio" name="access_type" 
									   @if ($current_room->users()->count() > 0)
									   	checked="checked" 
									   @endif
									   value="user" 
									   onchange="$('.internal-users-list').toggle();" />
								<label for="access_type_user">Choose Users</label>
							</div>
						</div>
						
						<div class="internal-users-list" 
								@if ($current_room->teams()->count() > 0)
							   	  style="display:none;" 
							    @endif
									   >
							<div class="inline">
								<!-- CHECKBOX TOGGLE HACK
									http://dabblet.com/gist/1506530 -->
								<input id="internal-edit-toggle-{{ Auth::user()->id }}" 
									   type="checkbox" 
									   disabled 
									   checked="checked" 
									   name="access_users[]" 
									   value="{{ Auth::user()->id }}" />
								<label class="toggle-checkbox rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="internal-edit-toggle-{{ Auth::user()->id }}">
									<!-- <i class="far fa-circle"></i>
									<i class="far fa-check-circle"></i>  -->
									{{ Auth::user()->name }}
								</label>
							</div>
							@foreach (Auth::user()->team->users()->get() as $user)
								@if ($user->id == Auth::user()->id) 
									@continue
								@endif
								<div class="inline">
									<!-- CHECKBOX TOGGLE HACK
										http://dabblet.com/gist/1506530 -->
									<input id="internal-edit-toggle-{{ $user->id }}" 
										   type="checkbox" 
										   name="access_users[]" 
										   @if ($current_room->users()->pluck('id')->contains($user->id))
												checked="checked"
										   @endif
										   value="{{ $user->id }}" />
									<label class="toggle-checkbox cursor-pointer rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="internal-edit-toggle-{{ $user->id }}">
										<!-- <i class="far fa-circle"></i>
										<i class="far fa-check-circle"></i>  -->
										{{ $user->name }}
									</label>
								</div>
									
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<button formaction="/basechat/rooms/{{ $current_room->id }}/archive" type="button" class="basechat-room-archive btn btn-danger float-left">Archive Room</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>

		</div>
	</div>

@else

	<div class="text-center w-full" style="min-height: 600px">

		<div class="text-xl p-8">
			You do not have a room open right now. <br><br>
			<b>You can select a room on the left or add a new room.</b>
		</div>

		<div class="text-xl mx-auto w-1/2 uppercase hover:text-grey-light hover:border-grey-light cursor-pointer text-grey-dark border-2 rounded-full border-grey-dark px-4 py-2 tracking-wide"
			 data-toggle="modal" data-target="#add-room-internal">
			Create a Room
		</div>

	</div>


@endisset