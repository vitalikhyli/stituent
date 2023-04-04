<div class="flex">

	<div class="text-2xl pt-6 pr-4 text-grey-dark font-bold w-48">
		<div class="border-r-4">
			4. Voter File
		</div>
	</div>

	<div class="p-6 flex-grow">

		<div class="flex w-full">

			<div class="pr-2 w-1/2">

				<div class="pb-1 font-bold border-b-4">
					Slices of Master Files
				</div>

				<div class="py-2">

					@if(!$available_slices->where('standalone', false)->first())

						<div class="text-red py-1">
							None found for {{ $team->data_folder_id }}
						</div>

					@else


				        <select name="db_slice"
				                class=""
				                wire:model="db_slice" />

				            <option value="">-- NONE --</option>

				            @foreach($available_slices->where('standalone', false) as $slice_option)

				                <option value="{{ $slice_option->name }}">
				                    {{ $slice_option->name }}
				                </option>

				            @endforeach

				        </select>

				    @endif

			    </div>

			</div>

			<div class="pl-2 w-1/2">

				<div class="pb-1 font-bold border-b-4">
					Standalone Voter Files
				</div>

				<div class="py-2">

					@if(!$available_slices->where('standalone', true)->first())

						<div class="text-red py-1">
							None found for {{ $team->data_folder_id }}
						</div>

					@else

				        <select name="db_slice"
				                class=""
				                wire:model="db_slice" />

				            <option value="">-- NONE --</option>

				            @foreach($available_slices->where('standalone', true) as $slice_option)

				                <option value="{{ $slice_option->name }}">
				                    {{ $slice_option->name }}
				                </option>

				            @endforeach

				        </select>

			        @endif

			    </div>

			</div>

		</div>


		<div>

			@if($team->db_slice)

		    	<div class="mt-4 text-xl font-bold text-blue">

		    		<i class="fas fa-check-circle"></i> Voter File: {{ $team->db_slice }}

		    	</div>

		    @endif

		</div>


    </div>

</div>