<option value="{{ $thecategory->id }}"
@if(isset($selected_id))
	@if($selected_id == $thecategory->id)
		selected
	@endif
@endif
	>{{ str_repeat('--', $level) }} {{ $thecategory->shortened_name }}</option>
@foreach ($thecategory->subModels() as $thesub)

	@if(isset($selected_id))

		@include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => $level+1, 'selected_id' => $selected_id])

	@else

		@include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => $level+1])

	@endif

@endforeach