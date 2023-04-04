<div id="add-donation" class="modal fade" role="dialog" wire:ignore>

	<div id="add-donation-form">

     	
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header bg-blue-dark text-white">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fas fa-money-bill-alt mr-2"></i> Add a Donation</h4>
				</div>

				<!-- Modal body-->

				<div class="p-1">
					
					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Date
						</div>
						<div class="p-2">
							<input wire:ignore autocomplete="off" wire:model="new_donation.date" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
						</div>
					</div>

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Amount
						</div>
						<div class="p-2">
							<input wire:model="new_donation.amount" size="10" type="text" class="border rounded-lg p-2" placeholder="0.00" />
						</div>
						<div class="text-sm uppercase text-right p-2 pt-4">
							<span class="text-red">Fee</span>
						</div>
						<div class="p-2">
							<input wire:model="new_donation.fee" size="10" type="text" class="text-red border rounded-lg p-2" placeholder="0.00" />
						</div>
					</div>

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-3">
							Method
						</div>
						<div class="p-2">
							<select wire:model="new_donation.method">
								<option value="">
									--
								</option>
								@foreach(['Check', 'Credit Card', 'Cash', 'In-Kind', 'ActBlue', 'WinRed', 'Online'] as $method)
									<option value="{{ $method }}">
										{{ $method }}
									</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="flex -my-2">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Event
						</div>
						<div class="p-2 pt-3 w-3/4">
							@if(!$events->first())
								<div class="pt-1 text-grey-dark">No events yet</div>
							@else
								<select wire:model="new_donation.campaign_event_id">
									<option value="">
										-- SELECT AN EVENT --
									</option>
									@foreach($events as $event)
										<option value="{{ $event->id }}">
											{{ $event->date }} - {{  mb_strimwidth($event->name, 0, 40, "...") }}
										</option>
									@endforeach
								</select>
							@endif
						</div>
					</div>

				</div>


				@if(isset($participant))

				<!-- {{ $participant->full_name }} -->

				<input wire:model="new_donation.return_to_participant" type="hidden" value="{{ $participant->id }}" />
				<input wire:model="new_donation.the_id" 	 type="hidden" value="{{ $participant->id }}" />
				<input wire:model="new_donation.first_name" type="hidden" value="{{ $participant->first_name }}" />
				<input wire:model="new_donation.last_name"  type="hidden" value="{{ $participant->last_name }}" />
				<input wire:model="new_donation.street" 	 type="hidden" value="{{ $participant->street }}" />
				<input wire:model="new_donation.city" 		 type="hidden" value="{{ $participant->city }}" />
				<input wire:model="new_donation.state" 	 type="hidden" value="{{ $participant->state }}" />
				<input wire:model="new_donation.zip" 		 type="hidden" value="{{ $participant->zip }}" />

				@else
					<!-- Modal Section -->

					<div class="bg-blue mt-2 text-center py-2">
						<input wire:model="new_donation.lookup" id="lookup" type="text" class="w-3/4 border border-blue-dark rounded-lg p-2" placeholder="Voter Look Up" />

						<input type="hidden" wire:model="new_donation.the_id" />
						<!-- <div class="text-white">ID#: 
							<input type="text" class="bg-blue" wire:model="new_donation.the_id" />
						</div> -->

						<div class="hidden w-full" id="list">
						</div>

					</div>

					<div class="p-1">

						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								First Name
							</div>
							<div class="p-2 w-3/4">
								<input required wire:model="new_donation.first_name" type="text" class="w-full border rounded-lg p-2" placeholder="First Name" />
							</div>
						</div>

						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								Last Name
							</div>
							<div class="p-2 w-3/4">
								<input required wire:model="new_donation.last_name" type="text" class="w-full border rounded-lg p-2" placeholder="Last Name" />
							</div>
						</div>

						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								Address
							</div>
							<div class="p-2 w-3/4">
								<input wire:model="new_donation.street" type="text" class="w-2/3 border rounded-lg p-2" placeholder="Street" />
							</div>
						</div>

						<div class="flex -my-1">
							<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
								Address 2
							</div>
							<div class="p-2">
								<input wire:model="new_donation.city" type="text" class="border rounded-lg p-2" placeholder="City" />
							</div>

							<div class="p-2">
								<input wire:model="new_donation.state" type="text" class="w-12 border rounded-lg p-2" placeholder="{{ Auth::user()->team->account->state }}" />
							</div>

							<div class="p-2">
								<input wire:model="new_donation.zip" type="text" class="w-16 border rounded-lg p-2" placeholder="Zip" />
							</div>
						</div>

					</div>

					<!-- Modal Section -->

				@endif

				<div class="p-1 border-t">

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Occupation
						</div>
						<div class="p-2">
							<input wire:model="new_donation.occupation" type="text" class="border rounded-lg p-2" placeholder="Occupation" />
						</div>
					</div>

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Employer
						</div>
						<div class="p-2">
							<input wire:model="new_donation.employer" type="text" class="border rounded-lg p-2" placeholder="Employer" />
						</div>
					</div>

				</div>

				<!-- Modal Section -->

				<div class="p-1 border-t">

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Notes
						</div>
						<div class="p-2 w-3/4">
							<textarea wire:model="new_donation.notes" class="border rounded-lg p-2 w-full" placeholder="Notes"></textarea>
						</div>
					</div>

				</div>


				<!-- Modal footer -->

				<div class="modal-footer bg-grey-light text-white">

					<button type="button" class="btn btn-default" data-dismiss="modal">
						Close
					</button>

					<button wire:click="save()" data-dismiss="modal" type="button" class="btn btn-primary" >
						Save
					</button>

				</div>
			</div>

		</div>

	</div>
</div>


<script type="text/javascript">
	$('.datepicker').datepicker(); //Need this here because modal
	$('.datepicker').on('change', function(e) {
		@this.set('new_donation.date', e.target.value);
	});
</script>