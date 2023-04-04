@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Case ID # {{ $thecase->id }}
@endsection

@section('breadcrumb')

    <a href="{{dir}}">Home</a> > 
    @if ($thecase->resolved)
    	<a href="{{dir}}/cases/all/resolved">Resolved Cases</a> 
	@else
		<a href="{{dir}}/cases/all/open">Open Cases</a> 
	@endif
    > &nbsp;<b>Case ID # {{ $thecase->id }}</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')


	<div class="text-2xl font-sans pb-3 text-blue-darker">
		<a href="{{dir}}/cases_edit/{{ $thecase->id }}">
		<button type="button" class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			EDIT
		</button>
		</a>

		@if($thecase->resolved)
			<span class="line-through">
				<i class="fa fa-folder-open mr-2"></i>
				{{ $thecase->subject }}
			</span>
			<i class="fas fa-check ml-2"></i>
		@else
			<i class="fa fa-folder-open mr-2"></i>
			{{ $thecase->subject }}
		@endif
	</div>

	<table class="text-base w-full border-t-4 border-blue">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-32">
				Type:
			</td>
			<td class="p-2">
				@if(!$thecase->type)
					<span class="text-grey">None selected</span>
				@else
					{{ $thecase->type }}
				@endif
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-32">
				People:
			</td>
			<td class="p-2">

				<div id="addothers_form" class="hidden text-sm w-1/2 mt-2">
					<input id="cfbar" type="text" placeholder="Look up People" style="font-family:Arial, Font Awesome\ 5 Free" data-toggle="dropdown" autocomplete="off" class="w-full text-black appearance-none rounded-full px-2 py-2 bg-grey-lightest border-2 border-grey focus:bg-white hover:shadow-outline shadow" />
				</div>

				<div id="existing_checkboxes" class="p-2 hidden">
				<form method="post" action="{{dir}}/cases/{{ $thecase->id }}/sync">
					@csrf
				@if($thecase->people->count() >0)
					@foreach($thecase->people as $theperson)
					<div>
						<label for="linked_{{ $theperson->id }}"><input type="checkbox" value="{{ $theperson->id }}" checked name="linked[]" id="linked_{{ $theperson->id }}" />
						<span class="ml-2">{{ $theperson->full_name }}</span></label>
					</div>
					@endforeach
				@endif

				<div id="existing_checkboxes_interior">
				</div>

					<div class="border-t-4 border-blue p-2 bg-grey-lighter">
						<button class="bg-blue text-white rounded-lg text-sm px-2 py-1">Save</button>
					</div>
				</form>
				</div>

				<div id="existing_linked" class="flex flex-wrap">
				@if($thecase->people->count() >0)
					@foreach($thecase->people as $theperson)
						<a href="{{dir}}/constituents/{{ $theperson->id }}">
							<div class="mb-1 border bg-orange-lightest hover:bg-blue hover:text-white rounded-lg mr-2 px-2 py-1 flex-1 flex-initial cursor-pointer">
								<i class="fas fa-user-circle text-lg mr-2"></i>
								{{ $theperson->full_name }}
							</div>
						</a>
					@endforeach
				@endif
					<div id="addothers_added" class="flex flex-wrap">
					</div>

					<div id="addothers"  class="mb-1 border bg-grey-lighter rounded-lg mr-2 px-2 py-1 flex-1 flex-initial cursor-pointer text-sm">
						<i class="fas fa-user-edit mr-1"></i>
						<span id="addothers_buttontext">Add / Remove</span>
					</div>
				</div>

				<div id="list" class="hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="width:600px;"></div>	

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-32">
				Households:
			</td>
			<td class="p-2" >



				<div id="addothers_form_hh" class="hidden text-sm w-1/2 mt-2">
					<input id="cfbar_hh" type="text" placeholder="Look up Households" style="font-family:Arial, Font Awesome\ 5 Free" data-toggle="dropdown" autocomplete="off" class="w-full text-black appearance-none rounded-full px-2 py-2 bg-grey-lightest border-2 border-grey focus:bg-white hover:shadow-outline shadow" />
				</div>

				<div id="existing_checkboxes_hh" class="p-2 hidden">
					<form method="post" action="{{dir}}/cases/{{ $thecase->id }}/sync_hh">
					@csrf
						@if($thecase->households->count() >0)
							@foreach($thecase->households->unique() as $thehousehold)
							<div>
								<label for="linked_hh_{{ $thehousehold->id }}"><input type="checkbox" value="{{ $thehousehold->id }}" checked name="linked_hh[]" id="linked_hh_{{ $thehousehold->id }}" />
								<span class="ml-2">{{ $thehousehold->full_address }}</span></label>
							</div>
							@endforeach
						@endif

					<div id="existing_checkboxes_interior_hh">
					</div>

						<div class="border-t-4 border-blue p-2 bg-grey-lighter">
							<button class="bg-blue text-white rounded-lg text-sm px-2 py-1">Save</button>
						</div>
					</form>
				</div>



				<div id="existing_linked_hh" class="flex flex-wrap">

					@if($thecase->households->count() >0)

						@foreach($thecase->households->unique() as $the_hh)
						<div>
							<a href="{{dir}}/households/{{ $the_hh->id }}">
								<div class="inline-block mb-1 border bg-orange-lightest hover:bg-blue hover:text-white rounded-lg mr-2 px-2 py-1 flex-initial cursor-pointer">
									<i class="fas fa-home text-lg mr-2"></i>
									{{ $the_hh->full_address }}
								</div>
							</a>
						</div>
						@endforeach

					@endif

					<div id="addothers_added_hh" class="flex flex-wrap">
					</div>

					<div id="addothers_hh"  class="inline-block mb-1 border bg-grey-lighter rounded-lg mr-2 px-2 py-1 flex-1 flex-initial cursor-pointer text-sm">
						<i class="fas fa-home mr-1"></i>
						<span id="addothers_buttontext_hh">Add / Remove</span>
					</div>
	
				</div>


				<div id="list_hh" class="hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="width:600px;"></div>

			</td>
		</tr>

	</table>




	<table class="text-base w-full">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-32">
				Description:
			</td>
			<td class="p-2 align-top">
				{{ $thecase->notes }}
			</td>

			<td class="p-2 bg-grey-lighter text-right align-top w-32">
				Assigned to
			</td>
			<td class="p-2 align-top">
				<i class="fas fa-user mr-1 text-grey-darkest"></i>
				{{ $thecase->assignedTo()->name }}
					
					<a class="hover:font-bold px-2 py-1 rounded-full" data-toggle="collapse" href="#assign_person" role="button" aria-expanded="false" aria-controls="assign_person">
						<button class="float-right px-4 py-1 text-base">
						Change
						</button>
					</a>

				<div id="assign_person" class="collapse">
				@foreach($team_users as $theuser)
					<a href="{{dir}}/cases/{{ $thecase->id }}/assign_user/{{ $theuser->id }}">
					<span class="w-16 px-3 py-2 text-base mr-2">
						<i class="fas fa-user mx-2"></i>
					</span>	
						{{ $theuser->name }}<br />
					</a>
				@endforeach
				</div>
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-32">
				Opened:
			</td>
			<td class="p-2">
				{{ \Carbon\Carbon::parse($thecase->date)->format("F j, Y") }}
				<span class="text-grey-dark text-sm ml-2">
				({{ \Carbon\Carbon::parse($thecase->date)->diffForHumans() }})
				</span>
			</td>

			<td class="p-2 bg-grey-lighter text-right w-32">
				Status:
			</td>
			<td class="p-2 align-middle w-1/3">
			@if($thecase->resolved)
				Closed
				<a href="{{dir}}/cases/{{ $thecase->id }}/resolved/0">
				<button class="float-right px-4 py-1 text-base">
					Re-Open
				</button>
				</a>
			@else
				Open
				<a href="{{dir}}/cases/{{ $thecase->id }}/resolved/1">
				<button class="float-right px-4 py-1 text-base">
					Mark as Resolved
				</button>
				</a>
			@endif
			</td>

		</tr>
	</table>



	<div class="flex mt-2">

		<div class="w-2/3 mr-2">
			

			<div class="text-xl font-sans mt-4">
				@if($contacts->count() >0 )
					Case Notes <span class="text-lg text-grey-dark">({{$contacts->count()}})</span>
				@else
					Add a Case Note
				@endif

			</div>

			<div class="mt-2 border-t-4 border-blue">



<form method="POST" id="contact_form" action="{{dir}}/cases/{{ $thecase->id }}/add_contact">
	@csrf	
<table class="text-base w-full border-t">
		<tr class="border-b">
			<td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
				Date
			</td>
			<td class="p-2 flex">
				<input name="date" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>

				<a class="px-2 py-1 rounded-full mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
				<div id="show_time_div" class="hidden">
					<input name="time" value="{{ \Carbon\Carbon::now()->format("h:i A") }}" class="border-2 rounded-lg px-4 py-2 w-32"/>
					<input type="hidden" value="0" name="use_time" id="use_time" />
				</div>
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
				Desc
			</td>
			<td class="p-2">
				<textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
			</td>
		</tr>
		<tr class="">
			<td class="p-2 align-middle" colspan="2">


				<div id="set_follow_up_div" class="hidden my-2 bg-orange-lightest p-2 border rounded-lg">

					<input type="checkbox" 
						   autocomplete="off"
						   name="person_followup"
						   id="person_followup" />

					<i class="fas fa-hand-point-right text-red ml-2"></i>

					<span class="text-sm">Follow Up on this Date</span>

					<input type="text" 
						   name="person_followup_on" 
						   placeholder="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" 
						   class="border px-2 py-1 rounded-lg" />

				</div>


				<div class="-mt-2 pt-3 pb-2 mb-1 text-sm border-b">

					<input type="submit" class=" -mt-1 ml-4 float-right shadow cursor-pointer hover:bg-blue-dark rounded-full py-1 px-3 text-sm bg-blue text-white" value="Add Note" />

					<a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>

				</div>


			</td>
		</tr>
	</table>
</form>

			
</div>

	@if($contacts->count() > 0)

		<div id="contacts">
			<?php $tabs_off = 1; ?>
			<div id="contacts-content">
			@foreach($contacts as $thecontact)
				<div>
					@include('office.cases.one-contact')
				</div>
			@endforeach
			</div>

			<!-- Modal -->
			<div id="contact-edit-modal" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->

				</div>
			</div>
			<!-- End Modal -->
		</div>

	@endif


			<br />
			<br />
		</div>

		<div class="w-1/3 ml-8">



			<div class="text-xl font-sans mt-5 pb-1 border-b-4 border-blue">
				@if($thecase->files->count() > 0 )
					Files <span class="text-lg text-grey-dark">({{ $thecase->files->count() }})</span>
				@else
					Add Files
				@endif
			</div>

				@if($thecase->files->count() >0 )
				<div class="mt-1 text-base w-full">
					@foreach($thecase->files as $thefile)
						<div class="flex-1 flex-initial mb-2 cursor-pointer border-grey-lighter hover:bg-orange-lightest hover:shadow rounded-lg px-2 py-1">
							<a href="{{dir}}/download_file/{{ $thefile->id }}" class="text-grey-darkest" target="_new">
							<i class="w-6 text-center far fa-file"></i>
						 	{{ $thefile->name }}
						 	</a>
						 	<div class="float-right">
						 		<a href="{{dir}}/delete_file/{{ $thefile->id }}" class="hover:bg-red hover:text-white text-red-dark text-sm rounded-full px-2 py-1">Remove</a>
						 	</div>
						 </div>
					@endforeach
				</div>
				@endif

			<div class="text-center">
				<a class="px-2 py-1 rounded-full" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
				<button class="bg-blue hover:bg-blue-dark rounded-full text-white mt-2 px-4 py-1">
					Add Files
				</button>
				</a>

				    <div class="collapse mt-4 p-4 rounded-lg bg-grey-lighter" id="collapseExample">
						<form disabled id="file_upload_form" action="{{dir}}/upload_file" method="post" enctype="multipart/form-data">
							
							@csrf

						    <div class="m-2">
						    	

						    	<input type="file" name="fileToUpload" id="fileToUpload" class="opacity-0 z-99 float-right absolute">

						    	<label for="fileToUpload">
						    		<button type="button" class="bg-orange-dark rounded-full text-white px-4 py-2">
						    			Choose File
						    		</button>
						    	</label>

						    </div>


						    <input type="hidden" name="case_id" value="{{ $thecase->id }}" />

						    <button name="submit" class="bg-blue rounded-full text-white px-4 py-2">
						    Upload <span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-4"></i>
						    </button>
						</form>
					</div>

			</div>




			<br /><br />
		</div>

	</div>



@endsection

@section('javascript')
<script type="text/javascript">
	
	function uncheckAll() {
  		$('input[type="checkbox"]:checked').prop('checked',false);
	}

	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('{{dir}}/cases_searchpeople/'+{!! $thecase->id !!}+'/'+v, function(response) {
			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}

	function getSearchData_hh(v) {
		if (v == '') {
			$('#list_hh').addClass('hidden');
		}
		// alert('{{dir}}/cases_searchhouseholds/'+{!! $thecase->id !!}+'/'+v);
		$.get('{{dir}}/cases_searchhouseholds/'+{!! $thecase->id !!}+'/'+v, function(response) {
			if (response == '') {
				$('#list_hh').addClass('hidden');
			} else {
				$('#list_hh').html(response);
				$('#list_hh').removeClass('hidden');
			}
		});
	}


	$(document).ready(function() {
		$("#contact_subject").focus();

		$(document).on("click", ".contact_followup", function() {
        	var id = $(this).attr('data-id');
            if ($("[data-id="+id+"]").is(":checked")) {
				$.ajax({
				 	type: "GET",
				 	url: '{{dir}}/followup_done/'+id+'/true', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('bg-orange-lightest');
						$('#followup_'+id).removeClass('text-black');
		                $('#followup_'+id).addClass('text-grey');
		                $('#followup_'+id).addClass('bg-grey-lighter');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            } else {
				$.ajax({
				 	type: "GET",
				 	url: '{{dir}}/followup_done/'+id+'/false', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('text-grey');
		                $('#followup_'+id).removeClass('bg-grey-lighter');
		                $('#followup_'+id).addClass('bg-orange-lightest');
		                $('#followup_'+id).addClass('text-black');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            }

        });

		$(document).on("click", "#show_time", function() {
			if($('#show_time_div').hasClass('hidden')) {
				$('#show_time_div').removeClass('hidden');
				$('#use_time').val(1);
			} else {
				$('#show_time_div').addClass('hidden');
				$('#use_time').val(0);
			}
		});

		$(function() {
		    $("#contact_notes").keypress(function (e) {
		        if(e.which == 13) {
		            e.preventDefault();
		            $('#contact_form').submit();
		        }
		    });
		});
	
	    $("input[type=file]").change(function(e){
            var fileName = e.target.files[0].name;
	    	$("#file_selected").text(fileName); 
	    });

		$( "#file_upload_form" ).submit(function( event ) {
			var fileName = $('#fileToUpload').val();
			if (fileName == '') { event.preventDefault(); }
		});

		$(document).on("click", "#set_follow_up", function() {
			if($('#set_follow_up_div').hasClass('hidden')) {
				$('#set_follow_up_div').removeClass('hidden');
				$('#person_followup').prop('checked', true);
			} else {
				$('#set_follow_up_div').addClass('hidden');
				$('#person_followup').prop('checked', false);
			}
		});

        $(document).on("click", "#person_followup", function() {
            if ($('#person_followup').is(":checked")) {
                $("#set_follow_up_div").removeClass('hidden');
            } else {
                $("#set_follow_up_div").addClass('hidden');
            }
        });

        //////////////////////////////////////////////////////////////////////////////

		$(document).on("click", "#addothers", function() {
			if($('#addothers_form').hasClass('hidden')) {
				$('#existing_linked').html('');
				$('#existing_checkboxes').removeClass('hidden')
				$('#addothers_form').removeClass('hidden');
				$("#cfbar").focus();
			}
		});

		$("#cfbar").focusout(function(){
			window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
		});
		
		$("#cfbar").keyup(function(){
			getSearchData(this.value);
		});

	    $(document).on('click', ".clickable-select-person", function () {
	    	id = $(this).data("theid");
	        
			$.get('{{dir}}/cases/'+{!! $thecase->id !!}+'/linkperson/'+id, function(response) {
				if (response != '') {
					$('#existing_checkboxes_interior').append(response);
				}
			});
	    });

		//////////////////////////////////////////////////////////////////////////////

		$(document).on("click", "#addothers_hh", function() {
			if($('#addothers_form_hh').hasClass('hidden')) {
				$('#existing_linked_hh').html('');
				$('#existing_checkboxes_hh').removeClass('hidden')
				$('#addothers_form_hh').removeClass('hidden');
				$("#cfbar_hh").focus();
			}
		});

		$("#cfbar_hh").focusout(function(){
			window.setTimeout(function() {$('#list_hh').addClass('hidden'); }, 300);
		});
		
		$("#cfbar_hh").keyup(function(){
			getSearchData_hh(this.value);
		});

	    $(document).on('click', ".clickable-select-household", function () {
	    	id = $(this).data("theid");
	        // alert('{{dir}}/cases/'+{!! $thecase->id !!}+'/link_hh/'+id);
			$.get('{{dir}}/cases/'+{!! $thecase->id !!}+'/link_hh/'+id, function(response) {
				if (response != '') {
					$('#existing_checkboxes_interior_hh').append(response);
				}
			});
	    });


	});

</script>
@endsection