@extends('campaign.base')

@section('title')
    Edit 
    @if ($participant)
    	Participant
    @else
    	Voter
    @endif
@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

<div class="w-full">

	<div class="border-b-4 border-blue text-xl py-1">

		

		@if($voter)
			@if ($voter->archived_at)
			<div class="float-right text-red" style="cursor:help;" title="Voter is Archived. May be moved or deceased, but was NOT in most recent active voter file.">
				
					<i class="fa fa-archive"></i> ARCHIVED (Not in most recent voter file)
				
			</div>
			@endif
		@endif

	@if($participant)
		<div class="float-right text-sm">
		      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg p-2 text-red text-center ml-2"/>
		        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Participant
		      </button>
		</div>
	@endif

		<span class="text-2xl font-bold">
			Edit 
		    @if ($participant)
		    	Participant
		    @else
		    	Voter
		    @endif
		</span>

	</div>



<form action="/{{ Auth::user()->team->app_type }}/participants/{{ $the_id }}/update" method="post">

	@csrf

	@if ($participant)

		<div class="text-sm flex {{ ($participant->deceased || $participant->go_away) ? 'px-2 bg-red-lightest' : '' }}">
			<div class="py-1 pr-4">
				Ignore because:
			</div>
			<div class="py-1 pr-4">
				<label for="deceased" class="font-normal whitespace-no-wrap">
					<input type="checkbox" value="1" id="deceased" class="" name="deceased" {{ ($participant->deceased) ? 'checked' : '' }}/> Deceased
				</label>
			</div>
			<div class="py-1 pr-4">
				<label for="go_away" class="font-normal whitespace-no-wrap">
					<input type="checkbox" value="1" id="go_away" class="" name="go_away" {{ ($participant->go_away) ? 'checked' : '' }}/> Ignore (Moved away, asked not to be contacted, etc.)
				</label>
			</div>
		</div>

		@endif


<div class="flex">

	<!------------------------------------/ COLUMN 1 /---------------------------------->

	<div class="w-1/2 mr-4 mt-2">

		@if($participant)
			@include('campaign.participants.tabs.edit-basics', ['model' => $participant])
		@endif

		

		@if($participant)
			@include('campaign.participants.tabs.edit-phone-email', ['model' => $participant])
		@endif

		@if($voter)
			@include('campaign.participants.tabs.edit-phone-email', ['model' => $voter])
		@endif

		<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mt-4">
			Tags
			@if($tag_options->first())
				({{ $tag_options->count() }})
			@endif
		</div>
		
		<div class="py-1 inline-flex flex-wrap">

			@foreach($tag_options as $tag)
			
				<div class="uppercase text-sm text-blue pr-4 pt-2">
					
					<label for="tag_{{ $tag->id }}" class="font-normal whitespace-no-wrap">
						
						<input type="checkbox" value="1" id="tag_{{ $tag->id }}" class="" name="tag_{{ $tag->id }}" {{ ($tag->hasParticipant($participant)) ? 'checked' : '' }}/>
					
						{{ $tag->name }}

					</label>

				</div>

			@endforeach

		</div>

	@if($participant)
		<!-- livewire('campaign.participants.volunteer-form', ['model' => $participant]) -->
	@endif

	@if($participant)
		@include('campaign.participants.tabs.edit-support', ['model' => $participant, 'campaigns' => $campaigns])
	@endif

	@if($voter)
		@include('campaign.participants.tabs.edit-support-blank', ['campaigns' => $campaigns])
	@endif


	@if($participant)
		@livewire('contribution', ['participant' => $participant])
	@endif


	@if($participant)
		@include('campaign.participants.tabs.invitations')
	@endif

		
		

	<!------------------------------------/ END COLUMN 1 /---------------------------------->
	</div>
	<div class="w-1/2 ml-2">
	<!------------------------------------/ COLUMN 2 /---------------------------------->

	
	

		@if($voter)
			
			<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mb-2 mt-4">
				Lists {{ $voter->first_name }} Belongs To<!--  (BETA) -->
			</div>

				@livewire('lists-voter-belongs-to', ['voter' => $voter])


		@endif
		
		@if($participant && $participant->voter)
			
			<div class="border-b-2 border-grey-light text-grey-darkest text-base font-medium pb-1 mb-2 mt-4">
				Lists {{ $participant->first_name }} Belongs To<!--  (BETA) -->
			</div>

				@livewire('lists-voter-belongs-to', ['voter' => $participant->voter])


		@endif

	

	@if($participant)

		@livewire('actions', ['participant' => $participant])

		@if(!$participant->voter_id)

			<div class="p-2">
				Participant is not linked to voter file
			</div>

		@elseif($participant->voter)

			@include('campaign.participants.tabs.voter-file', ['model' => $participant->voter])

			@include('campaign.participants.tabs.profile', ['model' => $participant->voter])

			@include('campaign.participants.tabs.voting-history', ['model' => $participant->voter])
			
			@if(Auth::user()->permissions->developer)

				<div class="text-xl mt-4">DEV:</div>

				<div class="text-sm">

					<div class="font-bold">Range</div>

					<pre>
						{{ print_r($participant->voter->range) }}
					</pre>


					<div class="font-bold">Profile</div>
					
					<pre>
						{{ print_r($participant->voter->profile) }}
					</pre>

					<div class="font-bold">Elections</div>
					
					<pre>
						{{ print_r($participant->voter->elections) }}
					</pre>

				</div>

			@endif

		@endif

	@else

		<div class="my-4 mx-2 text-right">
			<a class="bg-blue text-white hover:text-white hover:bg-blue-dark px-3 py-2 text-sm uppercase" href="add-action">
				Add Action
			</a>
		</div>

	@endif

	@if($voter)

		@include('campaign.participants.tabs.voter-file', ['model' => $voter])

		@include('campaign.participants.tabs.profile', ['model' => $voter])

		@include('campaign.participants.tabs.voting-history', ['model' => $voter])

		@if(Auth::user()->permissions->developer)

			<div class="text-xl mt-4">DEV:</div>

			<div class="text-sm">

				<div class="font-bold">Range</div>

				<pre>
					{{ print_r($voter->range) }}
				</pre>


				<div class="font-bold">Profile</div>
				
				<pre>
					{{ print_r($voter->profile) }}
				</pre>

				<div class="font-bold">Elections</div>
				
				<pre>
					{{ print_r($voter->elections) }}
				</pre>

			</div>

		@endif

	@endif

	<!------------------------------------/ END COLUMN 2 /---------------------------------->
	</div>


</div>


<div class="text-right p-2 text-sm mt-6 w-full border-t-2 inline-flex">

<div class="float-right text-base flex-1">

	<a href="/{{ Auth::user()->team->app_type }}/voters">
    	<button type="button" class="mr-2 rounded-lg bg-grey-lighter text-black px-3 py-2">Cancel / Go Back to All Participants</button>
    </a>

    <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

    <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/participants/{{ $the_id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

 </div>
  
</div>


</form>



<!---------------------------- MODALS ---------------------------->

	
		
	@if ($participant)
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
			</div>
			<div class="modal-body">
			  <div class="text-lg text-left text-red font-bold">
			    Are you sure you want to delete this participant?
			  </div>
			  <div class="text-left font-bold py-2 text-base">
			    This will delete the participant.
			  </div>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
			  <a href="/{{ Auth::user()->team->app_type }}/participants/{{ $participant->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
			</div>
		</div>
	</div>
	@endif

<!-------------------------- END MODALS -------------------------->



@endsection


@section('javascript')

	@livewireScripts
	<script type="text/javascript">
		
		$(document).ready(function() {

	        $(document).on('click', "#show-all-elections", function () {
	        	$('#remainder-of-elections').toggleClass('hidden');
	        	$('#show-all-elections-div').toggleClass('hidden');
			});

	        $(document).on('click', "#show-fewer-elections", function () {
	        	$('#remainder-of-elections').toggleClass('hidden');
	        	$('#show-all-elections-div').toggleClass('hidden');
			});

		});

	</script>
@endsection