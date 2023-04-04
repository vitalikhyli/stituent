@extends(Auth::user()->team->app_type.'.base')


@section('title')
    Edit Note
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Edit Note', 'edit_note') !!}


@endsection

@section('style')

	@livewireStyles()
	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')


<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}{{ $form_action }}/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		@if($thecontact->case_id)
		<a href="/{{ Auth::user()->team->app_type }}/contacts/{{ $thecontact->id }}/convert_to_case">
		  	<button type="button" data-toggle="modal" class="rounded-lg px-4 py-2 text-blue text-sm text-center mt-2 float-right"/>
		      <i class="fas fa-link mr-2"></i> Link to a different case
		    </button>
		</a>
	    @endif

	  	<button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 text-red text-sm text-center mt-2 float-right"/>
	      <i class="fas fa-exclamation-triangle mr-2"></i> Delete this Note
	    </button>

		<i class="fas fa-comments mr-2"></i>
		Edit Note

			
	</div>


	<table class="text-base w-full">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Subject:
			</td>
			<td class="p-2">
				<input type="text" name="subject" class="border-2 rounded-lg px-4 py-2 w-full"  value="{{ $thecontact->subject }}" />
			</td>
		</tr>

		@if($thecontact->case)
			<tr class="border-b">
				<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
					Connected to case:
				</td>
				<td class="p-2">
					{{ $thecontact->case->subject }}
				</td>
			</tr>
		@endif

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-32">

				People:					

			</td>
			<td class="p-2" wire:key="connector_contact">

				@livewire('connector', [
										'class' => 'App\Person',
										'model' => $thecontact,
										'show_linked' => true
										])

			</td>
		</tr>
<!-- 
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				User
			</td>
			<td class="p-2">


				@if($thecontact->user->team->id == $thecontact->team->id)

					<select name="user_id">
						@foreach($thecontact->team->users->sortBy('name') as $user)
							<option value="{{ $user->id }}" {{ ($user->id == $thecontact->user_id) ? 'selected' : '' }} >
								{{ $user->name }}
							</option>
						@endforeach
					</select>

				@else

					{{ $thecontact->user->name }}

				@endif

			</td>
		</tr> -->

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				<i class="fa fa-lock text-blue mr-1"></i>
			</td>
			<td class="p-2 font-bold">

				<label for="false" class="mr-4 mt-1">
				<input type="radio" name="private" id="false" value="false" {{ (!$thecontact->private) ? 'checked' : '' }} /> Staff
				</label>

				<label for="true" class="mr-4 mt-1">
				<input type="radio" name="private" id="true" value="true" {{ ($thecontact->private) ? 'checked' : '' }} />

				Private (
				    @if(Auth::user()->permissions->admin)
				    	<select name="user_id">
				    		@foreach(Auth::user()->team->usersAll as $user)
				    			<option
				    				@if($thecontact->user_id == $user->id)
				    					selected
				    				@endif
				    				value="{{ $user->id }}"
				    			>{{ $user->name }}</option>
				    		@endforeach
				    	</select>
				    @else
				    	{{ $thecontact->user->name }}
				    @endif

			    & Admins)

				</label>


			</td>
		</tr>


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
				Type
			</td>
			<td class="p-2">

                <select name="type" class="whitspace-no-wrap w-3/6">

                    <option value="" {{ ($thecontact->type == "") ? 'selected' : '' }}>
                    	-- None --
                	</option>

                    @foreach(Auth::user()->team->contactTypes() as $key => $type)

                    	<option value="{{ $type }}" {{ ($thecontact->type == $type) ? 'selected' : '' }}>{{ ucwords($type) }}</option>
                      
                    @endforeach

               </select>

				<input type="text" name="type-other" autocomplete="off" placeholder="Other?" class="border px-4 py-2 text-lg text-black rounded-lg ml-2" />

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				Date / Time
			</td>
			<td class="p-2">

				<input type="text" name="date" value="{{ \Carbon\Carbon::parse($thecontact->date)->format('m/d/Y') }}" class="datepicker border-2 rounded-lg px-4 py-2 w-1/5" />

				<input type="text" name="time" value="{{ \Carbon\Carbon::parse($thecontact->date)->format('g:i A') }}" class="border-2 rounded-lg px-4 py-2 w-1/5" />				

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
					Follow up on: <input placeholder="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" type="text" name="followup_on" id="followup_on" value="{{ (!$thecontact->followup_on) ? '' : \Carbon\Carbon::parse($thecontact->followup_on)->format('m/d/Y') }}" class="datepicker border-2 rounded-lg px-4 py-2 w-1/3 ml-2" />

					<label for="followup_done" class="font-normal px-2">
						<input type="checkbox" id="followup_done" name="followup_done" value="1" {{ ($thecontact->followup_done) ? 'checked' : '' }} /> Done
					</label>
				</div>

			</td>
		</tr>


	</table>

  <div class="float-right pt-2 text-sm">

		<button formaction="/{{ Auth::user()->team->app_type }}{{ $form_action }}/update/close" class="rounded-lg bg-blue-darker hover:bg-orange-dark text-white float-right text-base px-8 py-2 mt-1 ml-2 shadow">
			Save and Close
		</button>

		<button class="rounded-lg bg-blue hover:bg-orange-dark text-white float-right text-base px-8 py-2 mt-1 shadow">
			Save
		</button>

  </div>

</form>

<br />
<br />

<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this note?
          </div>
          @if($thecontact->people->count() >0 )
	          <div class="text-left font-bold py-2 text-base">
	            This will delete the note for <u>all {{ $thecontact->people->count() }} people connected to it.
	          </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}{{ $form_action }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

@endsection

@section('javascript')

@livewireScripts()

<script type="text/javascript">

function getSearchData(v) {
	if (v == '') {
	  $('#lookup-list').addClass('hidden');
	}
	$.get('/{{ Auth::user()->team->app_type }}/contacts/lookup/'+v, function(response) {
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
	    	$("#lookup-add").append('<div><input type="checkbox" name="add-entity-'+entity_id +'" id="add-entity-'+entity_id +'" checked><label class="ml-2" for="add-entity-'+entity_id +'" >'+name+'</label></div>');
	    }

	    person_id = $(this).data('person_id');
	    if (person_id != "") {
	    	$("#lookup-add").append('<div><input type="checkbox" name="add-person-'+person_id+'" id="add-person-'+person_id+'" checked><label class="ml-2" for="add-person-'+person_id+'" >'+name+'</label></div>');
	    }

	    $("#lookup").val(name);
	    $("#lookup").select();
	 });

});
</script>
@endsection