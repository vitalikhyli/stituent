<div>

	@if($upload->imported_count != $upload->count)


		<div class="text-3xl font-bold pb-2">
			Importing File...
		</div>

		<div>
            <div wire:poll.1000ms class="flex w-5/6 items-center">

            <div class="w-4/5 rounded-full h-4 border">
                <div class="bg-blue-light h-4 rounded-full" style="width: {{ round(($upload->imported_count/$upload->count) * 100) }}%;"></div>
            </div>
            <div class="w-1/5 pl-6 text-blue-light font-bold text-2xl">
                {{ round(($upload->imported_count/$upload->count) *100) }}%
            </div>

        </div>

	    <div class="flex">
	        @foreach(str_split(str_pad($upload->imported_count, 7,'0',STR_PAD_LEFT)) as $key => $char)
	            <div class="font-mono font-bold {{ ($key >= 7- strlen($upload->imported_count)) ? 'text-blue' : 'text-grey' }} border-t-4 border-l border-grey-dark px-2 py-2 bg-white shadow-lg">{{ $char }}</div>
	        @endforeach
	        <div class="text-lg font-bold p-2">Records</div>
	    </div>

	@else


	<div class="text-3xl font-bold border-b-4 border-blue pb-2">
		<input type="text" wire:model="upload_name" value="{{ $upload->name }}" class="border p-1" />

		<button class="opacity-25 text-sm font-normal mt-2 rounded-lg bg-red text-white px-2 py-1 float-right">
			Delete File
		</button>

	</div>
	<div class="w-full flex">
		
			<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
				<div>
					<i class="fas fa-check-circle mr-1"></i> Import
				</div>
			</div>

			<div class="cursor-pointer bg-white text-blue p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
				Match
			</div>
			<div class="cursor-pointer bg-white text-blue text-grey-darker p-2 text-center uppercase text-sm w-1/3">
				Integrate
			</div>
	</div>


		<div class="text-sm mt-2 hidden">

			<div class="flex">
				<div class="font-bold w-24">File Size</div><div>{{ round(($upload->file_size / 1000), 2) }} k</div>
			</div>
			<div class="flex">
				<div class="font-bold w-24">Count</div><div>{{ $upload->count }}</div>
			</div>
			<div class="flex">
				<div class="font-bold w-24">Uploaded</div><div>{{ \Carbon\Carbon::parse($upload->created_at)->toDateTimeString() }}</div>
			</div>
		</div>


		<div class="text-xl font-bold border-b-2 pb-1 mt-4">
			File Preview

			<button wire:click="togglePreview" class="rounded-lg bg-blue float-right text-sm font-normal text-white px-2 py-1">{{ ($show_preview) ? 'Hide' : 'Show' }}</button>

		</div>

		@if($show_preview)

			@include('shared-features.useruploads.preview', ['preview' => $preview, 'preview_count' => $preview_count])

		@endif





		<div class="text-xl font-bold border-b-2 pb-1 mt-6">
			Matching
		</div>

		<div class="bg-blue-lightest p-3 border-b text-sm">
			Certain fields can be used, by themselves or in combination, to match your data to records that already exist in Community Fluency.
		</div>

<!-- 		<button class="float-right border px-4 py-2 rounded-full my-2 bg-blue text-white shadow text-sm">
			<i class="fas fa-exclamation-circle"></i> Not Enough
		</button> -->

		@if($upload->column_matches)
		<div class="text-xl">

		<!-- <pre>{{ print_r($upload->column_matches) }}</pre> -->

			@foreach($upload->column_matches as $key => $match)

				<div class="text-sm py-2">

					<span class="text-grey-dark mr-4">{{ $key + 1 }}</span>

					<select wire:model="column_matches.{{ $key }}.user" class="{{ ($column_matches[$key]['user']) ? 'bg-red text-white' : 'opacity-50' }} text-black border">
						<option value="">-- User Upload Field--</option>
						@foreach(collect($upload->columns)->sort() as $fieldname)
							<option value="{{ $fieldname }}">{{ $fieldname }}</option>
						@endforeach
					</select>

					<span class="text-grey-dark p-4">=</span>

					<select wire:model="column_matches.{{ $key }}.db" class="{{ ($column_matches[$key]['db']) ? 'bg-blue text-white' : 'opacity-50' }} text-black border">
						<option value="">-- CF Field --</option>
						@foreach($matchable_fields as $db_field => $fieldname)
							@if(substr($db_field,0,4) == '****')
								<option disabled>-----------------</option>
							@else
								<option value="{{ $db_field }}">{{ $fieldname }}</option>
							@endif
						@endforeach
					</select>

<!-- 					<button wire:click="deleteMatch('{{ $key }}')" class="rounded-lg bg-red-lightest text-grey-dark px-2 py-1 mx-1 border font-bold">
						-
					</button>

					<button wire:click="addMatch()" class="rounded-lg bg-blue-lightest text-grey-dark px-2 py-1 mx-1 border font-bold mt-2">
						+
					</button> -->

				</div>

			@endforeach

		</div>

		@else

			<!-- <button wire:click="addMatch()" class="rounded-lg bg-blue-lightest text-grey-dark px-2 py-1 mx-1 border font-bold mt-2">
				+ Add Match
			</button> -->

		@endif

		
		<div class="mt-4">
			<button wire:click="shortcutMatches('first_name, last_name, address_city')" class="text-blue cursor-pointer mt-2 border rounded-lg px-4 py-1">Use First, Last, City</button>

			<button wire:click="shortcutMatches('first_name, last_name, address_city, primary_email')" class="text-blue cursor-pointer mt-2 border rounded-lg px-4 py-1">Use First, Last, City (OR) Primary Email</button>

			<button wire:click="shortcutMatches('id')" class="text-blue cursor-pointer mt-2 border rounded-lg px-4 py-1">Use Voter ID</button>

			<button wire:click="shortcutMatches('id, primary_email')" class="text-blue cursor-pointer mt-2 border rounded-lg px-4 py-1">Use Voter ID OR Primary Email</button>

			<button wire:click="shortcutMatches('primary_email')" class="text-blue cursor-pointer mt-2 border rounded-lg px-4 py-1">Use Primary Email</button>

		</div>


		<div class="text-center p-8">

			@if($all_matches)
			
				<a href="/{{ Auth::user()->team->app_type }}/useruploads/{{ $upload->id }}/match">
					<button class="rounded-full bg-blue text-xl text-white px-8 py-2">
						Match and Review
					</button>
				</a>

			@endif

		</div>



	@endif
</div>