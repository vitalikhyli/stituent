<div>

	<div class="border-2 p-2 bg-blue-darker text-white flex shadow">

		<div class="p-2 pt-3 text-xl">
			New:
		</div>

		<div class="p-2">
			<input type="text"
				   wire:model="new_name"
				   class="border-2 bg-white p-2 text-blue font-bold" />
		</div>

		<div class="p-2 pt-3 text-black">
			<select wire:model="new_type"
					class="text-xl">
				@foreach(['canvass', 'phonebank', 'standout'] as $type)
					<option value="{{ $type }}">{{ ucwords($type) }}</option>
				@endforeach

			</select>
		</div>

		<div class="p-2">
			<input type="text"
				   wire:model="new_starts_at"
				   class="border-2 bg-white p-2 text-blue font-bold w-24"
				   placeholder="{{ \Carbon\Carbon::today()->format('n/j/y') }}" />
		</div>

		<div class="p-2">
			to &nbsp;
			<input type="text"
				   wire:model="new_ends_at"
				   class="border-2 bg-white p-2 text-blue font-bold w-24"
				   placeholder="{{ \Carbon\Carbon::today()->format('n/j/y') }}" />
		</div>

		<div class="p-2 pt-3">
			<button wire:click="addNew()"
					class="rounded-lg bg-blue text-sm text-white px-4 py-1 text-xl
					@if(!$new_name)
						opacity-50
					@endif
					"
					@if(!$new_name)
						disabled
					@endif
					>
				Add
			</button>
		</div>

	</div>

	<div class="p-2 pt-4 flex flex-wrap">
		@foreach($types as $type)
			<button wire:click="filterType('{{ $type }}')"
					class="rounded-lg text-sm text-white px-2 py-1 mr-2 uppercase
						    @if($filter == $type)
						   		bg-blue-darker
						   	@else
						   		bg-grey
						   	@endif">
				{{ $type }}
			</button>
		@endforeach
	</div>

	@if($selected)

		<div class="border-4 border-blue mt-4">

			<div class="text-2xl bg-blue text-white p-4">

				{{ $selected->name }}

			</div>

			<div class="p-4">

				<div class="flex">

					<div class="pr-4 w-1/3">

						<div class="font-bold">
							Invited
						</div>

						@foreach($selected->invited as $volunteer)

							<div class="cursor-pointer px-2 py-1 bg-grey-lightest border rounded-lg mt-1">
								{{ $volunteer->email }}

								<span class="float-right text-red">
									X
								</span>

							</div>

						@endforeach

					</div>


					<div class="pl-4 w-2/3">

						<div class="font-bold">
							Volunteer List
						</div>

						@foreach($volunteer_options as $volunteer)

							<div class="cursor-pointer">
								{{ $volunteer->email }}
							</div>

						@endforeach

					</div>

				</div>

			</div>


		</div>


	@endif

	<div class="table mt-4">

		<div class="table-row bg-grey-lighter">

			<div class="table-cell border-b-2 px-2 py-1 uppercase text-sm w-32">
				Type
			</div>

			<div class="table-cell border-b-2 px-2 py-1 text-right w-12">
				#
			</div>

			<div class="table-cell border-b-2 px-2 py-1">
				Name
			</div>

			<div class="table-cell border-b-2 px-2 py-1">
				Starts
			</div>

			<div class="table-cell border-b-2 px-2 py-1">
				Ends
			</div>

			<div class="table-cell border-b-2 px-2 py-1">
				List
			</div>

		</div>


	    @foreach($opps as $opp)

			<div class="table-row cursor-pointer hover:bg-orange-lightest"
				 wire:click="selectOpportunity({{ $opp->id }})">

				<div class="table-cell border-b border-dashed px-2 py-1 uppercase text-sm">
					{{ $opp->type }}
				</div>

				<div class="table-cell border-b border-dashed px-2 py-1 text-right">
					@if($opp->invited->first())
						{{ $opp->invited->count() }}
					@else
						-
					@endif
				</div>

				<div class="table-cell border-b border-dashed px-2 py-1 text-blue">
					{{ $opp->name }}
				</div>

				<div class="table-cell border-b border-dashed px-2 py-1">
					@if($opp->starts_at)
						{{ $opp->starts_at->format('n/j/y') }}
					@endif
				</div>

				<div class="table-cell border-b border-dashed px-2 py-1">
					@if($opp->ends_at)
						{{ $opp->ends_at->format('n/j/y') }}
					@endif
				</div>

				<div class="table-cell border-b border-dashed px-2 py-1">
					@if($opp->list)
						"{{ $opp->list->name }}"
						({{ $opp->list->static_count_doors }} doors)
					@else
						-
					@endif
				</div>

			</div>

		@endforeach

	</div>

</div>
	