@foreach($categories as $cat)

		<div class="py-1 px-4 text-gray-dark">
			<select id="slim-select-category-{{ $cat->id }}" multiple="multiple" name="category_{{ $cat->id }}[]" query_form="true">
				<option data-placeholder="true"></option>
				@foreach ($cat->groups as $group)

					@if($group->archived_at)
						@continue
					@endif

					<option {{ selectedIfInArray($group->id."", $input, 'category_'.$cat->id) }} value="{{ $group->id }}">
						{{ $group->name }}
							@if($group->archived_at)
								[Archived {{ \Carbon\Carbon::parse($group->archived_at)->format("n/y") }}]
							@endif

					</option>
					
					@if($cat->has_position)


						<option {{ selectedIfInArray($group->id.'_supports', $input, 'category_'.$cat->id) }} value="{{ $group->id }}_supports">
							{{ $group->name }} (Supports)
						</option>

						<option {{ selectedIfInArray($group->id.'_opposed', $input, 'category_'.$cat->id) }} value="{{ $group->id }}_opposed">
							{{ $group->name }} (Opposed)
						</option>

					@endif

				@endforeach
			</select>
		</div>

	@endforeach




