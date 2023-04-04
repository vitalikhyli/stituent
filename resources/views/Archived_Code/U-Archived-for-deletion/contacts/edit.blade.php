@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>


@section('title')
    Edit Note
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Edit Note', 'edit_note') !!}


@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="{{dir}}{{ $form_action }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<button class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save
		</button>


		<button formaction="{{dir}}{{ $form_action }}/update/close" class="rounded-full bg-blue-darker hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 mr-2 shadow">
			Save and Close
		</button>


		<i class="fas fa-comments mr-2"></i>
		Edit Note

			
	</div>



	<table class="text-base w-full">

		@if($thecontact->call_log)
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Info from Call Log:
			</td>
			<td class="p-2">
				<textarea rows="3" name="subject" class="border-2 rounded-lg px-4 py-2 w-full">{{ $thecontact->subject }}</textarea>

				<select name="type">
					<option value="email" {{ ($thecontact->type == 'email') ? 'selected' : '' }}>Email</option>

					<option value="call" {{ ($thecontact->type == 'call') ? 'selected' : '' }}>Call</option>

					<option value="visit" {{ ($thecontact->type == 'visit') ? 'selected' : '' }}>Visit</option>
				</select>

			</td>
		</tr>
		@endif

		@if($thecontact->case_id)
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Connected to case:
			</td>
			<td class="p-2">
				{{ $thecontact->case_id }}
			</td>
		</tr>
		@else
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Connected to this note:
			</td>
			<td class="p-2">
				@foreach($thecontact->people as $person)
					<div><input type="checkbox" name="add_person_{{ $person->id }}" id="add_person_{{ $person->id }}" checked><label class="ml-2" for="add_person_{{ $person->id }}" >{{ $person->full_name }}</label></div>
				@endforeach
				@foreach($thecontact->entities as $entity)
					<div><input type="checkbox" name="add_entity_{{ $entity->id }}" id="add_entity_{{ $entity->id }}" checked><label class="ml-2" for="add_entity_{{ $entity->id }}" >{{ $entity->name }}</label></div>
				@endforeach

				<div class="" id="lookup-add"></div>

					<input id="lookup"
						   type="text"
						   placeholder="Search People and Entitites"
						   name="lookup"
						   value=""
						   autocomplete="off"
						   class="w-1/2 border-2 px-4 py-3 text-sm font-bold bg-grey-lightest" />
					
				<div id="lookup-list" class="hidden"></div>

			</td>
		</tr>		
		@endif

		@if(!$thecontact->case_id)
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				<i class="fas fa-lock text-sm"></i>
			</td>
			<td class="p-2">

				<label for="false" class="mr-4 font-normal mt-1">
				<input type="radio" name="private" id="false" value="false" {{ (!$thecontact->private) ? 'checked' : '' }} /> All team members
				</label>

				<label for="true" class="mr-4 font-normal mt-1">
				<input type="radio" name="private" id="true" value="true" {{ ($thecontact->private) ? 'checked' : '' }} /> Only {{ Auth::user()->name }}
				</label>


			</td>
		</tr>
		@endif


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Note:
			</td>
			<td class="p-2">
				<textarea rows="5" name="notes" id="notes" class="border-2 rounded-lg px-4 py-2 w-full">{{ $thecontact->notes }}</textarea>
			</td>
		</tr>
		
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				Date / Time
			</td>
			<td class="p-2">
				<input type="text" name="date" value="{{ \Carbon\Carbon::parse($thecontact->date)->format("Y-m-d g:i A") }}" class="border-2 rounded-lg px-4 py-2 w-1/3" />
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				Created By
			</td>
			<td class="p-2">
				{{ $thecontact->user->name }} <span class="text-grey-dark hidden">on {{ $thecontact->created_at }}</span>
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				Followup
			</td>
			<td class="p-2 flex">

				<div class="p-2">
					<label for="followup" class="font-normal px-2">
						<input type="checkbox" id="followup" name="followup" value="1" {{ ($thecontact->followup) ? 'checked' : '' }} /> Yes
					</label>
				</div>

				<div id="set_follow_up_div" class="{{ ($thecontact->followup) ? '' : 'hidden' }} flex-1">
					Follow up on: <input placeholder="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" type="text" name="followup_on" id="followup_on" value="{{ (!$thecontact->followup_on) ? '' : \Carbon\Carbon::parse($thecontact->followup_on)->format("Y-m-d") }}" class="border-2 rounded-lg px-4 py-2 w-1/3 ml-2" />

					<label for="followup_done" class="font-normal px-2">
						<input type="checkbox" id="followup_done" name="followup_done" value="1" {{ ($thecontact->followup_done) ? 'checked' : '' }} /> Done
					</label>
				</div>

			</td>
		</tr>

	</table>

</form>

  <div class="float-left pt-2 text-sm">

      <a href="{{dir}}{{ $form_action }}/delete">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest hover:bg-black text-white text-center ml-2"/>
          <i class="fas fa-exclamation-triangle mr-2"></i> Delete this Note
        </button>
      </a>

  </div>


@endsection

@section('javascript')
<script type="text/javascript">

function getSearchData(v) {
	if (v == '') {
	  $('#lookup-list').addClass('hidden');
	}
	$.get('{{dir}}/contacts/lookup/'+v, function(response) {
	  if (response == '') {
	    $('#lookup-list').addClass('hidden');
	  } else {
	    $('#lookup-list').html(response);
	    $('#lookup-list').removeClass('hidden');
	  }
	});
}


$(document).ready(function() {

	$(document).on("click", "#followup", function() {
		if ($(this).is(':checked')) {
			$('#set_follow_up_div').removeClass('hidden');
		} else {
			$('#set_follow_up_div').addClass('hidden');
			$('#followup_on').val(null);
			$('#followup_done').prop("checked", false);
		}
	});


    $("#lookup").focusout(function(){
        window.setTimeout(function() {$('#lookup-list').addClass('hidden'); }, 300);
    });
      
    $("#lookup").keyup(function(){
		getSearchData(this.value);
    });


	$(document).on('click', ".lookup-search-result", function () {

	    name = $(this).data('name');

	    entity_id = $(this).data('entity_id');
	    if (entity_id != "") {
	    	$("#lookup-add").append('<div><input type="checkbox" name="add_entity_'+entity_id +'" id="add_entity_'+entity_id +'" checked><label class="ml-2" for="add_entity_'+entity_id +'" >'+name+'</label></div>');
	    }

	    person_id = $(this).data('person_id');
	    if (person_id != "") {
	    	$("#lookup-add").append('<div><input type="checkbox" name="add_person_'+person_id+'" id="add_person_'+person_id+'" checked><label class="ml-2" for="add_person_'+person_id+'" >'+name+'</label></div>');
	    }

	    $("#lookup").val(name);
	    $("#lookup").select();
	 });

});
</script>
@endsection