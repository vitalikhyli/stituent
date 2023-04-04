	<div class="mt-4">

		<div class="text-xl font-bold">
			Voter List

			@if($opp->list_id)
				<i class="fas fa-check-circle text-blue ml-1 text-base"></i>
			@endif

		</div>

		<div class="flex mt-2 pl-8 border-l-4">

			<div class="flex-shrink pr-2">

				<input type="text"
					   class="border-2 bg-white p-2"
					   placeholder="Filter Lists"
					   wire:model="filter_lists"
					   id="filter_lists" />

			</div>

			<div class="flex-grow overflow-y-scroll h-48 border-b border-dashed text-sm">

				@foreach($lists as $list)

					<div class="px-2 py-1 flex cursor-pointer
								@if($list->selected)
									bg-blue-dark
									text-white
								@else
									hover:bg-orange-lighter 
								@endif
								"
						 wire:click="selectList({{ $list->id }})">

						<div class="truncate w-6 py-1">
							@if($list->selected)
						 		<i class="fas fa-check-circle text-white"></i>
						 	@endif
						</div>

						<div class="truncate w-24 py-1 text-grey">
							{{ $list->created_at->toDateString() }}
						</div>

						<div class="truncate w-1/2 py-1 font-semibold">
							{{ $list->name }}
						</div>

						<div class="truncate w-1/3 py-1 text-grey">
							{{ number_format($list->static_count_doors) }} doors
						</div>

					</div>

				@endforeach

			</div>
		
		</div>

	</div>