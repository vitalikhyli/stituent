<div>
    <div class="w-full flex text-lg font-bold pb-2">

		@if($guest)
	    	<div class="w-4/5 text-3xl truncate">

				<span class="text-blue">
					<i class="fas fa-phone mr-1"></i> List:
				</span>

	    		{{ $list->name }}
	    	</div>
		@endif


		@if(!$guest)
	    	<div class="w-1/5 text-3xl truncate">

	    		{{ $list->name }}
	    	</div>
			<div class="w-3/5 mt-3 text-center">

				<div class="flex">
					<img class="w-8 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
					<span class="text-green-dark">Yes</span>
					<img class="w-8 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png" />
					<span class="text-yellow-dark">Lean Yes</span>
					<img class="w-8 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/orange-dot.png" />
					<span class="text-orange">Undecided</span>
					<img class="w-8 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/red-dot.png" />
					<span class="text-red">No</span>
				</div>

			</div>
		@endif

		<div class="w-1/5 text-blue mt-3 text-right">
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" />
			{{ number_format($count) }} Voters
			<br>{{ $list->static_count_doors }} Doors 
		</div>
		
		
		<!-- <i class="fa fa-info-circle text-grey hover:text-blue transition cursor-pointer pl-2"></i> -->
		<!-- <span class="text-blue">*</span> -->
	</div>

	@if(!$guest)

		<div class="w-full">

			<div id="map" wire:ignore class="w-full border-4" style="height: 400px;"></div>
			
		</div>

		<div class="h-16"></div>

	@endif

	<div class="flex">

		<div class="whitespace-no-wrap pr-2 text-blue">
			{{ number_format($door_count) }} Doors :
		</div>

		<div class="flex-shrink">



			@if(!$guest)

				<div class="cursor-pointer inline" wire:click="toggleEditMode">

					@if ($edit_mode)
						View Mode / <b>Data Entry Mode</b>
					@else
						<b>View Mode</b> / Data Entry Mode
					@endif

				</div>

			@endif

		</div>
		<div class="flex-grow text-center">

			
				<input class="font-bold border text-center py-1 w-16 text-blue-500" type="text" wire:model.debounce.1000ms="perpage" />
				Per page

				| <select wire:model="sort">
					<option value="last_name">Sort by Last Name</option>
					<option value="address">Sort by Address</option>
					<option value="address_zip">Sort by Zip</option>
				</select>

		</div>
		<div class="flex-shrink text0right">

			@if(Auth::user()->permissions->admin && !$guest)

				<div class="float-right py-1">
					@include('campaign.lists.export-button', ['list' => $list, 'title' => 'Export List'])
				</div>

				<div class="float-right text-xs mr-4 mt-1 cursor-pointer rounded-lg px-2 py-1 text-blue">
					<a href="/campaign/lists/{{ $list->id }}/edit">Edit List</a>
				</div>



			@endif

			<div class="float-right text-xs mr-4 mt-1 cursor-pointer rounded-lg px-2 py-1 text-blue">
				<a target="_blank" href="/campaign/lists/{{ $list->id }}/print">Print for Walking</a>
			</div>

		</div>
	</div>

	<div class="mt-2">
		{{ $voters->links() }}
	</div>


	@if(!$guest)
	<div class="flex mt-2 border px-4 py-2 mt-4 text-sm">

		<div class="py-1">
			<i class="fa fa-tag text-2xl text-blue-light absolute -ml-6"></i> 
			<span class="ml-2">With Tag:</span>
			<select wire:model="tag_with">
				<option value="">-- NONE --</option>
				@foreach($available_tags as $tag)
					<option value="{{ $tag->id }}">{{ $tag->name}}</option>
				@endforeach
			</select>
		</div>

		@if($tag_with)

			<div>
				<button type="button" 
						class="text-xs mt-1 cursor-pointer rounded-lg px-4 py-1 ml-2 bg-blue text-white"
					 	wire:click="tagWholeList()">
					Add Tag to All
				</button>

				<button type="button" 
						class="text-xs mt-1 cursor-pointer rounded-lg px-4 py-1 ml-1 bg-blue text-white"
					 	wire:click="tagWholeList('remove')">
					Remove Tag from All
				</button>

			</div>

		@endif

		@if($affected_count)

			<div class="ml-4 text-green font-bold text-lg">
				{{ $affected_count }} {{ Str::plural('voter', $affected_count) }} affected.
			</div>

		@endif

	</div>
	@endif

		
	<table wire:loading.class="opacity-50" class="table text-grey-dark text-sm mt-8">

		<tr>
			<th></th>
			<th colspan="">Name/Phone/Email</th>
			<th>Address</th>
			<th class="text-left">Actions</th>
			<th class="text-center">
				<div class="w-5/6">
					Support (1=YES, 5=NO)
				</div>
			</th>
		</tr>

		@foreach ($voters as $voter)


			@livewire('participant-details', 
				[
					'voter_or_participant' => $voter,
					'iteration' => $loop->iteration + ($voters->currentPage() - 1) * $perpage,
					'edit' => $edit_mode,
					'tag_with_id' => ($tag_with) ? $tag_with : request('tag_with')
				], 
				key($voter->id.'_'.$loop->iteration.'_'.Str::random(10)
			))


		@endforeach

	</table>

	<div class="p-4">
		{{ $voters->links() }}
	</div>


</div>

@include('campaign.lists.export-modal')


@push('scripts')

 	@include('campaign.lists.map')

 	@include('campaign.lists.export-modal-js')

@endpush
