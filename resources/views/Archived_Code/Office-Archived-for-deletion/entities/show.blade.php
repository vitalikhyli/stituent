@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    	{{ $entity->name }}
@endsection

@section('breadcrumb')

	<!-- {!! Auth::user()->Breadcrumb($entity->full_name, 'show_person') !!} -->

	<a href="{{dir}}">Home</a> > 
	<a href="{{dir}}/entities">Organizations</a> 

    > &nbsp;<b>{{ $entity->name }}</b>

@endsection

@section('style')

	<style>
		.truncate {
		  width: 100px;
		  white-space: nowrap;
		  overflow: hidden;
		  text-overflow: ellipsis;
		}

	</style>

@endsection

@section('main')

@include('elements.errors')



	<div class="text-2xl font-sans text-blue-darker">

		<!-- <i class="fas fa-street-view mr-2 text-blue"></i> -->
		<i class="fas fa-building ml-1 mr-2"></i>
		<span class="mr-2">{{ $entity->name }}</span>

		<a href="{{dir}}/entities/{{ $entity->id }}/edit">
		<button type="button" class="rounded-lg bg-blue-darker hover:bg-blue-darkest text-white float-right text-sm px-8 py-2 mt-1 shadow">
			EDIT
		</button>
		</a>
	</div>


		

	<table class="text-base mt-4 w-full border-t-4 border-blue">

		<tr class="border-t border-b">
			<td class="p-2 bg-grey-lighter w-32">
				Address
			</td>
			<td class="p-2">
				{{ $entity->full_address }}
			</td>
		</tr>

		<tr class="border-t border-b">
			<td class="p-2 bg-grey-lighter w-32">
				Notes
			</td>
			<td class="p-2">
				@if(!$entity->private)
					<span class="text-grey-dark">(None)</span>
				@else
					{{ $entity->private }}
				@endif
			</td>
		</tr>

	</table>



		



	<div class="mt-6 flex flex-row-reverse text-sm"> 

		@if(isset($cases))
			@if($cases->where('resolved',1)->count() >0 )
			    <div class="entity-tab {{ ($tab == 'cases') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#entity_cases" id="tab_cases">
			       Resolved
			       ({{ $cases->where('resolved',1)->count() }})
			    </div>
			@endif
		@endif

		@if(false)
	    <div data-toggle="tooltip" data-placement="top" title="Issues and Other Labels" class="entity-tab {{ ($tab == 'groups') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }}  h-10 uppercase  pt-3 px-4  mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#entity_groups" id="tab_groups">
	       Groups
	        @if(isset($groups) && ($groups->count() >0))
	       		({{ $groups->count() }})
	        @endif
	    </div>
	    @endif

	    <div data-toggle="tooltip" data-placement="top" title="Connections to other people and entities" class="entity-tab {{ ($tab == 'relationships') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#entity_relationships" id="tab_relationships">
	       Relationships
	    </div>

		
	    <div data-toggle="tooltip" data-placement="top" title="Emails, Phone Numbers, etc." class="entity-tab {{ ($tab == 'contactinfo') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} h-10 uppercase pt-3 px-4 mr-2 rounded-t-lg cursor-pointer relative border-t border-l border-r " data-target="#entity_contactinfo" id="tab_contactinfo">
	      Contact
	    </div>
	    

		@if(false)
	   	<div data-toggle="tooltip" data-placement="top" title="Cases, Notes and Other Info" class="entity-tab {{ ($tab == 'contacts') ? 'active bg-blue text-white' : 'bg-grey-lighter text-blue-darker hover:text-black hover:bg-grey-lighter' }} relative rounded-t-lg h-10 uppercase pt-3 px-4 mr-2 border-t border-l border-r cursor-pointer" data-target="#entity_contacts">
	        Notes
	        @if(isset($contacts) && ($contacts->count() >0))
	       		({{ $contacts->count() }})
	        @endif

	        @if(isset($cases) && ($cases->where('resolved',0)->count() >0))
	        	&amp; Cases
	       		({{ $cases->where('resolved',0)->count() }})
	        @endif
	    </div> 
	    @endif   

	</div>

	


    <div class="entity-tabs w-full" style="min-height: 700px;">

        @include('office.entities.tabs.relationships')
        @include('office.entities.tabs.contactinfo')

	</div>


<br />
<br />
<br />
@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

		$('.switchform').click(function() {
	      var id = $(this).attr('id');
	      var m = 'GroupPerson';
	      var c = 'data';
	      var j = $(this).attr('data-json');
		  $.get('{{dir}}/get_form_json/'+m+'/'+c+'/'+j+'/'+id, function(response) {
				$('#'+id).replaceWith(response);
		  });
		});

		$('.btn_newgroup').click(function() {
	      var c = $(this).data('category');
	      var f = $(this).data('form');
		  $.get('{{dir}}/get_form_groups/'+f+'/'+'{!! $entity->id !!}'+'/'+c, function(response) {
				$('#'+f).replaceWith(response);
		  }); 
		});
	
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

		$(document).on("click", "#set_follow_up", function() {
			if($('#set_follow_up_div').hasClass('hidden')) {
				$('#set_follow_up_div').removeClass('hidden');
				$('#entity_followup').prop('checked', true);
			} else {
				$('#set_follow_up_div').addClass('hidden');
				$('#entity_followup').prop('checked', false);
			}
		});

        $(document).on("click", "#entity_followup", function() {
            if ($('#entity_followup').is(":checked")) {
                $("#set_follow_up_div").removeClass('hidden');
            } else {
                $("#set_follow_up_div").addClass('hidden');
            }
        });

		$("#contact_subject").focus();

		$(function() {
		    $("#contact_notes").keypress(function (e) {
		        if(e.which == 13) {
		            e.preventDefault();
		            $('#contact_form').submit();
		        }
		    });
		});


	});

</script>
@endsection