@extends(Auth::user()->team->app_type.'.base')

@section('title')
    	{{ $person->full_name }}
@endsection

@section('breadcrumb')
	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">@lang('Constituents')</a> 
    > &nbsp;<b>{{ $person->full_name }}</b>
@endsection

@push('styles')
	<style>
		.truncate {
		  width: 100px;
		  white-space: nowrap;
		  overflow: hidden;
		  text-overflow: ellipsis;
		}
	</style>



@endpush

@section('main')

@include('elements.errors')

	<div class="flex items-center mb-8">
		<div class="w-32">
			<img title="Avatar (Uploaded or publicly linked to email)" class="w-24 rounded-full h-24 border-2 border-gray-300" src="{{ $person->avatar }}" />
		</div>

		<div class="w-full">
			<div class="text-2xl font-sans 
							@if ($person->voter)
								@if ($person->voter->archived_at)
									text-red
								@endif
							@endif
							font-bold border-b-4 pb-2">

				
				<span class="mr-2"><span class="text-grey-dark">{{ $person->name_title }}</span> {{ $person->full_name }}</span>

				@if ($person->language)
					<span class="text-base">
						<span class="text-gray-400">Preferred Language:</span> {{ $person->language }}
					</span>
				@endif

				@if ($person->voter)
					@if ($person->voter->archived_at)
						<small>Archived {{ $person->voter->archived_at->format('n/j/Y') }}*</small>
					@endif
				@endif

				@if($mode_external)
					<span class="rounded-full bg-white border cursor-pointer px-2 py-1 text-grey-darkest text-xs">
						<i class="fas fa-unlink mr-1"></i>
						not yet imported
					</span>
				@endif


				@if (is_numeric($person->id))
				<a href="/{{ Auth::user()->team->app_type }}/merge-constituents?one={{ $person->id }}">
					<button type="button" class="rounded-full hover:text-black text-gray-dark  cursor-pointer float-right text-sm px-4 py-2 mt-1">
						Merge
					</button>
				</a>
				@endif


				<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/edit">
					<button type="button" class="rounded-full hover:text-black text-gray-dark  cursor-pointer float-right text-sm px-4 py-2 mt-1">
						Edit
					</button>
				</a>

			</div>
		

			@if($person->deceased)
				<div class="p-4 border-b bg-red-lightest border-b-4 font-bold">
					{{ $person->full_name }} is <span class="text-red">deceased</span>
					@if($person->deceased_date)
						({{ \Carbon\Carbon::parse($person->deceased_date)->diffForHumans() }})
					@endif
				</div>
			@endif

			<div class="p-2 text-grey-dark text-sm mb-6 w-full">


				<div class="float-right">
					@if (!$person->voter_id)

						@if ($person->creator)
							Added by <b>{{ $person->creator->name }}</b> - {{ $person->created_at->format('n/j/Y g:ia') }}
						@else
							Added by staff on {{ $person->created_at->format('n/j/Y g:ia') }}
						@endif

					@endif

					@if ($person->voter)
						@if ($person->voter->archived_at)
							<span class="text-red text-lg">*</span>
							This person was <b>not in the most recent voter file</b>. They may be deceased, have lapsed registration, or moved out of state. 
						@endif
					@endif
				</div>

				<span class="text-blue-light">
					@include('shared-features.constituents.activity-icons')
				</span>

			</div>
		</div>

	</div>
		

	<div class="flex flex-wrap">

		<div class="w-1/2 pr-4">
			<div class="font-bold text-sm text-grey-dark pl-2 mb-2 uppercase">
				General Info
			</div>
			@include('shared-features.constituents.tabs.basic')

			<div class="text-sm pl-2 my-2">
				<div class="float-right -mt-1">
					@if (Auth::user()->admin)
					<x-cf-plus-modal :voter="$person"></x-cf-plus-modal>
					@endif
				</div>
				<span class="font-bold text-orange-dark">CF+</span>
			</div>
			@php
				$cfplus = null;
				$cfplus = $person->cfPlus();
			@endphp

			@if (Auth::user()->admin)
				<table class="text-left w-full">

			       <tr>
				      <td class="border-t py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">Cell Phone</td>
				      <td class="border-t p-1 text-sm text-gray-900">
				      	@if ($cfplus)
				      		{{ $cfplus->cell_phone }}
				      	@else
				      		<span class="text-grey text-gray-300">Add CF+ Data</span>
				      	@endif
				      </td>
				   </tr>
				   <tr>
				      <td class="border-t py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">Ethnicity</td>
				      <td class="border-t p-1 text-sm text-gray-900">
				      	@if ($cfplus)
				      		{{ $cfplus->ethnic_description }}
				      	@else
				      		<span class="text-grey text-gray-300">Add CF+ Data</span>
				      	@endif
						</td>
				   </tr>
				   <tr>
				      <td class="border-t py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">Est. Income</td>
				      <td class="border-t p-1 text-sm text-gray-900">
				      	@if ($cfplus)
				      		{{ $cfplus->estimated_income }}
				      	@else
				      		<span class="text-grey text-gray-300">Add CF+ Data</span>
				      	@endif
				      	</td>
				   </tr>

			  </table>
		  @else
		  	<div class="italic">
		  		User must be Account Admin
		  	</div>
		  @endif
			
		</div>

		<div class="w-1/2">
			<div class="font-bold text-sm text-grey-dark pl-2 mb-2 uppercase">
				Contact Info
			</div>
			<div>
				@include('shared-features.constituents.tabs.contactinfo')
			</div>
		</div>

	</div>

	<div class="px-2">
			<div class="flex">
				<div class="w-1/2 pr-8">
					@include('shared-features.constituents.tabs.cases')
				</div>

				<div class="w-1/2">

					@include('shared-features.constituents.tabs.groups')
				</div>
			</div>

			@include('shared-features.constituents.tabs.notes')

			@include('shared-features.constituents.tabs.bulk-emails')

			<div>
				@include('shared-features.constituents.tabs.voterinfo')
			</div>

			

			@if(Auth::user()->team->app_type == 'u' ||
				Auth::user()->team->app_type == 'business')
				<div>
					@include('shared-features.constituents.tabs.entities')
				</div>
			@endif
	</div>


@endsection

@push('scripts')
<script type="text/javascript">


	$(document).ready(function() {

		$('.switchform').click(function() {
	      var id = $(this).attr('id');
	      var m = 'GroupPerson';
	      var c = 'data';
	      var j = $(this).attr('data-json');
		  $.get('{{ Auth::user()->team->app_type }}/get_form_json/'+m+'/'+c+'/'+j+'/'+id, function(response) {
				$('#'+id).replaceWith(response);
		  });
		});

		$('.btn_newgroup').click(function() {
	      var c = $(this).data('category');
	      var f = $(this).data('form');
		  $.get('{{ Auth::user()->team->app_type }}/get_form_groups/'+f+'/'+'{!! $person->id !!}'+'/'+c, function(response) {
				$('#'+f).replaceWith(response);
		  }); 
		});
	
		$(document).on("click", ".contact_followup", function() {
        	var id = $(this).attr('data-id');
            if ($("[data-id="+id+"]").is(":checked")) {
				$.ajax({
				 	type: "GET",
				 	url: '/{{ Auth::user()->team->app_type }}/followups/done/'+id+'/true', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('bg-orange-lightest');
						$('#followup_'+id).removeClass('text-black');
		                $('#followup_'+id).addClass('text-grey');
		                $('#followup_'+id).addClass('bg-grey-lighter');
		                $('#followup_'+id+"_pending").addClass('hidden');
		                $('#followup_'+id+"_done").removeClass('hidden');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            } else {
				$.ajax({
				 	type: "GET",
				 	url: '/{{ Auth::user()->team->app_type }}/followups/done/'+id+'/false', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('text-grey');
		                $('#followup_'+id).removeClass('bg-grey-lighter');
		                $('#followup_'+id).addClass('bg-orange-lightest');
		                $('#followup_'+id).addClass('text-black');
		                $('#followup_'+id+"_pending").removeClass('hidden');
		                $('#followup_'+id+"_done").addClass('hidden');
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


        $(document).on("click", "#upload-form-toggle", function() {
            $('#upload-form').toggleClass('hidden');
        });

        


        $(document).on("change", "#fileToUpload", function(e) {
        	var filename = e.target.files[0].name;
            $('#file_name_display').html(filename);
        });

        $(document).on("click", "#show-cohabitors", function(e) {
            $('#cohabitors').toggleClass('hidden');
        });

	});

</script>
@endpush