<div id="basechat-rooms">

	<div class="font-bold text-grey-dark text-xl">
		All Rooms
	</div>

	<div class="text-grey-dark uppercase border-b border-grey-darkest pb-2 mt-6">
		<div class="float-right text-xs hover:text-grey-light cursor-pointer text-grey-dark"
			 data-toggle="modal" data-target="#add-room-internal">
			Add Room
		</div>
		Internal
	</div>

	@isset($internalrooms)

	<div class="">

		@foreach ($internalrooms as $room)

			@if ($current_room)

				@if ($current_room->id == $room->id) 

				<div room-id="{{ $room->id }}" class="rounded-full bg-grey-light text-blue-darker pl-3 pr-2 py-1 mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					#{{ $room->slug }}
				</div>

				@else 
					
					<div room-id="{{ $room->id }}" class="room rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
						<div class="float-right">{{ $room->member_count }}</div>
						#{{ $room->slug }}
					</div>

				@endif

			@else 

				<div room-id="{{ $room->id }}" class="room rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					#{{ $room->slug }}
				</div>

			@endif

		@endforeach

		<!-- <div class="rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
			<div class="float-right">3</div>
			#stituent-support
		</div> -->
	</div>

	@endisset

	<div class="text-grey-dark uppercase border-b border-grey-darkest pb-2 mt-6">
		<!-- <div class="float-right text-xs hover:text-grey-light cursor-pointer text-grey-dark"
			 data-toggle="modal" data-target="#add-room-external">
			Add Room
		</div> -->
		<div class="float-right text-xs hover:text-grey-light text-grey-darker"
			 >
			Coming Soon...
		</div>
		External
	</div>

	@isset($externalrooms)

<!-- 	<div class="">

		@foreach ($externalrooms as $room)

			@if ($room->id == $current_room->id)

				<div room-id="{{ $room->id }}" class="rounded-full bg-grey-light text-blue-darker pl-3 pr-2 py-1 mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					#{{ $room->slug }}
				</div>

			@else 

				<div room-id="{{ $room->id }}" class="room rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					#{{ $room->slug }}
				</div>

			@endif

		@endforeach

	</div> -->

	@endisset
<!-- 
	<div class="text-grey-dark uppercase border-b border-grey-darkest pb-2 mt-6">
		<div class="float-right text-xs hover:text-grey-light cursor-pointer text-grey-dark"
			 data-toggle="modal" data-target="#add-room-direct">
			Add Room
		</div>
		<div class="float-right text-xs hover:text-grey-light text-grey-darker"
			 >
			Coming Soon...
		</div>
		Direct
	</div> -->

	@isset($directrooms)

<!-- 	<div class="">
		@foreach ($directrooms as $room)

			@if ($room->id == $current_room->id)

				<div room-id="{{ $room->id }}" class="rounded-full bg-grey-light text-blue-darker pl-3 pr-2 py-1 mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					@ {{ $room->other_person_name }}
				</div>

			@else 

				<div room-id="{{ $room->id }}" class="room rounded-full cursor-pointer pl-3 pr-2 py-1 hover:bg-grey-darkest hover:text-grey-light mt-1">
					<div class="float-right">{{ $room->member_count }}</div>
					@ {{ $room->other_person_name }}
				</div>

			@endif

		@endforeach
	</div> -->

	@endisset

	<!-- ============================== ADD ROOM MODALS ======================== -->

	<div id="add-room-internal" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form class="basechat-room-form" action="/basechat/rooms" method="POST">

					@csrf
					<input type="hidden" name="access_level" value="internal" />
					<input type="hidden" name="external" value="0" />
					<input type="hidden" name="direct" value="0" />

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add Internal Room</h4>
					</div>
					<div class="modal-body p-6">

						<div class="text-lg font-bold mt-2">
							1. Choose your room name
						</div>
						
						<input type="text" 
							   name="name" 
							   class="border-2 p-4 text-lg w-full" 
							   autocomplete="off" 
							   required
							   placeholder="Room Name" />


						<div class="text-lg font-bold mt-8">
							2. Who will have access to this room?
						</div>

						<div class="flex">
							<div class="w-1/2 py-4">
								<input id="access_type_team" 
									   type="radio" 
									   checked="checked" 
									   name="access_type" 
									   value="team" 
									   onchange="$('#internal-users-list').toggle();"
									/>
								<label for="access_type_team">All {{ Auth::user()->team->name }}</label>
							</div>
							<div class="w-1/2 py-4">
								<input id="access_type_user" 
									   type="radio" name="access_type" 
									   value="user" 
									   onchange="$('#internal-users-list').toggle();" />
								<label for="access_type_user">Choose Users</label>
							</div>
						</div>
						
						<div id="internal-users-list" style="display:none;">
							<div class="inline">
								<!-- CHECKBOX TOGGLE HACK
									http://dabblet.com/gist/1506530 -->
								<input id="internal-toggle-{{ Auth::user()->id }}" 
									   type="checkbox" 
									   disabled 
									   checked="checked" 
									   name="access_users[]" 
									   value="{{ Auth::user()->id }}" />
								<label class="toggle-checkbox rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="internal-toggle-{{ Auth::user()->id }}">
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
									<input id="internal-toggle-{{ $user->id }}" 
										   type="checkbox" 
										   name="access_users[]" 
										   value="{{ $user->id }}" />
									<label class="toggle-checkbox cursor-pointer rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="internal-toggle-{{ $user->id }}">
										<!-- <i class="far fa-circle"></i>
										<i class="far fa-check-circle"></i>  -->
										{{ $user->name }}
									</label>
								</div>
									
							@endforeach
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Add Room</button>
					</div>
				</form>
			</div>

		</div>
	</div>

	<div id="add-room-external" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form class="basechat-room-form" action="/basechat/rooms" method="POST">

					@csrf
					
					<input type="hidden" name="access_level" value="external" />
					<input type="hidden" name="external" value="1" />
					<input type="hidden" name="direct" value="0" />

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add External Room</h4>
					</div>
					<div class="modal-body">
						<p>Add a discussion room for any State Fluency subscribers.</p>
						<div class="text-lg font-bold mt-2">
							1. Choose your room name
						</div>
						
						<input type="text" 
							   name="name" 
							   class="border-2 p-4 text-lg w-full" 
							   autocomplete="off" 
							   placeholder="Room Name"
							   required />

						<div class="text-lg font-bold mt-8">
							2. Who will have access to this room?
						</div>

						<div class="flex flex-wrap">

							@foreach (App\Team::all() as $team)

								
								<div class="w-1/3">
									<!-- CHECKBOX TOGGLE HACK
										http://dabblet.com/gist/1506530 -->
									<input id="external-team-toggle-{{ $team->id }}" 
										   type="checkbox" 
										   name="access_teams[]" 
										   value="{{ $team->id }}" />
									<label class="toggle-checkbox cursor-pointer rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="external-team-toggle-{{ $team->id }}">
										<!-- <i class="far fa-circle"></i>
										<i class="far fa-check-circle"></i>  -->
										<!-- {{ $team->name }} -->
									</label>
								</div>

								<div class="w-2/3">

									@foreach ($team->users()->get() as $user)

										<div class="inline">
											<!-- CHECKBOX TOGGLE HACK
												http://dabblet.com/gist/1506530 -->
											<input id="external-users-toggle-{{ $user->id }}" 
												   type="checkbox" 
												   name="access_users[]" 
												   @if (Auth::user()->id == $user->id)
														checked="checked"
														disabled
												   @endif
												   value="{{ $user->id }}" />
											<label class="toggle-checkbox cursor-pointer rounded-full px-3 py-1 text-grey border font-normal hover:bg-grey-lightest whitespace-no-wrap m-1" for="external-users-toggle-{{ $user->id }}">
												<!-- <i class="far fa-circle"></i>
												<i class="far fa-check-circle"></i>  -->
												<!-- {{ $user->name }} -->
											</label>
										</div>
											
									@endforeach

								</div>
							@endforeach

						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Add Room</button>
					</div>
				</form>
			</div>

		</div>
	</div>

	<div id="add-room-direct" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<form class="basechat-room-form" action="/basechat/rooms" method="POST">

					@csrf
					<input type="hidden" name="access_level" value="direct" />
					<input type="hidden" name="external" value="" />
					<input type="hidden" name="direct" value="1" />

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Add Direct Room</h4>
					</div>
					<div class="modal-body">
						<p>Add a discussion room for one other person.</p>
						<input type="text" name="name" class="form-control" />
					</div>

					<div class="text-lg font-bold">
						Access
					</div>

					<select name="access_user" class="form-control">
						@foreach (App\User::all() as $user)
							<option value="{{ $user->id }}">{{ $user->name }}</option>
						@endforeach
					</select>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Add Room</button>
					</div>
				</form>
			</div>

		</div>
	</div>
</div>
