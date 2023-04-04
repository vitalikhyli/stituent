<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
	Emails
</div>
<div class="pl-2">

	<div class="">
		{{ $model->readable_phone }}
	</div>

	<div class="pb-2 flex">
		<div class="w-24">Primary</div>
		@if($model->primary_email)
			{{ $model->primary_email }}
		@else
			<span class="text-grey-dark">none</span>
		@endif
	</div>

	<div class="pb-2 flex">
		<div class="w-24">Work</div>
		@if($model->work_email)
			{{ $model->work_email }}
		@else
			<span class="text-grey-dark">none</span>
		@endif
	</div>

	@if($model->other_emails)
		
			
			@foreach($model->other_emails as $email)
				<div class="flex">
					@if($loop->first)
						<div class="font-bold w-24 ">Other</div>
					@else
						<div class="w-24"></div>
					@endif

					<div class="">
						{{ $email[0] }} <span class="text-grey-dark ml-2">- {{ $email[1] }}</span>
					</div>
				</div>
			@endforeach

	@endif

</div>


<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
	Phones
</div>
<div class="pl-2">

	<div class="pb-2 flex">
		<div class="w-24">Primary</div>
		@if($model->primary_phone)
			{{ $model->primary_phone }}
		@else
			<span class="text-grey-dark">none</span>
		@endif
	</div>

	<div class="pb-2 flex">
		<div class="w-24">Cell</div>
		@if($model->cell_phone)
			{{ $model->cell_phone }}
		@else
			<span class="text-grey-dark">none</span>
		@endif
	</div>

	<div class="pb-2 flex">
		<div class="w-24">Work</div>
		@if($model->work_phone)
			{{ $model->work_phone }}
		@else
			<span class="text-grey-dark">none</span>
		@endif
	</div>

	@if($model->other_phones)
		
			
			@foreach($model->other_phones as $phone)
				<div class="flex">
					@if($loop->first)
						<div class="font-bold w-24">Other</div>
					@else
						<div class="w-24"></div>
					@endif

					<div class="">
						{{ $phone[0] }} <span class="text-grey-dark ml-2">- {{ $phone[1] }}</span>
					</div>
				</div>
			@endforeach
		
	@endif

</div>