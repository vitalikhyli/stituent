<div class="w-1/2 mr-4">

	<!-- Section -->
	<div class="w-full">
		<div class="text-lg px-2 pb-3 my-2 cursor-pointer border-b">
			<button class="btn_newgroup float-right rounded-full bg-grey-lighter border hover:text-white hover:bg-blue text-black text-sm px-2 py-1 m-1 cursor-pointer" data-category="{{ \App\Category::where('name','volunteers')->where('preset','campaign')->first()->id}}" data-form="volunteers">Add
			</button>@lang('Volunteer Jobs')
		</div>
		<div id="volunteers"></div>
		<div>

			@if(!isset($volunteers) || $volunteers->count() <= 0 )
				<div class="m-4 text-grey-dark">None</div>
			@else
			@foreach($volunteers as $thegroup)
				<div class="ml-4 mr-2 p-2 cursor-pointer hover:bg-grey-lighter rounded-lg flex">
					<div class="flex-1 flex-initial w-8">
						<i class="fas fa-hands-helping mr-4"></i>
					</div>
					<div class="flex-1 flex-initial">
						{{$thegroup->name}}
						@if($thegroup->pivotdata)
							@if($thegroup->pivotdata->notes)
								<div id="{{ $thegroup->pivot->id }}" data-json="notes" class="switchform text-blue text-sm">
									{{ $thegroup->pivotdata->notes }}
								</div>
							@else
								<div id="{{ $thegroup->pivot->id }}" data-json="notes" class="switchform text-blue text-sm">
									(Add Note)
								</div>
							@endif
						@endif
					</div>
					<div class="flex-1">
						<a href="/campaign/constituents/{{ $person->id }}/group_remove/{{ $thegroup->pivot->id }}" class="float-right hover:font-black hover:text-white hover:bg-red px-2 rounded-lg">
							X
						</a>
					</div>
				</div>
			@endforeach
			@endif
		</div>
	</div>
	<!-- /Section -->


</div>
<div class="w-1/2 mr-4">

	<!-- Section -->
	<div class="w-full">
		<div class="text-lg px-2 pb-3 my-2 cursor-pointer border-b">
			<button class="btn_newgroup float-right rounded-full bg-grey-lighter border hover:text-white hover:bg-blue text-black text-sm px-2 py-1 m-1 cursor-pointer" data-category="{{ \App\Category::where('name','campaign issues')->where('preset','campaign')->first()->id}}" data-form="issues">Add
			</button> @lang('Campaign Issues')
		</div>
		<div id="issues"></div>
		<div>
			@if(!isset($issues) || $issues->count() <= 0 )
				<div class="m-4 text-grey-dark">None</div>
			@else
			@foreach($issues as $thegroup)
				
				<div class="ml-4 mr-2 p-2 cursor-pointer hover:bg-grey-lighter rounded-lg flex">
				<div class="flex-1 flex-none w-8">
					@if($thegroup->pivotdata->position == "Support")
						<i class="fas fa-thumbs-up mr-4 text-blue"></i>
					@elseif($thegroup->pivotdata->position == "Oppose")
						<i class="fas fa-thumbs-down mr-4 text-red"></i>
					@elseif($thegroup->pivotdata->position == "Undecided")
						<i class="fas fa-question"></i>
					@else
						<i class="fas fa-edit"></i>
					@endif
				</div>
				<div class="flex-1 flex-initial w-full">
					{{$thegroup->name}}
					<a href="/campaign/constituents/{{ $person->id }}/cycle_issue_position/{{ $thegroup->pivot->id }}" class="hover:bg-orange-lighter rounded-full px-2 py-1 text-sm">
					@if($thegroup->pivotdata->position)
						{{ $thegroup->pivotdata->position }}
					@else
						(Add a position)
					@endif
					</a>
					<div class="text-sm w-full">
						@if($thegroup->pivotdata->notes)
							<div id="{{ $thegroup->pivot->id }}" data-json="notes" class="switchform text-blue">
								{{ $thegroup->pivotdata->notes }}
							</div>
						@else
							<div id="{{ $thegroup->pivot->id }}" data-json="notes" class="switchform text-blue">
								(Add Note)
							</div>
						@endif
					</div>
					
				</div>
					<div class="flex-1">
						<a href="/campaign/constituents/{{ $person->id }}/group_remove/{{ $thegroup->pivot->id }}" class="float-right hover:font-black hover:text-white hover:bg-red px-2 rounded-lg">
							X
						</a>
					</div>
				</div>
				
			@endforeach
			@endif
		</div>
	</div>
	<!-- /Section -->


</div>
