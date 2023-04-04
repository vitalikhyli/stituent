@extends('campaign.base')

@section('title')
    Edit Campaign
@endsection

@section('style')


@endsection

@section('main')

<div class="w-full">



	<div class="border-b-4 border-blue text-xl pb-2">


	 <div class="float-right text-sm">
	      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Campaign
	      </button>
	  </div>

		Edit {{ $campaign->name }}

	</div>


<form action="/{{ Auth::user()->team->app_type }}/campaigns/{{ $campaign->id }}/update" method="post">

	@csrf

	<div class="table w-full">

		<div class="table-row">

			<div class="table-cell p-2 border-b bg-grey-lighter whitespace-no-wrap">
				Campaign Name
			</div>

			<div class="table-cell p-2 border-b">
				
				<input type="text" name="name" placeholder="Name" value="{{ $campaign->name }}" class="border p-2 rounded-lg w-2/3" />

			</div>

		</div>

		<div class="table-row">

			<div class="table-cell p-2 border-b bg-grey-lighter whitespace-no-wrap">
				Election Date
			</div>

			<div class="table-cell p-2 border-b">
				
				<input autocomplete="off" type="text" name="election_day" placeholder="Date of the Election" value="{{ \Carbon\Carbon::parse($campaign->election_day)->format('m/d/Y') }}" class="datepicker border p-2 rounded-lg" />

			</div>

		</div>

		<div class="table-row">

			<div class="table-cell p-2 border-b bg-grey-lighter whitespace-no-wrap">
				Current Campaign
			</div>

			<div class="table-cell p-2 border-b">
				
				<input type="checkbox" class="mr-4" id="current" name="current" {!! ($campaign->current) ? 'checked' : '' !!} />

				<label for="current" class="font-normal">
				Yes <span class="text-blue">(This changes it for all users)</span>
				</label>


			</div>

		</div>

		<div class="table-row">

			<div class="{{ (Request::input('votes_needed')) ? 'bg-yellow-lighter font-bold' : 'bg-grey-lighter' }} table-cell p-2 border-b whitespace-no-wrap">
				Votes Needed to Win
			</div>

			<div class="{{ (Request::input('votes_needed')) ? 'bg-yellow-lighter font-bold' : '' }} table-cell p-2 border-b">
				
				<input autocomplete="off" type="text" name="votes_needed" id="votes_needed" placeholder="Vote Goal" value="{{ ($campaign->votes_needed) ? number_format($campaign->votes_needed) : '' }}" class="border p-2 rounded-lg" />

			</div>

		</div>

	</div>



</div>

  <div class="text-right pt-2 text-sm mt-1">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/campaigns/{{ $campaign->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      </div>
      
  </div>


<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this campaign?
          </div>
          <div class="text-left font-bold py-2 text-base">
            This will delete the campaign.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/campaigns/{{ $campaign->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

<br /><br />
@endsection


@section('javascript')

	@if(Request::input('votes_needed'))

		<script type="text/javascript">
			
			$(document).ready(function() {

				$('#votes_needed').focus()
			});

		</script>

	@endif
@endsection