<div class="py-2 font-bold text-lg text-blue border-b-4 border-blue mb-2 inline-block">
	Create New:
</div>

<div class="text-center flex pl-8">

	<div class="py-2 text-left flow-grow">
		<div>
			State
		</div>
		<div class="py-2">
		    <select wire:model="state" name="state">
		        <option value="">-- CHOOSE A STATE --</option>
		        @foreach($available_states as $state_option)
		            <option value="{{ $state_option }}">
		                ({{ $state_option }}) x_voters_{{ $state_option }}_master
		            </option>
		        @endforeach
		    </select>
		</div>
	</div>

    @if($state)

        <div class="text-left border-l-4 pl-2 ml-4">

        	<div>
            	<input wire:model="municipality_lookup" id="municipality_lookup" type="text" placeholder="Type to Lookup" class="p-2 border rounded" />
            </div>

            <div class="py-2">
	            <select wire:model="municipality_id" name="municipality_id" placeholder="Municipality" class="p-1 border rounded-lg w-64 h-8 {{ ($municipality_lookup) ? 'font-bold' : '' }}">
	                <option value="">- Select Municipality -</option>
	                @foreach ($municipalities as $city)
	                    <option value="{{ $city['id'] }}">
	                        {{ $city['name'] }}
	                    </option>
	                @endforeach
	            </select>
	        </div>

        </div>

    @endif

   	<div class="pl-4">

	   	@if($municipality)

			<button wire:click="storeModel()" class="bg-blue text-white px-4 py-2 border-2 border-blue hover:bg-blue cursor-pointer">Create <span class="font-bold">"{{ \Carbon\Carbon::now()->toDateString() }} - {{ $municipality->name }}"</span></button>

		@endif

	</div>


</div>

<div class="py-2 font-bold text-lg text-blue border-b-4 border-blue mb-2 inline-block">
	OR Select Existing:
</div>

<div class="text-left pl-8 py-2">

	<select class="rounded-lg px-4 py-2 border-2"
			wire:model="selected_model">
			<option value="">-- Choose --</option>
			@foreach($model_options as $options)
				<option value="{{ $options->id }}">
					{{ $options->created_at->toDateString() }} - {{ $options->name }}
				</option>
			@endforeach
	</select>

</div>