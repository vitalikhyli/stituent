<div class="text-center">
	<input type="text"
		   class="font-bold p-2 w-1/3 border-2"
		   placeholder="Give it a name"
		   wire:model="model_new_name"
		   wire:keydown.enter="storeModel()" />

	<button @if (!$model_new_name)
				disabled
			@endif
			wire:click="storeModel()" class="bg-blue text-white px-4 py-2 border-2 border-blue hover:bg-blue cursor-pointer">Create New</button>

	<div class="p-4">
		~Or~ Select Existing:
	</div>
	<div>

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

</div>