@extends('campaign.base')

@section('title')
    Show Participant
@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

<div class="w-full">

	

	<div class="border-b-4 border-blue text-xl py-1">

	 <div class="float-right text-sm">

	 	<a href="/{{ Auth::user()->team->app_type }}/participants/{{ $the_id }}/edit">
	      <button type="button" class="rounded-lg p-2 text-white bg-blue text-center ml-2"/>
	        Edit Participant
	      </button>
	  </a>

	  </div>

	 	@if($participant)
			<div class="text-3xl font-bold">{{ $participant->full_name }}</div>
			<div class="text-grey-darker">{{ $participant->full_address }}</div>
		@endif

		@if($voter)
			<div class="text-3xl font-bold">{{ $voter->full_name }}</div>
			<div class="text-grey-darker">{{ $voter->full_address }}</div>
		@endif

	</div>

<div class="flex mt-2">

	<!------------------------------------/ COLUMN 1 /---------------------------------->
	<div class="w-1/2 mr-4">

		@if($participant)

		 	@include('campaign.participants.tabs.phone-email', ['model' => $participant])

			@include('campaign.participants.tabs.tags')

			@include('campaign.participants.tabs.support')
		
			@include('campaign.participants.tabs.contributions')

			@include('campaign.participants.tabs.invitations')

		@else

			<div class="text-grey-dark py-4">
				Edit this voter to add tags, support levels, etc.
			</div>

		@endif

	<!------------------------------------/ END COLUMN 1 /---------------------------------->
	</div>
	<div class="w-1/2 ml-2 pl-2">
	<!------------------------------------/ COLUMN 2 /---------------------------------->

		@if($participant)


			@if(!$participant->voter || isset($_GET['edit_voter_id']))
				<div class="p-2 text-grey-dark mt-1">
					This participant is not linked to Voter in your district.
				</div>

				@if(Auth::user()->permissions->developer)

					<div class="border">
						<div class="border-b bg-blue text-white p-2">
							Look Up Voters and Link
						</div>

						<div class="p-2">
							@livewire('voter-link.one-voter', ['model' => $participant])
						</div>
					</div>

				@endif

			@else
				@include('campaign.participants.tabs.voter-file', 
						['model' => $participant->voter])
			@endif
		@endif

		@if($voter)
			@include('campaign.participants.tabs.voter-file', 
					['model' => $voter])
		@endif

		@if($participant && $participant->voter)
			@include('campaign.participants.tabs.profile', 
					['model' => $participant->voter])
		@endif

		@if($voter)
			@include('campaign.participants.tabs.profile', 
					['model' => $voter])
		@endif

		@if($participant && $participant->voter)
			@include('campaign.participants.tabs.voting-history', 
					['model' => $participant->voter])
		@endif

		@if($voter)
			@include('campaign.participants.tabs.voting-history', 
					['model' => $voter])
		@endif


	<!------------------------------------/ END COLUMN 2 /---------------------------------->
	</div>


</div>


<div class="text-right p-2 text-sm mt-6 w-full inline-flex">

	<div class="text-center text-base flex-1 pt-2">

	    <a href="/{{ Auth::user()->team->app_type }}/voters">
	    	<button class="rounded-lg bg-blue text-white px-3 py-2">Go Back to All Participants</button>
	    </a>

	 </div>
  
</div>

<br />
<br />
<br />


<!---------------------------- MODALS ---------------------------->

	@include('campaign.donations.modal-add', ['events' => Auth::user()->team->events, 'participant' => $participant])


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