<div>

	<div class="border-2 p-2 bg-blue-darker text-white shadow">

		<div class="pl-2 text-xl">
			New:
		</div>

		<div class="flex">

			<div class="pl-2 py-2 flex">
				<div class="">
					<input type="text"
						   wire:model="new_group"
						   class="border-2 bg-white p-2 text-black text-sm font-bold w-48"
						   placeholder="Group (Optional)" />
				</div>
			</div>

			<div class="p-2">
				<input type="text"
					   wire:model="new_name"
					   class="border-2 bg-white p-2 text-black font-bold "
					   placeholder="Name" />
			</div>

			<div class="p-2 pt-3 text-black">
				<select wire:model="new_type"
						class="text-xl">
					<option value="">-- Type --</option>
					@foreach(['canvass', 'phonebank', 'matrix'] as $type)
						<option value="{{ $type }}">{{ ucwords($type) }}</option>
					@endforeach

				</select>
			</div>

			<div class="p-2">
				<input type="text"
					   wire:model="new_starts_at"
					   class="border-2 bg-white p-2 text-blue font-bold w-24" />
			</div>

			<div class="p-2">
				to &nbsp;
				<input type="text"
					   wire:model="new_ends_at"
					   class="border-2 bg-white p-2 text-blue font-bold w-24"
					   placeholder="{{ \Carbon\Carbon::today()->format('n/j/y') }}" />
			</div>

			<div class="p-2 pt-3">

				@if($new_name && $new_type)

					<button wire:click="addNew()"
							class="rounded-lg bg-blue text-sm text-white px-4 py-1 text-xl">
						Add
					</button>

				@endif

			</div>

		</div>

		<div class="px-2 text-sm text-grey-lighter">
			* Use Groups to organize multiple opportunities under one header, such as by date ("5/29 Weekend") or project ("Boston Volunteer Events").
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

		@foreach($opps as $group => $grouped_opps)

			<div class="table-row">
				<div class="table-cell font-bold border-b-4 border-blue py-1 pt-4 text-sm uppercase text-blue">
					@if($group)
						{{ $group }}
					@else
						--
					@endif
				</div>
				<div class="table-cell font-bold border-b-4 border-blue"></div>
				<div class="table-cell font-bold border-b-4 border-blue"></div>
				<div class="table-cell font-bold border-b-4 border-blue"></div>
				<div class="table-cell font-bold border-b-4 border-blue"></div>
				<div class="table-cell font-bold border-b-4 border-blue text-right">
					
					@if($group)
						<button class="rounded-lg bg-blue text-xs text-white px-2 py-1 font-normal"
								wire:click="$set('new_group','{{ $group }}')">
							Use This Group
						</button>
					@endif

				</div>
			</div>

		    @foreach($grouped_opps as $opp)

				<div class="table-row cursor-pointer hover:bg-orange-lightest">

					<div class="table-cell border-b border-dashed pr-2 py-1 uppercase text-sm text-grey-dark">
						{{ $opp->type }}
					</div>

					<div class="table-cell border-b border-dashed px-2 py-1 text-right">
						@if($opp->invited->first())
							{{ $opp->invited->count() }}
						@endif
					</div>

					<div class="table-cell border-b border-dashed px-2 py-1 text-blue">
						<a href="/campaign/opportunities/{{ $opp->type }}/{{ $opp->id }}">
							{{ $opp->name }}
						</a>
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

		@endforeach

	</div>

</div>
	