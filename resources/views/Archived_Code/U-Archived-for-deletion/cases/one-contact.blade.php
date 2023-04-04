<?php if (!defined('dir')) define('dir','/u'); ?>

@if($thecontact->followup)
<div id="followup_{{ $thecontact->id }}" class="{{ ($thecontact->followup_done) ? 'bg-grey-lighter text-grey' : 'bg-orange-lightest' }} p-2 border rounded-lg mt-2 w-full">

	<i class="fas fa-hand-point-right"></i>

	Follow up

	@if($thecontact->followup_on)
		on {{ $thecontact->followup_on }}
	@endif

	<label for="{{ $thecontact->id }}" class="float-right font-normal px-2">
		<input type="checkbox" data-id="{{ $thecontact->id }}" class="contact_followup" id="{{ $thecontact->id }}" name="{{ $thecontact->id }}" value="1" {{ ($thecontact->followup_done) ? 'checked' : '' }} /> Done
	</label>

</div>
@endif

<div class="group cursor-pointer border-b hover:bg-blue-lightest p-2 pb-4">

	<div class="flex pb-1 w-full">

		<div class="flex-1 flex-initial px-2 w-1/6 text-sm font-bold">
			{{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}
			<span class="text-grey-dark text-sm">
				@if(substr($thecontact->date,-8) != '00:00:00')
					{{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
				@endif
			</span>
		</div>

	

<!-- 		<div class="px-2 w-5/6">


			<span class="text-blue">{{ $thecontact->subject }}</span>


		</div> -->


		<div class="flex-1 flex-initial px-2 w-5/6">

            <div class="float-right w-12 ml-2">
                <a href="{{dir}}/cases/{{ $thecase->id }}/contacts/{{ $thecontact->id }}/edit">
                <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                    Edit
                </button>
                </a>
            </div>

			{{ $thecontact->notes }}
		</div>

	</div>

	@if(false)
	<div class="w-full flex text-left">
		<div class="flex-1 flex-initial px-2 w-1/6">
		</div>
		<div class="flex-1 flex-initial px-2 w-5/6">

			@if($thecontact->people->count() > 0)
				@foreach($thecontact->people as $theperson)
					<a href="{{dir}}/constituents/{{ $theperson->id }}" class="text-grey-dark">
						<span class="{{ ($thecontact->resolved) ? 'line-through bg-grey-lightest' : 'bg-grey-lighter' }}  hover:bg-blue hover:text-white rounded-lg mr-2 px-1 py-1 text-sm">
							{{ $theperson->full_name }}
						</span>
					</a>
				@endforeach
			@endif
		</div>
	</div>
	@endif

</div>

