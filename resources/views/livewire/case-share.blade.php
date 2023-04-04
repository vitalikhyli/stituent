<div class="my-2">


	
	@if ($case)

		@if ($case_id)

			<div class="mb-4 flex items-center border-b pb-2">

				<div>
					<a href="/{{ Auth::user()->app_type }}/cases/{{ $case->id }}" class="font-bold">{{ $case->subject }}</a> 
				</div>
				

				<div class="">
					@foreach ($case->people as $person)
						<div class="pl-4 text-sm">{{ $person->name }}</div>
					@endforeach
				</div>
				<div wire:click="switchCase()" class="w-1/3 cursor-pointer text-red text-right">
					Switch case
				</div>
			</div>

		@endif

		<div class="relative">

			@if (!$case_id)
				<div class="absolute bg-white pin-t pin-r border text-xs px-2 cursor-pointer" wire:click="toggleEditing()">
					@if ($editing)
						Done
					@else
						Edit
					@endif
				</div>
			@endif

			

			@if (!$editing)

				@if ($shared_cases->count() > 0)

					<div class="w-full">
					
						@foreach ($shared_cases as $sc)
							<div class="flex w-full">
								<div class="w-8">
									<i class="fa fa-check-circle text-blue"></i> 
								</div>
								<div class="">
									<div class="font-bold" title="{{ $sc->sharedUser->email }}">{{ $sc->name }}</div>
									<div class="text-xs text-grey">
										@if ($sc->shared_type == 'user')
											{{ $sc->sharedTeam->name }}
										@endif
										@if ($sc->shared_type == 'team')
											{{ $sc->sharedTeam->users()->count() }} users
										@endif
									</div>
								</div>
								
							</div>
						@endforeach
						
					</div>
				@else

					<div class="w-full flex px-2 italic text-grey">
						Not Shared
					</div>

				@endif

				

			@else

				<div class="items-center">

					<div class="">

						@if ($case->user_id == Auth::user()->id 
								|| (Auth::user()->permissions->admin && $case->team_id == Auth::user()->team_id)) 
							<div class="items-center">
								<div class="font-bold">
									Share with email:
								</div>
								<div class="mt-1">
									<input type="text" class="border-2 p-2 rounded-full w-64" wire:model="new_shared_email" placeholder="User Email" />
								</div>
							</div>
						@else
							<div class="text-grey-darker text-sm p-2">
								This case was started by <b class="text-black">{{ $case->user->name }}</b>.<br>
								Creator of case can add or remove shared users.

							</div>
						@endif

						@if ($new_shared_user)
							<div class="border p-2 m-2 shadow">
								<div class="text-sm text-gray-dark">
									<label class="flex p-1 font-normal">
										<input type="radio" wire:model="new_shared_type" value="user"/> 
										&nbsp;User:&nbsp; <b>{{ $new_shared_user->name }}</b>
									</label>
									<label class="flex p-1 font-normal">
										<input type="radio" wire:model="new_shared_type" value="team"/> 
										<div>&nbsp;Team:&nbsp;</div>
										<b>{{ $new_shared_team->name }}
										({{ $new_shared_team->users()->count() }} users)</b>
									</label>

								</div>
								<div class="p-2">
									<button wire:click="share()" class="bg-blue text-white px-3 py-2 rounded-full w-full">
										Share Case!
									</button>
								</div>
							</div>
						@endif
						
					</div>
					<div class="text-grey">
						
						@if ($shared_cases->count() > 0)

							<div class="border-b-2 pt-4">Currently shared with:</div>
							<div class="p-2 w-full">
							
								@foreach ($shared_cases as $sc)
									<div class="flex w-full">
										@if ($case->user_id == Auth::user()->id 
												|| (Auth::user()->permissions->admin && $case->team_id == Auth::user()->team_id)) 
											<div class="w-1/6 text-left hover:text-red cursor-pointer" wire:click="delete({{ $sc->id }})">
												<i class="fa fa-times"></i>
											</div>
										@endif
										<div class="w-5/6">
											{{ $loop->iteration }}. {{ strtoupper($sc->shared_type) }}: 
											<span class="font-bold text-black">{{ $sc->name }}</span>
											@if ($sc->shared_type == 'user')
												(part of {{ $sc->sharedTeam->name }})
											@endif
											@if ($sc->shared_type == 'team')
												({{ $sc->sharedTeam->users()->count() }} users)
											@endif
										</div>
										
									</div>
								@endforeach
								
							</div>
							@if ($case_id)
								<a class="float-right px-3 py-2 bg-blue-light rounded-full text-xs text-white hover:bg-blue hover:text-white" href="/{{ Auth::user()->app_type }}/shared-cases">
									Done Editing & Sharing
								</a>
							@endif
						@else

							<div class="pt-2">
								You can share this case with any combination of Community Fluency individual users or offices.
							</div>
						@endif
					</div>
				</div>

			@endif
		</div>
	@else
		<select wire:model="case_id" class="p-2 rounded-full border mt-2">
			<option value="">- Select Case -</option>
			@foreach ($allcases as $onecase)
				<option value="{{ $onecase->id }}">{{ $onecase->subject }}</option>
			@endforeach
		</select>
	@endif

	
</div>
