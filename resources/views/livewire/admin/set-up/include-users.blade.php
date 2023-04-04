<div class="flex">

	<div class="text-2xl pt-6 pr-4 text-grey-dark font-bold w-48">
		<div class="border-r-4">
			3. Users
		</div>
	</div>

	<div class="p-6">

		<div class="pl-6">
			@if(!$team->usersAll->first())

				<div class="border-4 border-red text-red px-4 py-2 mb-2">
					This Team has No Users
				</div>

			@endif
		</div>

		<div class="pl-6">
			@if($team->usersAll->first())

				<div>
					@if(!\App\Permission::whereIn('user_id', $team->usersAll->pluck('id')->toArray())
									   ->where('team_id', $this->team_id)
									   ->where('admin', true)
									   ->first())

						<div class="border-4 border-red text-red px-4 py-2 mb-2">
							This Team has No Admins
						</div>

					@endif
				</div>

			@endif
		</div>

		<div class="pl-6">

			<div class="py-2 flex">

				<div class="py-2 pr-2">
					Create a new user:
				</div>

				<div>

					<input type="text"
						   wire:model="new_user_name"
						   placeholder="Name"
						   class="border p-2 w-48 mr-2" />

				</div>

				<div>

					<input type="text"
						   wire:model="new_user_email"
						   placeholder="Email"
						   class="border p-2 w-48" />

					<div>

						@if(\App\User::where('email', $new_user_email)->exists())
							<div class="font-bold text-red">Email in use</div>
						@endif

					</div>

				</div>


			</div>


			<div>
				@if($account && $account->users()->first())

					<div class="mb-4"
						 wire:key="users">

						<div class="pb-1 border-b-2 mb-1 font-bold">
							Available Users:
						</div>

						@foreach($account->users() as $member)

							<div class="flex cursor-pointer py-1"
								  wire:key="user_{{ $member->id }}">

								<div class="px-2 truncate w-48">

									{{ $member->name }}

								</div>

								<div wire:key="onTeam_{{ $member->id }}"
									 class="flex-shrink">
									<div wire:click="toggleOnTeam({{ $member->id }})"
										 wire:key="button_onTeam_{{ $member->id }}"
										 class="rounded-lg px-2 py-1 text-sm

											@if(\App\TeamUser::where('user_id', $member->id)
							 		  		  ->where('team_id', '!=', $team->id)
							 		  		  ->whereIn('team_id', $account->teams->pluck('id'))
							 		  		  ->doesntExist())

							 		  		  	opacity-25

							 		  		 @endif

											    @if($member->memberOfTeam($team))
											   		bg-blue text-white
											   	@else
											   		bg-grey-lighter text-grey-dark
											    @endif
										 	   ">
										On Team
									</div>
								</div>
								
								<div wire:key="admin_{{ $member->id }}"
									 class="flex-shrink">
									<div wire:click="toggleAdmin({{ $member->id }})"
										 wire:key="button_admin_{{ $member->id }}"
										 class="ml-2 rounded-lg px-2 py-1 text-sm
											    @if($member->permissionsForTeam($team)->admin)
											   		bg-blue text-white
											   	@else
											   		bg-grey-lighter text-grey-dark
											    @endif
										 	   {{ (!$member->memberOfTeam($team)) ? 'opacity-0' : '' }}
											    ">
										Admin
									</div>
								</div>

								<div class="pl-2 -mt-1 text-right text-sm"
									 wire:key="password_{{ $member->id }}">
										@livewire('admin.set-password', 
												['user_id' => $member->id],
												key('password-component-'.$member->id))
								</div>

							</div>


						@endforeach

					</div>

					<div class="text-sm text-black mb-2 flex">
						<div class="w-1/2">
						</div>
						<div class="flex-grow text-right">
							* You can't remove users if they only belong to one team (because they would be orphaned in the database.) Delete them instead.
						</div>
					</div>

				@endif

			</div>

			<div>

				@if($new_user_email || $new_user_name)

	    			<div class="p-2 px-4 border-2 flex bg-grey-lightest mb-2">

	    				<div class="py-1 text-xl">
	    					<div class="font-bold">{{ $new_user_name }}</div>
	    					<div class="">{{ $new_user_email }}</div>
	    				</div>
						
						<div class="flex-grow text-right">
							@if($new_user_email
								&& $new_user_name
								&& \App\User::where('email', $new_user_email)->doesntExist())

			    				<div class="flex-grow text-right pt-2">
							    	<button wire:click="createUser()"
							    			class="rounded-lg bg-blue text-white px-2 py-1 text-sm ml-2">
							    		Add User
							    	</button>
							    </div>

							@endif

						</div>

	    			</div>

				@endif

			</div>

		</div>

    </div>

</div>