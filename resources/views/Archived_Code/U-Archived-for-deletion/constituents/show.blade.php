@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    	{{ $person->full_name }}
@endsection

@section('breadcrumb')

	<!-- {!! Auth::user()->Breadcrumb($person->full_name, 'show_person') !!} -->

	<a href="{{dir}}">Home</a> > 
	<a href="{{dir}}/constituents">Constituents</a> 

    > &nbsp;<b>{{ $person->full_name }}</b>

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



	<div class="text-2xl font-sans text-blue-darker border-b-4 border-blue pb-2">

		<i class="fas fa-user-circle ml-1 mr-2"></i>

		<span class="mr-2">{{ $person->full_name }}</span>

		@if($mode_external)
			<span class="rounded-full bg-white border cursor-pointer px-2 py-1 text-grey-darkest text-xs">
				<i class="fas fa-unlink mr-1"></i>
				not yet imported
			</span>
		@endif

		<a href="{{dir}}/constituents/{{ $person->id }}/edit">
			<button type="button" class="rounded-lg bg-blue-darker hover:bg-blue-darkest text-white float-right text-sm px-4 py-2 mt-1">
				Edit
			</button>
		</a>

	</div>


	<div class="flex mb-2">

		<div class="mr-4 flex-1 w-1/2">

			@include('university.constituents.tabs.basic')

		</div>


		<div class="ml-4 flex-1 w-1/2">

			@include('university.constituents.tabs.contactinfo')

		</div>

	</div>
		

	<div class="flex">

		<div class="mr-4 flex-1 w-1/2">

			@include('university.constituents.tabs.notes')

		</div>

		<div class="ml-4 flex-1 w-1/2">

			<div>

				@include('university.constituents.tabs.cases')

			</div>

			<div class="p-4 bg-grey-lightest border rounded-lg">

				@include('university.constituents.tabs.groups')

			</div>

			<div class="">

				@include('university.constituents.tabs.relationships')

			</div>

			<div>

				@include('university.constituents.tabs.otherinfo')

			</div>

		</div>

	</div>


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
		  $.get('{{dir}}/get_form_groups/'+f+'/'+'{!! $person->id !!}'+'/'+c, function(response) {
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