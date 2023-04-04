<div>

	<div>
		@include('livewire.admin.set-up.include-account')
	</div>

	<div>
		@if($account)

			@include('livewire.admin.set-up.include-team')

			<div>
				@if($team &&
						(
							($team->app_type != 'office') ||
							($team->app_type == 'office' && $team->hasPresetCats())
						)
				   )

					@include('livewire.admin.set-up.include-users')

					<div>
						@if($team && \App\Permission::whereIn('user_id', $team->usersAll->pluck('id')->toArray())
													   ->where('team_id', $this->team_id)
													   ->where('admin', true)
													   ->first())

							@include('livewire.admin.set-up.include-voterfile')

							<div>
								
								@if($team && $team->db_slice)

							    	<div class="mt-4 text-4xl font-bold text-blue border-t-4 border-b-4 border-blue py-4 text-center bg-blue-lightest">

							    		<i class="fas fa-check-circle"></i> Set Up Complete, Chief

							                    <img src="/images/marketing/bruce-wayne.png"
							                    	 class="float-right h-32" />
							    	</div>

							    	<div class="mt-2">
							    		
							    		@foreach($team->usersAll as $login_as_user)

							    			<div class="py-4 border-b">

												<span class="mr-2">Log in as:</span>

							                    <a href="/admin/mock/{{ $login_as_user->id }}"
							                       target="new">
							                        <button class="rounded-lg bg-blue text-white px-4 py-2 hover:bg-blue-darker" type="button">
							                            {{ $login_as_user->name }}
							                        </button>
							                    </a>



							                </div>

										@endforeach

							    	</div>

							    @endif

							</div>

						@endif
					</div>


				@endif
			</div>


			

		@endif
	</div>

	

</div>