<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-2 mb-2">
	Emails
</div>
<div class="">

	<div class="pb-2">
		<input name="primary_email" value="{{$model->primary_email}}" class="border rounded px-4 py-2 w-1/2" placeholder="primary@email.com" />

		<span class="ml-2">Primary</span>
	</div>

	<div class="pb-2">
		<input name="work_email" value="{{$model->work_email}}" class="border rounded px-4 py-2 w-1/2" placeholder="work@email.com" />

		<span class="ml-2">Work</span>
	</div>

	@if($model->other_emails)
		@foreach($model->other_emails as $email)
			<div class="pb-2">
				<input name="email_{{ $loop->iteration }}" value="{{ $email[0] }}" class="border rounded px-4 py-2 w-1/2"/>
				<input name="email-notes_{{ $loop->iteration }}" placeholder="Notes" value="{{ $email[1] }}" class="border rounded px-4 py-2 w-1/3"/>
			</div>
		@endforeach
	@endif

	<div class="pb-2">
		<input name="email_new" placeholder="New@Email.Address" value="" class="border rounded px-4 py-2 w-1/2"/>
		<input name="email-notes_new" placeholder="Notes" class="border rounded px-4 py-2 w-1/3"/>
	</div>

</div>

<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-2 mb-2">
	Phones
</div>
<div class="">

	<div class="pb-2 flex">

		<div class="px-4 w-1/2 font-mono text-blue">
			@if ($model->voter)
				{{ $model->voter->readable_phone }}
			@endif
		</div>

		<span class="ml-2">Voter File Data</span>

	</div>

	<div class="pb-2">
		<input name="primary_phone" value="{{$model->primary_phone}}" class="border rounded px-4 py-2 w-1/2" placeholder="(617) 000-0000" />

		<span class="ml-2">Primary</span>
	</div>

	<div class="pb-2">
		<input name="cell_phone" value="{{$model->cell_phone}}" class="border rounded px-4 py-2 w-1/2" placeholder="(617) 000-0000" />

		<span class="ml-2">Cell</span>
	</div>

	<div class="pb-2">
		<input name="work_phone" value="{{$model->work_phone}}" class="border rounded px-4 py-2 w-1/2" placeholder="000-000-0000" />

		<span class="ml-2">Work</span>
	</div>



	@if($model->other_phones)
		@foreach($model->other_phones as $phone)
			<div class="pb-2">
				<input name="phone_{{ $loop->iteration }}" value="{{ $phone[0] }}" class="border rounded px-4 py-2 w-1/2"/>
				<input name="phone-notes_{{ $loop->iteration }}" placeholder="Notes" value="{{ $phone[1] }}" class="border rounded px-4 py-2 w-1/3"/>
			</div>
		@endforeach
	@endif

	<div class="pb-2">
		<input name="phone_new" placeholder="New Phone #" value="" class="border rounded px-4 py-2 w-1/2"/>
		<input name="phone-notes_new" placeholder="Notes" class="border rounded px-4 py-2 w-1/3"/>
	</div>

</div>

