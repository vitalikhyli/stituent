<div wire:loading.class="opacity-50" class="transition-opacity">

    <div class="p-1 flex">

    	@if($label)
			<div class="font-bold text-xs uppercase w-48 pt-2">
				{{ $label }}
			</div>
		@endif


	<div class="">

		<div class="">

			<select class="w-64 p-1 border-2"
					id="{{ $field }}_selector"
					name="{{ $field }}_selector"
					wire:model="new_selected_option"
					wire:change="selectNewOption()">

				<option value="">-- SELECT --</option>
			 	@foreach (collect($allOptions)->sort()->toArray() as $id => $name)

			 		<option value="{{ $id }}"
			 				@if(collect($selectedOptions)->contains($id))
			 					disabled
			 				@endif
			 				>{{ $name }}</option>

				@endforeach		

			</select>

			@if($anyAll)
				<select name="{{ $query_mode_name }}"
						wire:model="query_mode">
					<option value="and">ALL</option>						
					<option value="or">any</option>
				</select>
			@endif

		</div>

		@if(!empty($selectedOptions))

			<input type="hidden" id="{{ $field }}_first_id" value="{{ array_slice($selectedOptions, 0, 1)[0] }}" />
		
		@endif

		<div class="">
		 	@foreach ($allOptions as $id => $name)

				<div class="m-1 {{ (!collect($selectedOptions)->contains($id)) ? 'hidden' : '' }}">
				<!-- <div> -->

				 	<label for="{{ $field }}_option_{{ $id }}">

					    <input type="checkbox" 
					    	   id="{{ $field }}_option_{{ $id }}"
					    	   name="{{ $field }}[]" 
					    	   wire:model="selectedOptions"
					    	   value="{{ $id }}"
					    	   wire:key="{{ $field }}_option_{{ $id }}"
					    	   {{ (collect($selectedOptions)->contains($id)) ? 'checked' : '' }}
					    	   />

						<span>{{ $allOptions[$id] }}</span>
				    	
					</label>
				</div>

			@endforeach
		</div>

		@if(1 == 2)
			<pre>
				@php
					print_r($selectedOptions)
				@endphp
			</pre>

			<pre class="hidden">
				@php
					print_r($allOptions)
				@endphp
			</pre>
		@endif

	</div>

	</div>
</div>
