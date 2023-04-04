<div>

    <div class="py-2 flex">

    	@if(!$edit_script)
    	<div class="w-1/2 mr-2 h-48 overflow-scroll">

		    <div class="font-bold py-1 mb-2">
		    	Phone Script:

		    	<button class="rounded-lg bg-blue text-white px-2 py-1 float-right font-normal text-sm"
		    			wire:click="$toggle('edit_script')">
		    		Edit Script
		    	</button>

		    	<div class="font-normal mt-2 py-2 text-grey-dark">
		    		{!! nl2br($script) !!}
		    	</div>

			   	@if($just_changed['script'] != null)
			   		<div class="pt-1 text-sm text-blue" wire:poll.5000ms>
			   			<i class="fas fa-check-circle"></i> Script Updated
			   		</div>
			   	@endif

		    </div>

		</div>
    	@endif

		@if($edit_script)
    	<div class="w-1/2 mr-2">

		    <div class="font-bold py-1 mb-2">
		    	Phone Script:

		    		@if($copy_script)

						<div class="border-t font-normal py-2">

							<div class="my-2">
					    		<select class="border text-lg"
					    				wire:model="script_to_copy">
					    			<option value="">-- Select --</option>
					    			@foreach($other_scripts as $option)
					    				<option value="{{ $option->id }}">{{ \Carbon\Carbon::parse($option->created_at)->toDateString() }} - {{ $option->name }}</option>
					    			@endforeach
					    		</select>


		    					<button class="rounded-lg bg-grey-light text-grey-darker px-2 py-1 text-sm"
		    							wire:click="$set('copy_script', false)">
		    						Cancel
		    					</button>
					    	</div>

				    		@if($script_to_copy)

				    			<div class="p-6 bg-blue-lightest border border-grey-darker shadow">

					    			{!! App\CampaignList::find($script_to_copy)->scriptFormatted !!}

			    					<button class="rounded-lg bg-blue text-white px-4 py-2 text-sm text-lg"
			    							wire:click="copyScript()">
			    						@if($list->script)
			    							Replace Your Script with This
			    						@else
			    							Copy Script
			    						@endif
			    					</button>

			    					<button class="rounded-lg bg-grey-light text-grey-darker px-4 py-2 text-lg"
			    							wire:click="$set('copy_script', false)">
			    						Cancel
			    					</button>

								</div>

				    		@endif

			    		</div>

			    	@else
			    		<div class="text-blue p-2 cursor-pointer float-right font-normal"
			    			 wire:click="$set('copy_script', true)">Copy a Script</div>

			    	@endif

		    	

		    </div>

		    <textarea placeholder="Phone script / Hi, I'm calling from..."
		    		  class="border rounded-lg p-2 text-grey-darker w-full h-48"
		    		  wire:dirty.class="bg-yellow-light"
		    		  wire:model.lazy="script"></textarea>

	    	<button class="rounded-lg bg-green text-white px-2 py-1 float-right font-normal text-sm"
	    			wire:click="$toggle('edit_script')">
	    		Done Editing
	    	</button>

		   	@if($just_changed['script'] != null)
		   		<div class="pt-1 text-sm text-blue" wire:poll.5000ms>
		   			<i class="fas fa-check-circle"></i> Script Updated
		   		</div>
		   	@endif

		</div>
		@endif


		<div class="w-1/2 ml-2">

		    <div class="font-bold py-1 mb-2">
		    	Assign Volunteers
		    </div>

				<div class="flex text-sm mb-2">

				    <div class="flex border-b border-dashed pb-2 w-full">
						<div class="mx-2 text-grey-dark whitespace-no-wrap">
							Filter By:
						</div>

						<div>
							<div>

							    <label for="showAllVolunteers" class="font-normal mr-2">
									<input type="checkbox"
											wire:model="showAllVolunteers"
											id="showAllVolunteers" />
										All Volunteers
								</label>

							    <label for="showPhoneVolunteers" class="font-normal mr-2">
									<input type="checkbox"
											wire:model="showPhoneVolunteers"
											id="showPhoneVolunteers" />
										Phone Volunteers
								</label>

							    <label for="showTeam" class="font-normal mr-2">
									<input type="checkbox"
											wire:model="showTeam"
											id="showTeam" />
										Staff / Users
								</label>
							</div>

							<div class="text-grey-dark">
								<span class="text-blue font-bold">*</span> Only Participants with emails will show up in this search.
							</div>

						</div>
					</div>




				</div>

			    <input type="text"
			    	   class="border p-2"
			    	   placeholder="Volunteer Lookup"
			    	   wire:model="lookup" />

		    	<div class="whitespace-no-wrap float-right py-1">
					
				    <button class="rounded-lg bg-blue text-white px-4 py-1
				    		@if($showAll)
				    			hidden
				    		@endif
				    		"
				    		wire:click="$toggle('showAll')">
				    	Show All
				    </button>

				    <button class="rounded-lg bg-grey-light px-4 py-1
				    		@if(!$showAll)
				    			hidden
				    		@endif
				    		"
				    		wire:click="$toggle('showAll')">
				    	Hide All
				    </button>

				</div>

			@if($lookup || $showAll)

				<div class="border absolute z-10 bg-white shadow-lg p-2" style="width:520px;">

					    <button class="rounded-lg px-2 py-1 float-right"
					    		 wire:click="$toggle('showAll')">
					    	<i class="fas fa-times"></i>
					    </button>

					<div class="p-2">

						<div class="font-bold py-1 mb-2">
					    	Create New
					    </div>

			    		<div class="flex">

			    			<div class="w-1/3 pr-2">
							    <input type="text"
							    	   class="border p-2 w-full"
							    	   placeholder="Name"
							    	   wire:model="lookup" />
						    </div>

							<div class="w-1/3 pr-2">
							    <input type="text"
							    	   class="border p-2 w-full"
							    	   placeholder="Email"
							    	   wire:model="new_email" />
						    </div>

							<div class="w-1/3">
							   	
									<button class="rounded-lg bg-blue text-white px-2 py-2
													@if(!$new_validated)
														hidden
													@endif
												  "
											type="button"
											wire:click="createVolunteerAndLink()"
											>
										Add New
									</button>

							</div>

						</div>

						@foreach($display_errors as $error)
							<div class="py-2 text-red w-4/5">
								{{ $error }}
							</div>
						@endforeach

					</div>

					<div class="p-2">

						@if($existing->first())

							<div class="font-bold py-1">
						    	Participants & Staff
						    </div>

						@endif

						@foreach($existing as $option)

							<div class="flex w-full hover:bg-blue-lightest cursor-pointer"
								 wire:click="linkVolunteer('{{ $option->id }}', '{{ $option->class }}')">

								<div class="p-2 {{ (!$loop->last) ? 'border-b' : '' }} w-10">
									<button class="rounded-lg bg-blue text-xs text-white px-2 py-1">
										Add
									</button>
								</div>
								<div class="flex-grow p-2 pl-4 {{ (!$loop->last) ? 'border-b' : '' }}">
									{{ $option->name }}

									@if($option->class == 'User')
										<div class="px-2 uppercase float-right text-xs rounded-lg bg-orange-lightest border">Staff</div>
									@endif

									@if($option->class == 'Participant')
										<!-- <div class="px-2 uppercase float-right text-xs rounded-lg bg-orange-lightest border">Volunteer</div> -->
									@endif

								</div>

								<div class="w-1/3 truncate p-2 pl-4 {{ (!$loop->last) ? 'border-b' : '' }} text-grey-dark text-sm">
									{{ $option->email }}
								</div>
							</div>

						@endforeach

					</div>
				</div>

			@endif

		</div>

	</div>


    <div class="font-bold py-1 mt-2 mb-1">

    	<div>
	    	Volunteers
	    	@if($assignments->first())
	    		({{ $assignments_count }})
	    	@endif

	    </div>

	    <div class="flex">

	    	<div class="font-normal pt-2">
				<button class="rounded-lg px-3 py-1 border text-sm
							   @if($email_mode)
							   		bg-blue text-white
							   @else
							   		bg-grey-lightest text-grey-darkest 
							   @endif
							   "
						wire:click="$toggle('email_mode')">
					<i class="fas fa-envelope mr-1"></i> Email links to volunteers
				</button>
			</div>

			<div class="pt-2 ml-4">
				<i class="fas fa-search mr-1 w-4 text-blue"></i>
				<input type="input"
					   class="border p-1 text-sm text-blue font-normal"
					   placeholder="Search Assignments..." 
					   wire:model.debounce="search"
					   id="search" />
			</div>

			<div class="flex-grow">
				<!-- Spacer -->
			</div>

	    	<div class="text-right pt-2">

	    		<div class="font-normal text-sm flex">
				   	@if($just_changed['new_expiration'] != null)
				   		<div class="pt-1 text-sm text-blue mr-2" wire:poll.5000ms>
				   			<i class="fas fa-check-circle"></i> Updated
				   		</div>
				   	@endif

			    	<div>
			    		<input type="text"
			    			   class="p-1 border text-sm w-32"
			    			   placeholder="{{ \Carbon\Carbon::now()->addDays(7)->format('n/j/y g:i A') }}"
			    			   wire:model="new_expiration"
			    			   wire:keydown.enter="resetExpiration()" />
			    	</div>

			    	<div class="ml-1">
						<button class="{{ ($new_expiration_validated) ? '' : 'hidden' }} rounded-lg bg-blue text-white px-2 py-1 border text-sm"
								wire:click="resetExpiration()">
							Reset expirations for all
						</button>
						<button class="{{ (!$new_expiration_validated) ? '' : 'hidden' }} rounded-lg bg-grey-lighter text-grey-darkest px-2 py-1 border text-sm opacity-50">
							Reset expirations for all
						</button>
					</div>
				</div>


			</div>

    	</div>

    </div>


    @if($email_mode)

	    <div class="border-t-4 border-b-4 border-blue mt-1 p-6 flex"
	    	 wire:key="email_mode_div">

	    	@if(!$edit_email)
	    	<div class="w-1/2 pr-2">

	    		<div class="h-48 overflow-scroll">
		    		<div class="border-b-2 py-2 font-bold">

		    			<i class="fas fa-envelope"></i> Subject: {{ $mail_data['subject'] }}

				    	<button class="rounded-lg bg-blue text-white px-2 py-1 float-right font-normal text-sm"
				    			wire:click="$toggle('edit_email')">
				    		Edit Email
				    	</button>
			    	
			    	</div>
			    	@if($mail_data['body1'])
			    		<div class="py-2 text-grey-dark">
				    		{!! nl2br($mail_data['body1']) !!}
				    	</div>
				    @endif
					<div class="py-2 text-grey-dark">
						Here's your unique login link: {{ config('app.url') }}/link/each_link_will_go_here
					</div>
					@if($mail_data['body2'])
						<div class="py-2 text-grey-dark">
							{!! nl2br($mail_data['body2']) !!}
						</div>
					@endif

				</div>

				@if($mail_data_validated)
					<div class="text-blue py-1 float-left">
						<i class="fas fa-check-circle"></i> Email looks good
					</div>
				@else
					<div class="text-red py-1 float-left">
						<i class="fas fa-times"></i> Required fields are blank
					</div>
				@endif

		    </div>



	    	@endif

	    	@if($edit_email)
	    	<div class="w-1/2 pr-2
		    			@if($email_mode_progress)
		    				opacity-50
		    			@endif
	    			   ">

				<div class="flex items-center">
					<div class="w-full flex ">
						<div class="py-2 pr-2">
							From:
						</div>
						<div class="flex-grow">
							<select wire:model="mail_data.from_user_id" class="border-2 rounded px-2 mt-1 w-full py-1">
								<option value="">
									-- None --
								</option>
								@foreach ($from as $fromarr)
									<option id="user_{{ $fromarr['id'] }}" value="{{ $fromarr['id'] }}">
										{{ $fromarr['name'] }} - {{ $fromarr['email'] }}
									</option>
								@endforeach
							</select>
						</div>
						
					</div>
				</div>

				<div class="flex">
					<div class="py-2 pr-2">
						Subject:
					</div>
					<input wire:model="mail_data.subject" class="bg-grey-lightest rounded w-full p-2 border-2 mb-2" type="text" placeholder="Email subject" />
				</div>

				<textarea wire:model="mail_data.body1" class="p-2 w-full rounded bg-grey-lightest border-2" rows="4" placeholder="Greeting / Email body beginning"></textarea>

				<textarea readonly="readonly" class=" -mt-2 -mb-2 p-2 w-full rounded bg-grey-lightest border-2" rows="2">Here's your unique login link: {{ config('app.url') }}/link/each_link_will_go_here</textarea>

				<textarea wire:model="mail_data.body2" class="p-2 w-full rounded bg-grey-lightest border-2" rows="4" placeholder="Signature / Email body end"></textarea>

				<div>
					@if($mail_data_validated)
						<div class="text-blue py-1 float-left">
							<i class="fas fa-check-circle"></i> Email looks good
						</div>
					@else
						<div class="text-red py-1 float-left">
							<i class="fas fa-times"></i> Required fields are blank
						</div>
					@endif

					<button class="rounded-lg bg-green text-white px-2 py-1 float-right font-normal text-sm"
			    			wire:click="$toggle('edit_email')">
			    		Done Editing
			    	</button>

			    </div>

			</div>

			@endif

			<div class="w-1/2 pl-2">

				<div class="mb-2">
					<button class="{{ ($email_mode_progress || !$mail_data_validated || $email_mode_progress_percentage == 100) ? 'hidden' : '' }} rounded-lg bg-blue text-white px-4 py-2 border-transparent"
					wire:click="$toggle('email_mode_progress')">
						Start Emailing
					</button>
					<button class="{{ (!$email_mode_progress) ? 'hidden' : '' }} rounded-lg bg-grey-lighter text-grey-darker px-4 py-2 border"
					wire:click="$toggle('email_mode_progress')">
						Stop Emailing
					</button>
				</div>

				@if($email_mode_progress)
					<div wire:poll.2000ms></div>
				@endif

				<div>

			    	<div class="text-lg font-bold text-blue mb-2">
			    		Sent: {{ $email_mode_progress_count }} of {{ $assignments_count }}
			    		@if($email_mode_progress_percentage == 100)
							<i class="fas fa-check-circle ml-1"></i>
						@endif
			    	</div>

				    <div class="border-4 w-full">

				    	@if($email_mode_progress_percentage > 0)
					    	<div class="bg-blue p-4 text-white h-8"
					    		 style="width:{{ $email_mode_progress_percentage }}%">
						    </div>
						@else
							<div class="p-4 text-white h-8">
						    </div>
					    @endif

					</div>

				</div>

			</div>

	    </div>

	@endif


	@if(!$assignments->first())

		<div class="py-2 text-grey-dark">
			None
		</div>

	@else

	    <div class="" wire:key="assignments_div">


	    	<div class="flex border-l border-t text-sm w-full">

	    		<div class="w-8 truncate border-b  p-2 bg-grey-lightest">
	    			
	    		</div>

	    		<div class="w-1/4 truncate border-b border-r p-2 bg-grey-lightest">
	    			Volunteer
	    		</div>

	    		<div class="w-1/3 truncate border-b border-r p-2 bg-grey-lightest">
	    			Phonebank Login Link
	    		</div>

	    		<div class="w-1/6 truncate border-b border-r p-2 bg-grey-lightest">
	    			Link Expires
	    		</div>

	    		<div class="w-24 truncate border-b border-r p-2 bg-grey-lightest">
	    			Emailed
	    		</div>

	    		<div class="flex-grow truncate border-b border-r p-2 bg-grey-lightest">
	    			# Clicks
	    		</div>

	    	</div>

		    @foreach($assignments as $ass)

		    	@if($ass->trashed())

		    		@if($ass->participant)

						<div class="flex border-l text-sm w-full">

				    		<div class="bg-blue-lightest truncate border-b border-r p-4 w-full">
				    			<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $ass->participant->id }}">
				    				{{ $ass->participant->full_name }}
				    			</a>
				    			isn't on this list, but is still in your Participants.

				    			<div class="float-right text-red cursor-pointer"
				    				 wire:click="forceDeleteAssignment('{{ $ass->id }}')">
				    				Remove this message
				    			</div>
				    		</div>

							
						</div>

					@endif

		    	@else


		    	<div class="flex border-l text-sm w-full"
		    		 wire:key="assignment_row_{{ $ass->id }}"
		    		 >

		    		<div class="w-8 truncate border-b border-r p-2 pt-3"
		    			 wire:key="row_{{ $ass->id }}_delete">
		    			<div class="text-red cursor-pointer"
		    				 wire:click="unassign('{{ $ass->id }}')">
		    				X
		    			</div>
		    		</div>

		    		<div class="w-1/4 truncate border-b border-r p-2 pt-3 text-grey-dark">

		    			@if($ass->user_id)
		    				@if(!$ass->participant)
		    					<div class="text-grey-darkest font-medium truncate">
		    						{{ $ass->user->name }}
									<span class="px-2 uppercase text-xs rounded-lg bg-orange-lightest border">Staff</span>
		    					</div>
		    				@else
		    					<a class="text-blue font-medium"
		    					   href="/{{ Auth::user()->team->app_type }}/participants/{{ $ass->participant_id }}">
		    						{{ $ass->user->name }}
		    					</a>
		    				@endif
		    				<div>{{ $ass->user->email }}</div>
		    			@endif
		    		</div>

		    		<div class="w-1/3 truncate border-b border-r p-2">
		    			<input type="text"
		    				   value="{{ config('app.url') }}/lists/{{ $ass->uuid }}"
		    				   class="border-b p-1 w-4/5"
		    				   id="{{ $ass->id }}" />

		 				<button onclick="copyToClipboard('{{ $ass->id }}')" type="button" class="mr-1 text-blue py-1 text-xs font-normal my-1 ml-2">
			    			<i class="fas fa-clipboard text-xs"></i> Copy
			    		</button>

		    		</div>

		    		<div class="w-1/6 truncate border-b border-r p-2 pt-3">
		    			@if(!$ass->expires_at)
		    				<span class="text-grey-dark">none</span>
		    			@else
		    				{{ \Carbon\Carbon::parse($ass->expires_at)->format('n/j/y @ g:i a') }}
		    				<div class="text-grey-dark text-xs">
		    					{{ \Carbon\Carbon::parse($ass->expires_at)->diffForHumans() }}
		    				</div>
		    			@endif
		    		</div>

		    		<div class="w-24 truncate border-b border-r p-2 text-center">
						@if($ass->emailed_at)
							<div class="text-blue">
								<i class="far fa-check-circle text-xs"></i>
			    				{{ \Carbon\Carbon::parse($ass->emailed_at)->format('n/j/y') }}
			    			</div>

			    			<div wire:click="clearEmailed({{ $ass->id }})"
			    				 class="text-red text-xs cursor-pointer hover:font-bold">
			    				Clear
			    			</div>
		    			@endif
		    		</div>

		    		<div class="flex-grow truncate border-b border-r p-2 pt-3">
		    			@if($ass->clicks_count)
		    				{{ $ass->clicks_count }}
		    			@else
		    				<span class="text-grey-dark">none</span>
		    			@endif
		    		</div>

		    	</div>

		    	@endif

		    @endforeach

		</div>

	@endif


</div>