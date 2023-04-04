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

<div class="group cursor-pointer border-b hover:bg-blue-lightest pb-4">

	<div class="flex pb-1 w-full pt-2 ">


		<div class="flex-1 flex-initial w-1/6 font-bold text-sm">

			{{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}

			<div class="text-grey-dark text-xs">
				@if(substr($thecontact->date,-8) != '00:00:00')
					{{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
				@endif
			</div>

		</div>


		<div class="flex-1 flex-initial w-5/6 px-2 mb-2">


			@if(!$thecontact->private)
				<div class="float-right text-right mb-2">
					<a data-toggle="tooltip" data-placement="top" title="Contact to Case" href="{{dir}}/contacts/{{ $thecontact->id }}/convert_to_case/{{ $person->id }}" class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1"><i class="fas fa-folder-open"></i></a>
				</div>
			@else
				<div data-toggle="tooltip" data-placement="top" title="Note is private" class="float-right text-right mb-2 border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1"">
					<i class="fas fa-lock"></i>
				</div>
			@endif

            <div class="float-right w-12 ml-2">
                <a href="{{dir}}/constituents/{{ $person->id }}/contacts/{{ $thecontact->id }}/edit">
                <button class="border shadow text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
                    Edit
                </button>
                </a>
            </div>


			{{ $thecontact->notes }}
		</div>



	</div>


	<div class="w-full flex text-left mt-2">
		<div class="flex-1 flex-initial px-2 w-1/6">
		</div>
		<div class="flex-1 flex-initial px-2 w-5/6">
			@if($thecontact->people->count() > 1)
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

	


</div>

