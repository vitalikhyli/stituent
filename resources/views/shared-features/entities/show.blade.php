@extends(Auth::user()->team->app_type.'.base')

@section('title')
    	{{ $entity->name }}
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/organizations">Organizations</a> 

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

	<div class="border-b-4 border-blue w-full pb-2">

	<div class="text-2xl font-sans text-blue-darker">

		<!-- <i class="fas fa-street-view mr-2 text-blue"></i> -->

			@switch($entity->type )
			    @case('Course')
			        <i class="fas fa-chalkboard-teacher ml-1 mr-2"></i>
			        @break

			    @case('NU Department')
			        <i class="fas fa-school ml-1 mr-2"></i>
			        @break

			    @default
			    	<i class="fas fa-building ml-1 mr-2"></i>
			@endswitch
			
				<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit">
					<button type="button" class="rounded-lg bg-blue text-white float-right text-sm mt-1 py-2 px-4 font-normal">
						Edit
					</button>
				</a>


			<span class="mr-2">{{ $entity->name }}</span>

		</div>
	</div>


		



	<div class="flex mt-8">

		

		<div class="text-base w-3/5 pt-4 pr-4">

			<div class="border-b-4 uppercase border-grey font-black pb-1 text-2xl">Basic Info</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Description
				</div>
				<div class="p-2 w-4/5">

					@if(!$entity->private)

						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit" class="text-xs text-grey">
							Add
						</a>

					@else

						{{ $entity->private }}
						
					@endif
				</div>
			</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Notes
				</div>

				<div class="p-2 w-4/5">
					@if(!$entity->contact_info)
						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit" class="text-xs text-grey">
							Add
						</a>
					@else
						@foreach ($entity->contact_info as $contact)
							<div class="mb-2">
							@if(isset($contact['name']))
								<div class="font-bold">{{ $contact['name'] }}</div>
							@endif
							@if(isset($contact['email']))
								<div class="text-sm text-grey-darker">
									&lt;{{ $contact['email'] }}&gt;
								</div>
							@endif
							@if(isset($contact['phone']))
								<div class="text-sm text-grey-darker">
									{{ $contact['phone'] }}
								</div>
							@endif
							</div>
						@endforeach
					@endif
				</div>
			</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Type
				</div>
				<div class="p-2 w-4/5">
					@if(!$entity->type)
						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit" class="text-xs text-grey">
							Add
						</a>
					@else
						{{ $entity->type }}
					@endif
				</div>
			</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Address
				</div>
				<div class="p-2 w-4/5">
					@if(!$entity->full_address)
						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit" class="text-xs text-grey">
							Add
						</a>
					@else
						{{ $entity->full_address }}
					@endif
				</div>
			</div>


			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Social Media
				</div>
				<div class="p-2 w-4/5">

					@if($entity->social_facebook || $entity->social_twitter)
						@if($entity->social_facebook)
							<div class="flex">
								<span class="text-grey-dark w-24">Facebook</span> {{ $entity->social_facebook }} 
							</div>
						@endif
						@if($entity->social_twitter)
							<div class="flex">
								<span class="text-grey-dark w-24">Twitter</span> {{ $entity->social_twitter }}
							</div>
						@endif
					@else
						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit" class="text-xs text-grey">
							Add
						</a>
					@endif


				</div>
			</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					People
				</div>
				<div class="p-2 w-4/5 text-sm">
		
					@foreach($entity->people->sortBy('last_name') as $person)
	                    <div id="relationship_{{ $person->id }}" class="flex w-full border-b py-1">

	                        <div class="flex-1 w-1/2">
	                            <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}"><i class="fas fa-user-circle mr-1"></i> {{ $person->full_name }}</a>
	                        </div>
	                        
	                        <div class="flex-1 w-1/2">
	                            
	                            <span class="flex-initial text-grey-darkest ml-2 capitalize">{{ $person->pivot->relationship }}</span>
	                        </div>
	                        

	                        <div class="flex-shrink mx-2">
                            
		            			<button type="button" data-person_id="{{ $person->id }}" data-type="{{ $person->pivot->relationship }}" data-full_name="{{ $person->full_name }}" class="edit_relationship_modal_button rounded-lg bg-grey-lighter text-grey-darker px-2 py-1 text-xs">
									Edit
								</button>

	                        </div>

	                    </div>
	            	@endforeach

	            	<div class="mt-2">

            			<button type="button" class="add_relationship_modal_button rounded-lg bg-blue text-white px-2 py-1 text-xs">
							Add Person
						</button>

	            	</div>
	            	
				</div>
			</div>

			<div class="w-full pt-4">
				<div class="border-b-4 uppercase border-grey font-black pb-1 text-2xl">
					Cases
				</div>

				@livewire('case-org', ['org_id' => $entity->id])
				
			</div>

		</div>

		<div class="w-2/5 pt-4">
			<div class="uppercase border-grey font-black pb-1 text-2xl">Contacts & Notes</div>

			<div class="p-4 rounded border-4">
				@include('shared-features.entities.notes-form')
			</div>

			@if($entity->notes()->where('private', false)->orWhere('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->count() <= 0)
				<div class="p-2">None.</div>
			@else
			<table class="table text-sm">
				@foreach ($entity->notes()->orderBy('created_at', 'desc')->get() as $note)

				@if((!$note->private) || ($note->user_id == Auth::user()->id))
					<tr>
						<td class="text-grey whitespace-no-wrap">
							
							@if ($note->created_at > \Carbon\Carbon::now()->subMonth())
								{{ $note->created_at->diffForHumans() }}
							@else
								{{ $note->created_at->format('n/j/Y') }}
							@endif
							<br>
							<i>{{ $note->user->first_name }}</i>
						</td>
						<td>
							<b>{{ $note->subject }}</b><br>
							{{ $note->notes }}

	                        <div class="float-right w-12 ml-2">
	                            <a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/contacts/{{ $note->id }}/edit">
	                            <button class="text-grey-darker text-xs rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1">
	                                Edit
	                            </button>
	                            </a>
	                        </div>

						</td>
					</tr>
					@endif
				@endforeach
			</table>
			@endif
		</div>
	</div>



	
	





@include('shared-features.constituents.relationships-modal-add')
@include('shared-features.constituents.relationships-modal-edit')

<br />
<br />

@endsection



@section('javascript')

	@include('shared-features.constituents.relationships-modal-javascript')
	
	<script type="text/javascript">

	$(document).ready(function() {

		////////////////////////////////////////////////////////////////// CONTACT FORM
	
		$(document).on("click", ".contact_followup", function() {
        	var id = $(this).attr('data-id');
            if ($("[data-id="+id+"]").is(":checked")) {
				$.ajax({
				 	type: "GET",
				 	url: '/{{ Auth::user()->team->app_type }}/followup_done/'+id+'/true', 
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
				 	url: '/{{ Auth::user()->team->app_type }}/followup_done/'+id+'/false', 
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