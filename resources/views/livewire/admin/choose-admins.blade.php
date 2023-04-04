<div class="relative">

	<div class="absolute z-10 px-4 py-4 text-base leading-tight text-white transform -translate-x-1/2 -translate-y-full bg-black rounded-lg shadow-lg"
		 style="width:400px;margin-left:-200px;">

		<div wire:click="setTooltip('Admins', null)"
			 class="border-b pb-1 mb-1 flex">
			<div class="font-bold">
			 	
			</div>
			<div class="flex-grow text-right">
				<i class="fas fa-times text-grey-lightest text-xl"></i>
			</div>
		</div>

		<div class="font-medium">
			Admins for "{{ $team->name }}"
		</div>

		<!-- // Main Part -->

		<div class="text-center" wire:loading>
			<i class="fas fa-cog text-orange-dark fa-spin text-3xl mt-4"></i>
		</div>

		<div class="text-left my-2 border-t py-2" wire:loading.remove>

			@php
				$start = microtime(true);
			@endphp

			@php
				$users = $team->usersAll()
						  ->where('active', true)
						  ->get()
						  ->each(function ($item) use ($team) {
						  		$item['the_clicks'] = 
						  				$item->userLogs()
						  					 ->where('team_id', $team->id)
						  					 ->whereNull('type')
						  					 ->whereNull('mock_id')
						  					 ->where('created_at', '>', \Carbon\Carbon::now()->subMonths(1))
						  					 ->count();
						  })
						  ->reject(function ($item) {
						  	return (!$item->permissions || $item->permissions->guest);
						  })
						  ->sortByDesc('the_clicks')
			@endphp

			@if($users->first())
				
				<form wire:submit.prevent="setAdmins({{ $team->id }})">

				<div class="flex">
					<div class="truncate p-1 flex-grow">
					</div>
					<div class="truncate p-1 w-48 text-right text-yellow text-xs">Clicks in the last month</div>
				</div>

				@foreach($users as $user)

					<div class="flex {{ (!$loop->last) ? 'border-b' : '' }} border-grey-dark border-dashed py-1">
						<div class="truncate p-1 flex-grow">
							<label for="toggle_admin_{{ $team->id }}_{{ $user->id }}"
								   class="font-normal"
								   >
								<input type="checkbox"
								   name="insert_admins[]"
								   wire:model.defer="insert_admins.{{ $user->id }}"
								   value="1"
								   id="toggle_admin_{{ $team->id }}_{{ $user->id }}"
								   
								   {!! ($user->permissionsForTeam($team)->admin) ? 'checked' : '' !!}
								   >
								{{ $user->name }}
							</label>
						</div>
						<div class="truncate p-1 text-right w-24 text-yellow">{{ number_format($user->the_clicks) }}</div>
						<div class="truncate p-1 text-right w-24 text-grey-light">{{ \Carbon\Carbon::parse($user->last_activity)->format('n/j/y') }}</div>
					</div>

				@endforeach



				<div class="text-center mt-2">

					<button type="submit"
							class="rounded-lg bg-blue text-white px-3 py-2 hover:bg-white hover:text-blue-dark"
							>
						Save These as Admins
					</button>

				</div>

				<div class="p-2 text-sm text-grey-light border border-dashed my-4">

					<div class="font-mono text-grey-dark text-xs py-1">

						@php
							$admin_users_time = microtime(true) - $start;
						@endphp

				    	User processing took {{ number_format($admin_users_time, 3) }}

				    </div>

					{{ print_r($insert_admins)}}
				</div>

				</form>

			@else

				No Users in this Team.

			@endif

		</div>
		<!-- // Main Part -->

	</div>

</div>