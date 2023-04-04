<div>

<!----------------------------------------------------/ /-------------------------------------->

	<div wire:click="toggleOpen('emails');"
		 class="cursor-pointer font-medium border-b-2 bg-green-lightest px-3 py-1 mb-1 hover:shadow-lg hover:border-grey-darker">
		 @if(!$is_open['emails'])
		 	<i class="fas fa-plus-circle mr-1 text-grey-darker"></i>
		 @else
		 	<i class="fas fa-minus-circle mr-1 text-grey-darker"></i>
		 @endif
		Email Recipients

		 @if(!empty($selected_emails))
			 <span class="float-right text-green-dark">
			 	<i class="fas fa-check-circle text-green-dark"></i>
			 	{{ count($selected_emails) }}
			 </span>
		 @endisset

	</div>

	<div class="{{ (!$is_open['emails']) ? 'hidden' : '' }}  border-dashed border ">

 		<div class="py-1 whitespace-no-wrap flex p-2">

			<input type="text"
				   class="border p-2 rounded-lg w-3/4"
				   wire:model.debounce="lookup_email"
				   wire:key="lookup_email"
				   placeholder="Lookup email"
				   />

			<button wire:click="clearLookup('email')"
					class="rounded-lg bg-grey-lighter text-gray-darker p-2 border ml-1">
				Clear
			</button>

		</div>

		@if(!empty($selected_emails))
			<div class="bg-grey-lightest p-2 border-b-2 mx-2">
				
				<div class="p-2">
					These constituents will be <b>removed</b> if they got any of the following emails:
				</div>

			    @foreach($selected_emails_chosen as $email)

					<div class="mt-1 ml-2 flex border-grey-lighter w-full"
						 wire:key="email_div-chosen_{{ $email->id }}">

						<label for="selected_emails_{{ $email->id }}" class="font-normal truncate">

							<input id="selected_emails_{{ $email->id }}" 
								   type="checkbox" 
								   wire:model.debounce="selected_emails" 
								   wire:key="email-chosen_{{ $email->id }}" 
								   multiple="multiple" 
								   value="{{ $email->id }}" />

						{{ \Carbon\Carbon::parse($email->completed_at)->format("Y-m-d") }} - {{ $email->subject }}

						 @if ($email->old_tracker_code)
						 	({{ $email->old_tracker_code }})
						 @endif

						</label>

					</div>

				@endforeach
			</div>
		@endif

	    @foreach($emails as $email)

			<div class="mt-1 ml-2 flex border-grey-lighter w-full"
				 wire:key="email_div_{{ $email->id }}">

				<label for="selected_emails_{{ $email->id }}" class="font-normal truncate">

					<input id="selected_emails_{{ $email->id }}" 
						   type="checkbox" 
						   wire:model.debounce="selected_emails" 
						   wire:key="email_{{ $email->id }}" 
						   multiple="multiple" 
						   value="{{ $email->id }}" />
					

						{{ \Carbon\Carbon::parse($email->completed_at)->format("Y-m-d") }} - {{ $email->subject }}

						@if ($email->old_tracker_code)
							({{ $email->old_tracker_code }})
						@endif

				</label>

			</div>

		@endforeach

	</div>


</div>
