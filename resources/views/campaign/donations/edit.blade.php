@extends('campaign.base')

@section('title')
    Edit Donation
@endsection

@section('style')


@endsection

@section('main')

<div class="w-full">



	<div class="border-b-4 border-blue text-xl pb-2">


	 <div class="float-right text-sm">
	      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete donation
	      </button>
	  </div>

		Edit Donation

	</div>


<form action="/{{ Auth::user()->team->app_type }}/donations/{{ $donation->id }}/update" method="post">

	@csrf

	<!-- Edit body-->

	<div class="p-1">
		
		<div class="flex -my-1 bg-blue shadow -mx-1 ">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4 text-white">
				Date
			</div>
			<div class="p-2">
				<input name="date" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" value="{{ \Carbon\Carbon::parse($donation->date)->format('m/d/Y') }}"  />
			</div>

			<div class="text-sm uppercase text-right p-2 pt-4 text-white">
				Amount
			</div>
			<div class="p-2">
				<input name="amount" size="10" type="text" class="border rounded-lg p-2" placeholder="0.00" value="{{ $donation->amount }}" />
			</div>
			<div class="text-sm uppercase text-right p-2 pt-4 text-white">
				Fee
			</div>
			<div class="p-2">
				<input name="fee" size="10" type="text" class="text-red border rounded-lg p-2" placeholder="0.00" value="{{ $donation->fee }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Method
			</div>
			<div class="p-4 w-3/4">
				<select name="method">
					<option value="" {{ ($donation->method == '') ? 'selected' : '' }}>
						--
					</option>
					@foreach(['ActBlue', 'Cash', 'Check', 'Credit Card', 'In-Kind', 'WinRed', 'Online'] as $method)
						<option value="{{ $method}}" {{ ($donation->method == $method) ? 'selected' : '' }}>
							{{ $method }}
						</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Event
			</div>
			<div class="p-4 w-3/4">

				@if(!$events->first())

					<span class="text-grey-dark">No events yet</span>

				@else

					<select name="campaign_event_id">
						<option value="" {{ ($donation->campaign_event_id == '') ? 'selected' : '' }}>
							-- SELECT AN EVENT --
						</option>
						@foreach($events as $event)
							<option value="{{ $event->id }}" {{ ($donation->campaign_event_id == $event->id) ? 'selected' : '' }}>
								{{ $event->date }} - {{ $event->name }}
							</option>
						@endforeach
					</select>

				@endif
			</div>
		</div>

	</div>


	<!-- Edit Section -->

	<div class="p-1 border-t">


		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Linked To
			</div>
			<div class="p-2 pt-3 w-3/4">

				<div class="flex">

					<input name="lookup" id="lookup" type="text" class="w-1/3 border bg-grey-lightest rounded-lg p-2" placeholder="Voter Look Up" />

					<input type="hidden" name="participant_id" />

					<div class="p-1 text-blue flex">

						@if($donation->participant_id)
							<label for="linked" class="font-normal mx-2">
								<input type="checkbox" name="linked" id="linked" {{ ($donation->participant_id) ? 'checked' : '' }} /> Linked to:
							</label>
						@else
							<span class="text-red mx-2">Not linked to a Voter / Participant</span>
						@endif

						@if($donation->participant && $donation->participant->voter)

							<a href="/campaign/participants/{{ $donation->participant_id }}">
								<div id="lookup_selection">
									<i class="text-center fa fa-user mx-2 text-blue"></i>
									{{ $donation->participant->voter->full_name }}
									|
									{{ $donation->participant->voter_id }}
								</div>
							</a>
								
						@elseif($donation->participant)

							<a href="/campaign/participants/{{ $donation->participant_id }}">
								<div id="lookup_selection">
									<i class="text-center fa fa-user mx-2 text-blue"></i>
									{{ $donation->participant->full_name }}
									|
									ID # {{ $donation->participant->id }}
								</div>
							</a>

						@endif

						<div id="lookup_selection">
						</div>

					</div>

				</div>

				<div class="hidden w-full bg-blue p-1" id="list">
				</div>

				<div id="checkbox-div">
				</div>

			</div>
		</div>


		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				First Name
			</div>
			<div class="p-2 w-3/4">
				<input name="first_name" type="text" class="w-full border rounded-lg p-2" placeholder="First Name" value="{{ $donation->first_name }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Last Name
			</div>
			<div class="p-2 w-3/4">
				<input name="last_name" type="text" class="w-full border rounded-lg p-2" placeholder="Last Name" value="{{ $donation->last_name }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Address
			</div>
			<div class="p-2 w-3/4">
				<input name="street" type="text" class="w-2/3 border rounded-lg p-2" placeholder="Street" value="{{ $donation->street }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Address 2
			</div>
			<div class="p-2">
				<input name="city" type="text" class="border rounded-lg p-2" placeholder="City" value="{{ $donation->city }}" />
			</div>

			<div class="p-2">
				<input name="state" type="text" class="w-12 border rounded-lg p-2" placeholder="{{ Auth::user()->team->account->state }}" value="{{ $donation->state }}" />
			</div>

			<div class="p-2">
				<input name="zip" type="text" class="w-16 border rounded-lg p-2" placeholder="Zip" value="{{ $donation->zip }}" />
			</div>
		</div>

	</div>

	<!-- Edit Section -->

	<div class="p-1 border-t">

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Occupation
			</div>
			<div class="p-2">
				<input name="occupation" type="text" class="border rounded-lg p-2" placeholder="Occupation" value="{{ $donation->occupation }}" />
			</div>
		</div>

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Employer
			</div>
			<div class="p-2">
				<input name="employer" type="text" class="border rounded-lg p-2" placeholder="Employer" value="{{ $donation->employer }}" />
			</div>
		</div>

	</div>

	<!-- Modal Section -->

	<div class="p-1 border-t">

		<div class="flex -my-1">
			<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
				Notes
			</div>
			<div class="p-2 w-3/4">
				<textarea rows="4" name="notes" class="border rounded-lg p-2 w-full" placeholder="Notes">{{ $donation->notes }}</textarea>
			</div>
		</div>

	</div>


</div>

  <div class="text-right pt-2 text-sm mt-1 border-t-4 border-grey-dark">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/donations/{{ $donation->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      </div>
      
  </div>

</form>


<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this donation?
          </div>
          <div class="text-left font-bold py-2 text-base">
            This will delete the donation.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/donations/{{ $donation->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

<br /><br />
@endsection


@section('javascript')
<script type="text/javascript">


	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('/{{ Auth::user()->team->app_type }}/participants/lookup/donations/'+v, function(response) {

			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}

	$(document).ready(function() {

		$("#lookup").focusout(function(){
			window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
		});
		
		$("#lookup").keyup(function(){
			getSearchData(this.value);
		});

	   	$(document).on('click', ".participant-select", function () {
	   		$("#lookup_selection").html($(this).data("first_name")+' '+$(this).data("last_name"));
	   		$( "input[name='first_name']").val($(this).data("first_name"));
	   		$( "input[name='last_name']").val($(this).data("last_name"));
	   		$( "input[name='street']").val($(this).data("street"));
	   		$( "input[name='city']").val($(this).data("city"));
	   		$( "input[name='ma']").val($(this).data("ma"));
	   		$( "input[name='zip']").val($(this).data("zip"));
			$( "input[name='participant_id']").val($(this).data("id"));
			$("#lookup").select();
	    });

	});

</script>
@endsection