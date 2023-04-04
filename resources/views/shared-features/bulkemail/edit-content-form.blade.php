

<div class="">

	<table class="w-full">
		

		<div class="text-left flex">
			<div class="w-1/6 pt-4 text-sm uppercase font-bold text-grey-darker">
				Sent From
			</div>
			<div class="w-5/6 p-2 text-left">
				<div class="w-full">
					@if($email->queued)
						{{ $email->sent_from }} &lt;{{ $email->sent_from_email }}&gt;
					@else
						<div class="w-full flex">
							<div class="w-1/3 pr-2">
								<select id="previous-senders" name="previous_sent_from" class="form-control">
									<option value="">
										~ Previous Sent From ~
									</option>
									@foreach ($bulk_email_senders as $sender)
										<option value="{{ $sender->sent_from_email }}" email="{{ $sender->sent_from_email }}" name="{{ $sender->sent_from }}">
											{{ $sender->sent_from }} &lt;{{ $sender->sent_from_email }}&gt;
										</option>
									@endforeach
								</select>
								
							</div>
							<div class="w-2/3">
								<input id="sent-from" type="text" name="sent_from" placeholder="Name" class="border rounded-lg p-2 w-full font-semibold text-grey-darker" value="{{ $errors->any() ? old('sent_from') : $email->sent_from }}" />
								
							</div>
						</div>
						<div class="w-full flex pt-2">
							<div class="w-1/3">
							</div>
							<div class="w-2/3">
								<input id="sent-from-email" type="text" name="sent_from_email" placeholder="Email" class="border rounded-lg p-2 w-full font-semibold text-grey-darker" value="{{ $errors->any() ? old('sent_from_email') : $email->sent_from_email }}" />
							</div>
						</div>
					@endif

				</div>
			</div>
		</div>

		<div class="text-left flex">
			<div class="w-1/6 pt-4 text-sm uppercase font-bold text-grey-darker">
				Subject
			</div>
			<div class="w-5/6 p-2 text-left">
				@if($email->queued)
					<div class="font-semibold text-blue">
						{{ $email->subject }}
					</div>
				@else
					<input type="text" name="subject" maxlength="90" placeholder="Email Subject Line" class="border rounded-lg p-2 w-full font-semibold text-grey-darker" value="{{ $errors->any() ? old('subject') : $email->subject }}" />
				@endif
			</div>
		</div>

		@if(!$email->queued)
			<div class="text-left flex">
				<div class="w-1/6 pt-2 text-sm uppercase font-bold text-grey-darker">
					Merge Field
				</div>
				<div class="w-5/6 p-2 text-left leading-loose">
					<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2 whitespace-no-wrap" value="full_name">+ Full Name</div>
					<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2 whitespace-no-wrap" value="title">+ Title</div>
					<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2 whitespace-no-wrap" value="first_name">+ First Name</div>
					<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2 whitespace-no-wrap" value="last_name">+ Last Name</div>
					<div class="bg-blue text-white inline border rounded-full px-2 py-1 text-sm hover:bg-blue-dark cursor-pointer mr-2 whitespace-no-wrap" id="add_image">+ Previous Image</div>
				</div>
			</div>
		@endif

		<div id="images" class="hidden text-blue">
			<div class="border-b-4 border-blue font-bold my-2">
				My Uploaded Files
			</div>
			@foreach($pictures as $thepic)
			<div class="flex">
				<div class="w-1/3 text-grey-dark pr-2">
					{{ $thepic['time'] }}
				</div>
				<div class="dynamic-picture list-none cursor-pointer hover:font-bold" data-url="{{ config('app.url') }}/{{ $thepic['url'] }}" data-filename="{{ $thepic['file_name'] }}">

					<div class="rounded-lg bg-blue text-xs text-white px-2 py-1 mr-1 inline-block">Add</div>
					<i class="fas fa-image mr-2"></i> {{ $thepic['name'] }}
				</div>
			</div>
			@endforeach
		</div>

		<div class="text-left flex">
<!-- 
			<div class="w-1/6 py-2 align-top text-sm uppercase pt-8 font-bold text-grey-darker">
				Body
			</div> -->

			<div class="w-full text-left p-2">

				@if(!$email->queued)

					<textarea id="summernote" name="content" rows="6" id="email-content">{{ $email->content }}</textarea>

				@else

					<div class="font-semibold text-left text-red text-sm py-2">
						Your email has be queued/sent so it is not editable.
					</div>

					<div class="border p-4 mb-2 shadow">
						{!! $email->content !!}
					</div>

				@endif

			</div>
		</div>


		<div class="text-left flex pt-2">
<!-- 
			<div class="w-1/6 py-2 align-top text-sm font-bold text-grey-darker align-top">



			</div> -->

			<!-- <div class="w-full text-left p-2 align-top">


				<div class="text-lg font-bold text-black border-b-2 border-grey">
					Plain Text
				</div>

				<span class="text-grey-dark italic text-xs mt-2 font-semibold">
					A plain text version is needed to send properly formatted emails.
				</span>

				@if(!$email->queued)

					

					<label class="checkbox-inline pt-1 text-blue font-semibold text-sm">
						<input type="checkbox" name="refresh_plain" {{ ($email->refresh_plain) ? 'checked' : '' }}>
						Automatically refresh plain text based on HTML version when saving
					</label>

					<textarea name="content_plain" rows="18" id="email-content-plain" class="border p-2 rounded-lg w-full mt-4 text-grey-darker text-sm font-mono">{{ $email->content_plain }}</textarea>


				@else

					<div class="font-semibold text-left text-red text-sm py-2">
						Your email has be queued/sent so it is not editable.
					</div>

					<div class="w-full border p-4 mb-2 text-grey-darker text-sm font-mono shadow">
						{!! nl2br($email->content_plain) !!}
					</div>

				@endif

			</div> -->
		</div>
	</table>

</div>
