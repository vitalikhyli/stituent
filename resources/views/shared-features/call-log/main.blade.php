<div class="flex">
	
	<div class="w-1/3 pr-6">
		<div class="text-2xl border-b pb-2">Notes</div>

		<div class="pt-4">


			<form method="post" id="call-log-search" action="/call-log/search">
				@csrf
				<input type="text" name="search_value" id="call-log-search_value" class="bg-grey-lighter text-black px-2 py-2 rounded-lg border" placeholder="Search notes here" />
				<input type="submit" value="Search" name="search" class="rounded-lg bg-grey-lighter text-grey-darker p-2 text-sm border" />
			</form>
		

			<div class="p-1 mt-2 text-base text-blue cursor-pointer" data-toggle="modal" data-target="#call-log-custom-dates-modal">
		      	<i class="far fa-file-alt mr-2"></i> Create a Notes Report
		    </div>


		</div>


	</div>
	
	<div class="w-2/3 text-base text-grey-darker shadow border p-8 relative">
	<!-- style="transform: rotate(1deg);" -->
		
			<img class="absolute pin-t pin-r" style="top: -42px; right: -20px;" src="/images/stupid_paperclip.png" />

			<form id="call-log-add" method="POST" action="/call-log">
				{{ csrf_field() }}
	
				<div class="w-full -mt-2">

					<div class="flex w-full">

						<div class="w-1/2 flex p-2 px-3 bg-blue rounded-t-lg">

							<div class="w-1/2 pr-2">

								<select name="type" class="h-8 w-full">
									@foreach(Auth::user()->team->contactTypes() as $key => $type)

									   	<option value="{{ ucwords($type) }}">
									   		{{ ucwords($type) }}
									   	</option>

									@endforeach
								</select>

							</div>

							<div class="w-1/2">

					   			<input type="text" name="type-other" autocomplete="off" placeholder="Other?" class="h-8 px-2 py-1 text-sm text-black font-bold rounded w-full" />

					   		</div>

					   	</div>

					   	<div class="w-1/2 pl-2 pt-2 flex">

							<input type="text" name="date" autocomplete="off" placeholder="Date" class="datepicker border px-2 py-1 h-8 text-sm text-black font-bold w-1/3" />
					
							<div x-data="{ use_time: false }">

							    <button @click="use_time = true"
							    		x-show="!use_time"
							    		class="p-2 text-sm cursor-pointer text-blue"
							    		type="button">Set Time</button>

							    <div x-show="use_time">

									<input name="time" class="ml-1 border px-2 py-1 h-8 text-sm text-black font-bold w-1/2" placeholder="{{ \Carbon\Carbon::now()->format('h:i A') }}"/>

							    </div>

							</div>

					   	</div>

					</div>

					<div class="border-t-4 border-blue-dark">

						<input id="call-subject"
							   type="text"
							   placeholder="Who?"
							   name="subject"
							   value=""
							   required
							   autocomplete="off"
							   class="border-l-2 border-r-2 border-b-2 text-lg px-4 py-3 w-full font-bold bg-grey-lightest" />
						
						<textarea 
							   id="call-log-notes"
							   required
							   name="notes" 
							   class="border-2 p-4 border-t-0 text-lg w-full bg-grey-lightest"
							   placeholder="Notes"
							   rows="4"></textarea>

						<div class="text-blue-light text-sm italic text-right">
							* Phone numbers and emails will be automatically detected in notes
						</div>

					</div>

					<div class="" id="call-add">
					</div>

				</div>

				<div id="call-list" class="hidden"></div>


				<div class="flex items-end">

					<div class="w-full pl-6">

						<div class="my-1 text-sm uppercase">
							<label class="checkbox cursor-pointer inline">
								<input type="checkbox" 
									   name="private"
									   autocomplete="off">
								
								<i class="fa fa-lock" style="color: #999;"></i> <span class="font-normal">Only {{ Auth::user()->name }} &amp; Admins</span>
								
							</label>
						</div>

						<div class="my-1 text-sm">
							<label class="checkbox cursor-pointer inline uppercase">
								<input type="checkbox" 
									   autocomplete="off"
									   name="followup" id="followup">

								<i class="fas fa-hand-point-right text-red"></i>
								<!-- <i class="fa fa-exclamation-triangle text-red"></i> -->

								<span class="text-red font-light">Follow Up</span>
								
							</label>
						</div>

						<div class="hidden" id="followup_on">
							on this date: <input type="text" name="followup_on" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" class="datepicker border px-2 py-1 rounded-lg" />
						</div>

					</div>

					<div class="w-2/3">

						<input type="submit"
							   class="cursor-pointer hover:bg-blue-dark rounded-lg float-right py-2 px-4 bg-blue text-white"
							   value="Save"
							   />

						<button type="button"
								class="cursor-pointer hover:bg-green-dark rounded-lg float-right py-2 px-4 mr-2"
							   name="save_as_new_case"
							   id="save_as_new_case">Save as New Case</button>

								<input type="hidden" id="save_as_new_case_serialize" name="save_as_new_case_serialize" class="border" />

					</div>

				</form>
			

		</div>
	</div>
</div>

@include('shared-features.call-log.content', ['call_log' => new \App\CallLogViewModel(Auth::user())])

<!-- Modal -->
<div id="call-log-edit-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		

	</div>
</div>

<div id="call-log-connect-modal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		

	</div>
</div>



