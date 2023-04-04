<div>

	<div class="">

		<div class="py-2">
			<i class="fas fa-plus mr-2 w-4"></i>
			<input type="input"
				   class="border p-2 text-sm"
				   placeholder="Add New Volunteer..." 
				   wire:model.debounce="lookup" />
		</div>

		<div class="py-2">
			<i class="fas fa-search mr-2 w-4"></i>
			<input type="input"
				   class="border p-2 text-sm w-64"
				   placeholder="Search Volunteers..." 
				   wire:model.debounce="search"
				   id="search" />
		</div>

		@if($lookup)
			<div class="p-4 absolute w-2/5 border shadow-lg bg-white z-50">

				<div class="py-2 pb-3 border-b-2 font-bold text-sm text-right">

					<button class="inline rounded-lg bg-blue hover:bg-blue-dark text-white px-4 py-2 font-normal"
							wire:click="createParticipant()">
						Create New Participant: "{{ $lookup }}"
					</button>

				</div>

				@if(!$lookup_results->first())

					<div class="text-grey-darker py-2">
						No Participants / Voters found.
					</div>

				@endif

				@foreach($lookup_results as $result)
					<div wire:key="lookup_result_{{ $result->id }}"
						 class="{{ (!$loop->last) ? 'border-b' : '' }} p-1 flex w-full cursor-pointer hover:bg-blue-lightest"
						 wire:click="addVolunteer('{{ $result->id }}')">

						<div class="flex-grow truncate w-24">
							{{ $result->full_name }}
						</div>

						<div class="w-2 rounded-lg text-blue float-right">
							+
						</div>

					</div>
				@endforeach
			
			</div>
		@endif

	</div>


    <div class="mt-8 w-full text-sm text-grey-darker full table">

			<div class="table-row uppercase bg-grey-lighter text-grey-dark text-xs">

				<div class="text-center table-cell border-b border-r w-8">
					#
				</div>

				<div class="p-2 table-cell border-b border-r w-1/5">
					name
				</div>

				@if($show_emails)
					<div class="p-2 table-cell border-b border-r w-4">
						
					</div>
				@endif
				
				<div class="p-2 table-cell border-b w-1/5">

					<label for="show_emails" class="font-normal pl-2">
						<input type="checkbox" class="cursor-pointer text-blue"
							  checked="checked" disabled="disabled" id="show_emails">
						Email
					</label>


					<label for="show_phones" class="font-normal pl-2">
						<input type="checkbox" class="cursor-pointer"
							  wire:model="show_phones" id="show_phones">
						Phone
					</label>

				</div>

				@foreach($volunteer_options as $option)

					
						@if($filter == $option)
							<div class="relative table-cell font-bold text-black w-8 p-0 border-b text-center z-20">
								<div wire:click="setFilter('')" class="cursor-pointer" >
									<div class="z-10 absolute w-full -ml-1 -mb-1 whitespace-no-wrap p-4 ml-2 pin-t" style="transform: rotate(-60deg);">
										{{ str_replace('_',' ',substr($option,10)) }}
									</div>
								</div>
							</div>
						@else
							<div class="relative table-cell w-8 p-0 border-b text-center z-20">
								<div wire:click="setFilter('{{ $option }}')" class="cursor-pointer" >
									<div class="z-10 absolute -ml-1 w-full whitespace-no-wrap p-4 ml-2 pin-t" style="transform: rotate(-60deg);">
										{{ str_replace('_',' ',substr($option,10)) }}
									</div>
								</div>
							</div>
						@endif
					

				@endforeach

			</div>

			
			@foreach($participants as $participant)

				<div class="table-row">

					<div class="text-center table-cell border-b border-r text-grey text-xs">
						{{ $loop->iteration }}
					</div>

					<div class="px-2 table-cell border-b border-r border-b">
						<a href="/campaign/participants/{{ $participant->id }}/edit">
							{{ $participant->full_name }}
						</a>
					</div>


					@if($show_emails)

						<div wire:click="addRecipient('{{ $participant->primary_email }}')" class="px-2 table-cell hover:text-black text-center border-b border-r cursor-pointer">
							<i class="fa fa-plus"></i>
						</div>

					@endif

					<div class="px-2 table-cell border-b border-r relative">
						
						<div class="py-1 flex">
							<div class="py-1 mr-2">
								<i class="fas fa-envelope text-grey"></i>
							</div>
							<div class="py-1 flex-grow">
								@if ($emails[$participant->id]['edit'] && Auth::user()->permissions->admin)
									<input type="text" class="w-full bg-grey-lightest p-1" wire:model.debounce.1000ms="emails.{{ $participant->id }}.email" />
								@else
									<div class="w-full h-4 cursor-pointer flex-grow" wire:click="edit({{ $participant->id }})">
										{{ $emails[$participant->id]['email'] }}
									</div>
								@endif

								@if (filter_var($emails[$participant->id]['email'], FILTER_VALIDATE_EMAIL))
									<i class="fa fa-check text-green absolute pin-r pin-t m-3"></i>
								@endif
							</div>
						</div>

						@if($show_phones)
							<div class="py-1 flex border-t border-dashed">
								<div class="py-1 mr-2">
									<i class="fas fa-phone text-grey"></i>
								</div>
								<div class="py-1 flex-grow">
									@if ($phones[$participant->id]['edit'] && Auth::user()->permissions->admin)
										<input type="text" class="w-full bg-grey-lightest p-1" wire:model.debounce.1000ms="phones.{{ $participant->id }}.phone" />
									@else
										<div class="w-full h-4 cursor-pointer w-full" wire:click="editPhone({{ $participant->id }})">
											{{ $phones[$participant->id]['phone'] }}
										</div>
									@endif
								</div>
							</div>
						@endif

					</div>

					@foreach($volunteer_options as $option)


						@if (Auth::user()->permissions->admin)
							<div wire:click="toggleVolunteer('{{ $option }}', '{{ $participant->id }}')" class="group relative px-2 table-cell border-b z-30 border-r text-blue text-base text-center cursor-pointer" title="{{ str_replace('_', ' ', strtoupper($option)) }}">
								
								@if ($participant->campaignParticipant)
									@if($participant->campaignParticipant->$option)
										<i class="fas fa-check-circle  z-40"></i>
									@else
										<i class="group-hover:opacity-100 opacity-0 transition fas fa-check-circle text-grey z-40"></i>
									@endif
								@endif
								
							</div>
						@else

							<div class=" relative px-2 table-cell border-b z-30 border-r text-blue text-base text-center" title="{{ str_replace('_', ' ', strtoupper($option)) }}">
								
								@if ($participant->campaignParticipant)
									@if($participant->campaignParticipant->$option)
										<i class="fas fa-check-circle  z-40"></i>
									@endif
								@endif
								
							</div>

						@endif

						

					@endforeach

				</div>

			@endforeach
		</div>

		<div class="flex mt-16 border-b-4">
			<div class="w-1/2">
				<div class="flex w-full">
					<div class="w-2/3 text-xl text-black font-bold">
						Emailing Volunteers
					</div>
					<div class="w-1/3 text-right pr-8 pt-1">
						
					</div>
				</div>
			</div>
			<div class="w-1/2 pl-8">
				<div class="text-xl text-grey-dark font-bold">
					Previous emails
				</div>
			</div>
		</div>
		<div class="flex mt-2">
			<div class="w-1/2 pr-8 pt-8">

				@if ($show_emails)


					<div class="flex">
						<div class="w-1/2">
							<b>Recipients: (bcced)</b>
						</div>
						<div class="w-1/2 text-right">
							<div wire:click="clearRecipients()" class="text-sm text-blue hover:text-blue-dark font-bold cursor-pointer">
								Clear
							</div>
						</div>
					</div>
					<textarea class="w-full border-2 p-2 text-sm text-grey-dark" wire:model="recipients_final" rows="6"></textarea>
					
					<input wire:model.debounce.1000ms="subject" class="bg-grey-lightest rounded w-full p-2 border-2 mb-2" type="text" placeholder="Email subject" />

					<textarea wire:model.debounce.2000ms="body" class="p-2 w-full rounded bg-grey-lightest border-2" rows="10" placeholder="Email body"></textarea>

					<div class="flex items-center">
						<div class="w-2/3 flex ">
							<div class="p-2">
								From:
							</div>
							<div class="">
								<select wire:model="from_user_id" class="border-2 rounded px-2 mt-1 w-full py-1">
									@foreach ($from as $admin)
										<option value="{{ $admin->id }}">
											{{ $admin->name }}
											- {{ $admin->email }}
										</option>
									@endforeach
								</select>
							</div>

							
						</div>
						<div class="w-1/3">
							<!-- <div class="w-full text-right pr-6">
								<label for='cc' class="cursor-pointer">
									<input id="cc" type="radio" wire:model="carbon" value="cc" /> Cc</label>
								<label for='bcc' class="pl-4 cursor-pointer">
									<input id="bcc" type="radio" wire:model="carbon" value="bcc"  /> Bcc</label>
							</div> -->
							<!-- <div class="text-right">
								<i class="text-grey-dark text-sm">* Emails are BCCed</i>
							</div> -->

							<div wire:click="send()" class="cursor-pointer hover:bg-blue-dark 
								@if (!$from_user_id || !$subject || !$body || $sending || (count($recipients) < 1))
									hidden
								@endif
								rounded-full text-white bg-blue px-6 py-4 float-right cursor-pointer">
								Send Email
							</div>

							<div class="rounded-full text-white 
									@if ($from_user_id && $subject && $body && (count($recipients) > 0))
										hidden
									@endif
									bg-grey px-6 py-4 float-right cursor-pointer">
								Send Email
							</div>

							<div class="rounded-full text-white 
									@if (!$sending)
										hidden
									@endif
									bg-grey px-6 py-4 float-right">
								Sending...
							</div>
						</div>
					</div>

				@else

					@if (Auth::user()->admin)

					<div class="w-full text-center">
						<button wire:click="showEmails()" class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark">
								Start Email
						</button>

						<a href="/{{ Auth::user()->team->app_type }}/participants/export/?ids={{ json_encode($participants->pluck('id')) }}">
								<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark">
									Export CSV	
							</button>
						</a>

					</div>


					@else

						<div class="text-grey-dark text-center p-2">
							Admins can send volunteer emails. If you believe you should have Admin status, please discuss with your team or contact Peri at 617.699.4553.
						</div>

					@endif

				@endif

			</div>
			<div class="w-1/2 pl-8">
				@if (Auth::user()->admin)
					@foreach ($volunteer_emails as $ve)
						<div class="flex text-sm">
							<div class="w-1/5 text-grey p-2">
								{{ $ve->created_at->format('n/j/Y g:ia') }}
							</div>
							<div class="w-4/5 p-2">
								<b>{{ $ve->subject }}</b>
								<div class="text-grey-dark">
									<b>{{ count(explode(' ', $ve->recipients)) }} Recipients</b>
									Sent:
									@if ($ve->sent_at)
										{{ $ve->sent_at->format('n/j/Y g:ia') }}
									@else
										<i class="text-red">Not Sent</i>
									@endif
								</div>
								@if($ve->carbon == 'individual')
									<div class="text-blue">
										<i class="fas fa-user-circle"></i> Phonebank invite (individual)
									</div>
								@endif
							</div>
						</div>
					@endforeach
				@else
					{{ $volunteer_emails->count() }} volunteer emails have been sent.
				@endif
			</div>
		</div>
</div>
