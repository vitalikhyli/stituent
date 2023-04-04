<!-- Specific include for this component -->

@if ($visible_chunk)

<div class="flex w-full">

	<div class="whitespace-nowrap text-right mt-2 pr-2 flex-grow">

		@if($list)

			<span class="text-blue-dark text-base hover:underline">
				<a href="/campaign/lists/{{ $list->id }}">
					<i class="fas fa-clipboard-list"></i>
					List: {{ $list->name }}
				</a>
			</span>

		@else

			<button class="rounded-lg bg-blue-dark text-white px-3 py-1 text-sm font-normal"
					wire:click="storeList()">
				Create a List
			</button>

		@endif

	</div>

	@if($textarea)

		<div class="text-right mt-2 flex-shrink">

			<button class="rounded-lg bg-blue-light text-white px-3 py-1 text-sm font-normal"
					wire:click="reduceColumns()">
				Reduce Columns to What is Needed
			</button>

		</div>

	@endif

</div>

<div class="flex w-full">

	<div class="w-48 p-2 uppercase text-sm text-blue-300 font-bold">
		Extra Data
	</div>

	<div class="m-2 w-3/4">

		<label for="add_emails" class="mr-4">
			<input type="checkbox"
				   wire:model="add_emails"
				   id="add_emails"
				   class="mr-2" />
				   Add Emails
		</label>

		<label for="add_phones" class="mr-4">
			<input type="checkbox"
				   wire:model="add_phones"
				   id="add_phones"
				   class="mr-2" />
				   Add Phones
		</label>

		

	</div>
	
</div>

<div class="flex w-full">

	<div class="w-48 p-2 uppercase text-sm text-blue-300 font-bold">
		Tags
	</div>

	<div class="m-2 w-3/4">

		<div>

			<label for="tag_mode_add" class="mr-4">
				<input type="radio"
					   wire:model="tag_mode"
					   id="tag_mode_add"
					   name="tag_mode"
					   value="add"
					   class="mr-2" />
					   Add Tag
			</label>

			<label for="tag_mode_remove" class="mr-4">
				<input type="radio"
					   wire:model="tag_mode"
					   id="tag_mode_remove"
					   name="tag_mode"
					   value="remove"
					   class="mr-2" />
					   Remove Tag
			</label>

			<label for="tag_mode_default" class="mr-4">
				<input type="radio"
					   wire:model="tag_mode"
					   id="tag_mode_default"
					   name="tag_mode"
					   value="none"
					   class="mr-2" />
					   No Tags
			</label>

			@if($tag_mode != 'none')

				<div>

					<select class="rounded-lg px-4 py-2"
							wire:model="selected_tag">
							<option value="">-- Choose --</option>
						@foreach($tag_options as $tag)
							<option value="{{ $tag->id }}">{{ $tag->name }}</option>
						@endforeach
					</select>

				</div>

				@if($selected_tag)

					<div>
						# Tagged in Your DB: {{ number_format($tag_count) }}
					</div>

				@endif

			@endif 

		</div>

	</div>

</div>

@endif