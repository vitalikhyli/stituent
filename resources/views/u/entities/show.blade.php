@extends(Auth::user()->team->app_type.'.base')

@section('title')
    	{{ $entity->name }}
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/entities">Organizations</a> 

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
			
				<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit">
					<button type="button" class="rounded-lg bg-blue text-white float-right text-sm mt-1 py-2 px-4 font-normal">
						Edit
					</button>
				</a>


			<span class="mr-2">{{ $entity->name }}</span>

		</div>
	</div>


		



	<div class="flex">

		

		<div class="text-base w-3/5 pt-4 pr-4">

			<div class="border-b-4 border-grey font-bold pb-1">Basic Info</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Description
				</div>
				<div class="p-2 w-4/5">

					@if(!$entity->private)

						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit" class="text-xs">
							Add
						</a>

					@else

						{{ $entity->private }}
						
					@endif
				</div>
			</div>

			<div class="flex">
				<div class="w-1/5 p-2 text-grey-darker text-right whitespace-no-wrap border-r">
					Contacts
				</div>

				<div class="p-2 w-4/5">
					@if(!$entity->contact_info)
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit" class="text-xs">
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
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit" class="text-xs">
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
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit" class="text-xs">
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

					@if($entity->social_facebook || $entity->social_twitter || $entity->social_instagram)
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
						@if($entity->social_instagram)
							<div class="flex">
								<span class="text-grey-dark w-24">Instagram</span> {{ $entity->social_instagram }}
							</div>
						@endif
					@else
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/edit" class="text-xs">
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

		</div>

		<div class="w-2/5 pt-4">
			<div class="border-b-4 border-grey font-bold pb-1">Contacts & Notes</div>

			@include('u.entities.notes-form')


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
	                            <a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/contacts/{{ $note->id }}/edit">
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

	
	@if(Auth::user()->permissions->developer)
	@if($entity->communityBenefits->first())

		<div class="font-bold border-b-4 border-grey text-base mt-8 pb-1">

			Community Benefits ({{ $entity->communityBenefits->count() }})

		</div>

		<div class="">
			
			<div class="flex border-b">

				<div class="flex-grow">
					
				</div>

				<div class="w-32 p-2 border-l text-center text-sm uppercase">

					Beneficiary

				</div>

				<div class="w-32 p-2 border-l text-center text-sm uppercase">

					Initiator

				</div>
				
				<div class="w-32 p-2 border-l text-center text-sm uppercase">

					Partner

				</div>

			</div>

			
			@foreach($entity->communityBenefits as $benefit)

				<div class="flex">

					<div class="p-2 border-b flex-grow">
						<a href="/{{ Auth::user()->team->app_type }}/community-benefits/{{ $benefit->id }}/edit">
							<i class="fas fa-dollar-sign mr-2"></i> {{ $benefit->program_name }}
						</a>
					</div>

					<div class="border-l w-32 text-center border-b">
						@if($benefit->pivot->beneficiary)
							<div class="bg-blue-lightest p-2">
								<i class="fa fa-check-circle text-blue text-xl mt-1"></i>
							</div>
						@endif
					</div>

					<div class="border-l w-32 text-center border-b">
						@if($benefit->pivot->initiator)
							<div class="bg-blue-lightest p-2">
								<i class="fa fa-check-circle text-blue text-xl mt-1"></i>
							</div>
						@endif
					</div>

					<div class="border-l w-32 text-center border-b">
						@if($benefit->pivot->partner)
								<div class="bg-blue-lightest p-2">
								<i class="fa fa-check-circle text-blue text-xl mt-1"></i>
							</div>
						@endif
					</div>

				</div>

			@endforeach

		</div>

	@endif
	@endif



	@if (!$entity->departmentPartnerships()->first())
		<div class="font-bold border-b-4 border-grey text-base mt-8 pb-1">

			<a class="float-right text-sm font-normal" href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/new">
				Add a New Partnership
			</a>

			Partnerships ({{ $entity->partnerships()->count() }})
		</div>
	@endif

	@if ($entity->partnerships()->first())

		
	
		<table class="table text-sm">

		<tr>
			<th>Type</th>
			<th>Program</th>
			<th>Contacts</th>
			<th class="whitespace-no-wrap">NU Dept</th>
			<th></th>
		</tr>

	@foreach ($partnership_years as $year)

		<tr class="">
			<td class="border-b-4 bg-grey-lighter" colspan="5">
				<div class="p-1 font-semibold text-base">
					{{ \Carbon\Carbon::parse($year)->format("Y") }}
				</div>
			</td>
		</tr>


		@foreach ($entity->partnerships->where('year',$year) as $partnership)



			<tr>
				<td class="whitespace-no-wrap">
					@if($partnership->partnershipType)
						{{ $partnership->partnershipType->name }}
					@endif
				</td>
				<td  class="font-bold">{{ $partnership->program }}</td>
				<td>
					@if ($partnership->contacts)
						@foreach ($partnership->contacts as $contact)
							<div class="flex mb-2">

								@if(($contact['name']) || ($contact['email']))
								<div>
									<i class="fas fa-user mr-3 text-blue-dark"></i>
								</div>
								@endif

								<div>
									@if(($contact['name']) && ($contact['email']))
										{{ $contact['name'] }}, <span class="text-blue-dark">{{ $contact['email'] }}</span>
									@elseif($contact['name'])
										{{ $contact['name'] }}
									@elseif($contact['email'])
										<span class="text-blue-dark">{{ $contact['email'] }}</span>
									@endif
								</div>
						</div>
						@endforeach
					@endif
				</td>
				<td>

					@if ($partnership->department)

						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $partnership->department->id }}">

							{!! $partnership->department->name !!}

						</a>
					@endif

				</td>
				<td>
					<a class="float-right text-xs border rounded-lg text-grey px-2 py-1" href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/{{ $partnership->id }}/edit">
						Edit
					</a>
				</td>

			</tr>

		@endforeach
	@endforeach
	</table>

	@endif

	@if ($entity->departmentPartnerships()->count() > 0)

		<div class="font-bold border-b-4 border-blue text-lg mt-8 pb-2">
			{{ $entity->departmentPartnerships()->count() }} Service Learning Partnerships
		</div>
	
		<table class="table text-sm">

		<tr>
			<th>Course</th>
			<th>Faculty</th>
			<th>Filed By</th>
			<th>Partner</th>
			<th></th>
		</tr>

		@foreach ($entity->departmentPartnerships as $slp)

			<tr>
				<td>{{ $slp->program }}</td>
				<td>{{ $slp->data['faculty'] }}</td>
				<td>{{ $slp->data['filer'] }}</td>
				<td>
					<b>
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $slp->partner->id }}">
							{{ $slp->partner->name }}
						</a>
					</b>
					@if ($slp->contacts)
						@foreach ($slp->contacts as $contact)
							{{ $contact['name'] }}, {{ $contact['email'] }}<br>
						@endforeach
					@endif
				</td>
				<td>
					<a class="float-right text-xs border rounded-lg text-grey px-2 py-1" href="/u/entities/{{ $slp->partner->id }}/partnerships/{{ $slp->id }}/edit">
						Edit
					</a>
				</td>

			</tr>

		@endforeach
	</table>

	@endif


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