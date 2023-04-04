<!-- Modal -->

<div id="view-recipients" class="modal fade destroy-after-use" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- End Modal -->



<div class="text-xl font-bold text-black mt-8 border-b-4 border-grey pb-2">

	@if(Auth::user()->permissions->developer)
		<div class="float-right text-base">
			<label class="checkbox-inline pt-1 text-red">
				<input type="checkbox" id="bulk_email_array" name="bulk_email_array" {{ (Auth::user()->getMemory('bulk_email_array')) ? 'checked' : '' }}>
				<i class="fas fa-user-cog"></i> Show Array
			</label>
		</div>
	@endif

	<span class="text-grey-dark text-lg pr-2">2.</span> <i class="fas fa-user mr-2"></i>Recipients

</div>

<div class="flex">

	<div class="w-1/6 pl-8 pr-2">

		<div id="bulk-email-recipients-count" value="{{ $email->expected_count }}" class="bulk-email-recipients-count text-blue text-5xl font-bold p-2">{{ ($email->expected_count) ? $email->expected_count : 0 }}</div>

		<a href="/office/emails/view-recipients" 
		   data-toggle="modal" data-target="#view-recipients">
			<button class="rounded-lg border text-grey-darker text-sm px-2 py-1 m-1 w-full" type="button">
				Preview
			</button>
		</a>

		<button class="rounded-lg border text-grey-darker text-sm px-2 py-1 m-1 w-full" type="button" id="clear_all">
			Uncheck All
		</button>


	</div>

	<div class="w-5/6 p-2" id="recipients">

		<div class="flex border-b">
			<button id="button_add" class="tab-button rounded-t-lg bg-blue-darker text-white px-4 py-2 mx-1 w-full text-left" type="button">
			<i class="fas fa-plus-circle mr-2"></i> Add
			</button>

			<button id="button_remove" class="tab-button rounded-t-lg bg-blue text-white px-4 py-2 mx-1 w-full text-left" type="button">
			<i class="fas fa-minus-circle mr-2"></i> Remove
			</button>

			<button id="button_filter" class="tab-button rounded-t-lg bg-blue text-white px-4 py-2 mx-1 w-full text-left" type="button">
			<i class="fas fa-filter mr-2"></i> Filter
			</button>
		</div>



		@if(Auth::user()->getMemory('bulk_email_array'))
			<div class="text-grey-dark text-xs border-blue border-b-4">
				<pre>{{ print_r($email->recipients_form) }}</pre>
			</div>
		@endif

		@if($email->queued)
			<div class="text-left p-2 flex border-b-4 border-blue">
				<div class="px-2 pt-3 text-right">
					<i class="fas fa-check-circle mr-2"></i> This email has been queued, so you can't edit it.
				</div>
				<div class="text-left p-2">

					<a href="{{dir}}/emails/{{ $email->id }}/queueshow">
						<button type="button" class="rounded-lg px-4 float-right py-2 border bg-blue text-white text-center ml-2 hover:bg-blue-dark hover:text-white"/>
							See the email queue
						</button>
					</a>

				</div>
			</div>
		@endif


		<!-- Start form panel -->
		<div id="panel_add" class="form-panel text-left p-2 {{ ($email->queued) ? 'opacity-50' : '' }}">

			<div class="bg-grey-lighter p-2 border-grey-dark mb-1 -mt-1 -mx-1 border-t border-b-4 font-semibold text-xs uppercase text-grey-darkest text-center">
				<b>Add</b> people who match ANY of the below:
			</div>

<!-- 		<div class="flex">
			<div class="w-1/4">
				<label class="checkbox-inline pt-1">
					<input type="checkbox" name="recipients_masteremail" {{ (isset($email->recipients_form['masteremail'])) ? 'checked' : '' }}>
					Master Email List
				</label>
			</div>
		</div> -->


		<div class="flex flex-wrap">
			
			@foreach ($categories as $category)

				<div id="people-list-category-{{ $category->id }}" class="w-1/2">
					<div class="uppercase border-b text-sm font-bold mt-4 mr-4">
						<i class="fas fa-plus-circle mr-2"></i> {{ ucwords($category->name) }}
					</div>
					<div class="pl-4">
						@foreach ($category->groups()->where('team_id', Auth::user()->team_id)->orderBy('name')->get() as $group)
							
						<div class="checkbox group-checkbox">
						<label>


						<input type="checkbox" name="recipients_add_group_{{ $group->id }}" {{ (isset($email->recipients_form['add_group_'.$group->id])) ? 'checked' : '' }} >
						{{ $group->name }}
						
						@if($group->cat->has_position)
						<div class="position float-right ml-4">
							<select name="recipients_add_position_{{ $group->id }}">
								<option value="">- Any Position -</option>

								@foreach ($group->positions() as $position)

									<option value="{{ $position }}" {{ ((isset($email->recipients_form['add_position_'.$group->id])) && ($email->recipients_form['add_position_'.$group->id] == $position)) ? 'selected' : '' }}>
										{{ ucwords($position) }}
									</option>

								@endforeach
								
							</select>
						</div>
						@endif
						
							
						</label>
						</div>
									
						@endforeach
					</div>
				</div>
				@endforeach
				
			</div>

			<div class="uppercase font-bold text-sm pt-2 border-t mt-2">
				<i class="fas fa-plus-circle mr-2"></i> Recipients of previous emails
			</div>

			<div class="pl-4">
				@if($completed->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						No other emails have been sent yet
					</div>

				@else
					@foreach ($completed as $thecompleted)
						
						<div class="checkbox group-checkbox">
							<label>

							<input type="checkbox" name="recipients_add_sent_{{ $thecompleted->id }}" {{ (isset($email->recipients_form['add_sent_'.$thecompleted->id])) ? 'checked' : '' }} >
							<span class="text-xs">
							{{ \Carbon\Carbon::parse($thecompleted->completed_at)->diffForHumans() }}
							</span>
							<span class="ml-2 font-bold">
								{{ $thecompleted->name }}
							</span>
							({{ $thecompleted->queuedAndSentCount() }})
								
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

			<!-- SECTION -->
			<div class="uppercase font-bold text-sm pt-2 border-t mt-6">
				<i class="fas fa-plus-circle mr-2"></i> Community
			</div>

			<div class="pl-4">
				@if($cities->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						Strangely, no towns.
					</div>

				@else
					@foreach ($cities as $thecity)
						
						<div class="checkbox group-checkbox">
							<label class="font-">

							<input type="checkbox" name="recipients_add_city_{{ $thecity }}" {{ (isset($email->recipients_form['add_city_'.$thecity])) ? 'checked' : '' }} >
								{{ $thecity }}
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

		</div>
		<!-- End form panel -->


	<!-- Start form panel -->
	<div id="panel_remove" class="form-panel hidden text-left p-2 {{ ($email->queued) ? 'opacity-50' : '' }}">

			<div class="bg-grey-lighter p-2 border-grey-dark mb-1 -mt-1 -mx-1 border-t border-b-4 font-semibold text-xs uppercase text-grey-darkest text-center">
				<b>Remove</b> people who match ANY of the below:
			</div>

		<div class="flex flex-wrap w-full">
			
			@foreach ($categories as $category)

				<div id="people-list-category-{{ $category->id }}" class="w-1/2">
					<div class="uppercase border-b text-sm font-bold mt-4 mr-4">
						<i class="fas fa-minus-circle mr-2"></i> {{ ucwords($category->name) }}
					</div>
					<div class="pl-4">
						@foreach ($category->groups()->where('team_id', Auth::user()->team_id)->orderBy('name')->get() as $group)
							
						<div class="checkbox group-checkbox">
						<label>


						<input type="checkbox" name="recipients_ex_group_{{ $group->id }}" {{ (isset($email->recipients_form['ex_group_'.$group->id])) ? 'checked' : '' }} >
						{{ $group->name }}
						
						@if($group->cat->has_position)
						<div class="position float-right ml-4">
							<select name="recipients_ex_position_{{ $group->id }}">
								<option value="">- Any Position -</option>

								@foreach ($group->positions() as $position)

									<option value="{{ $position }}" {{ ((isset($email->recipients_form['ex_position_'.$group->id])) && ($email->recipients_form['ex_position_'.$group->id] == $position)) ? 'selected' : '' }}>
										{{ ucwords($position) }}
									</option>

								@endforeach
								
							</select>
						</div>
						@endif
						
							
						</label>
						</div>
									
						@endforeach
					</div>
				</div>
				@endforeach
				
			</div>

			<!-- SECTION -->
			<div class="uppercase font-bold text-sm pt-2 border-t mt-2">
				<i class="fas fa-minus-circle mr-2"></i> Recipients of previous emails
			</div>

			<div class="pl-4">
				@if($completed->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						No other emails have been sent yet
					</div>

				@else
					@foreach ($completed as $thecompleted)
						
						<div class="checkbox group-checkbox">
							<label>

							<input type="checkbox" name="recipients_ex_sent_{{ $thecompleted->id }}" {{ (isset($email->recipients_form['ex_sent_'.$thecompleted->id])) ? 'checked' : '' }} >
							<span class="text-xs">
							{{ \Carbon\Carbon::parse($thecompleted->completed_at)->diffForHumans() }}
							</span>
							<span class="ml-2 font-bold">
								{{ $thecompleted->name }}
							</span>
							({{ $thecompleted->queuedAndSentCount() }})
								
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

			<!-- SECTION -->
			<div class="uppercase font-bold text-sm pt-2 border-t mt-6">
				<i class="fas fa-minus-circle mr-2"></i> Community
			</div>

			<div class="pl-4">
				@if($cities->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						Strangely, no towns.
					</div>

				@else
					@foreach ($cities as $thecity)
						
						<div class="checkbox group-checkbox">
							<label class="font-">

							<input type="checkbox" name="recipients_ex_city_{{ $thecity }}" {{ (isset($email->recipients_form['ex_city_'.$thecity])) ? 'checked' : '' }} >
								{{ $thecity }}
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

		</div>
		<!-- End form panel -->


		<!-- Start form panel -->
		<div id="panel_filter" class="form-panel hidden text-left p-2 {{ ($email->queued) ? 'opacity-50' : '' }}">

			<div class="bg-grey-lighter p-2 border-grey-dark mb-1 -mt-1 -mx-1 border-t border-b-4 font-semibold text-xs uppercase text-grey-darkest text-center">
				Of the people added/removed, <b>only include</b> those who match ALL of the below:
			</div>

		<div class="flex flex-wrap w-full">
			
			@foreach ($categories as $category)

				<div id="people-list-category-{{ $category->id }}" class="w-1/2">
					<div class="uppercase border-b text-sm font-bold mt-4 mr-4">
						<i class="fas fa-filter mr-2"></i> {{ ucwords($category->name) }}
					</div>
					<div class="pl-4">
						@foreach ($category->groups()->where('team_id', Auth::user()->team_id)->orderBy('name')->get() as $group)
							
						<div class="checkbox group-checkbox">
						<label>


						<input type="checkbox" name="recipients_only_group_{{ $group->id }}" {{ (isset($email->recipients_form['only_group_'.$group->id])) ? 'checked' : '' }} >
						{{ $group->name }}
						
						@if($group->cat->has_position)
						<div class="position float-right ml-4">
							<select name="recipients_only_position_{{ $group->id }}">
								<option value="">- Any Position -</option>

								@foreach ($group->positions() as $position)

									<option value="{{ $position }}" {{ ((isset($email->recipients_form['only_position_'.$group->id])) && ($email->recipients_form['only_position_'.$group->id] == $position)) ? 'selected' : '' }}>
										{{ ucwords($position) }}
									</option>

								@endforeach
								
							</select>
						</div>
						@endif
						
							
						</label>
						</div>
									
						@endforeach
					</div>
				</div>
				@endforeach
				
			</div>

			<!-- SECTION -->
			<div class="uppercase font-bold text-sm pt-2 border-t mt-2">
				<i class="fas fa-filter mr-2"></i> Recipients of previous emails
			</div>

			<div class="pl-4">
				@if($completed->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						No other emails have been sent yet
					</div>

				@else
					@foreach ($completed as $thecompleted)
						
						<div class="checkbox group-checkbox">
							<label>

							<input type="checkbox" name="recipients_only_sent_{{ $thecompleted->id }}" {{ (isset($email->recipients_form['only_sent_'.$thecompleted->id])) ? 'checked' : '' }} >
							<span class="text-xs">
							{{ \Carbon\Carbon::parse($thecompleted->completed_at)->diffForHumans() }}
							</span>
							<span class="ml-2 font-bold">
								{{ $thecompleted->name }}
							</span>
							({{ $thecompleted->queuedAndSentCount() }})
								
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

			<!-- SECTION -->
			<div class="uppercase font-bold text-sm pt-2 border-t mt-6">
				<i class="fas fa-filter mr-2"></i> Community
			</div>

			<div class="pl-4">
				@if($cities->count() <= 0)

					<div class="py-2 text-grey-dark text-sm">
						Strangely, no towns.
					</div>

				@else
					@foreach ($cities as $thecity)
						
						<div class="checkbox group-checkbox">
							<label class="font-">

							<input type="checkbox" name="recipients_only_city_{{ $thecity }}" {{ (isset($email->recipients_form['only_city_'.$thecity])) ? 'checked' : '' }} >
								{{ $thecity }}
							</label>
						</div>
								
					@endforeach
				@endif
			</div>

		</div>
		<!-- End form panel -->



	</div>
</div>






