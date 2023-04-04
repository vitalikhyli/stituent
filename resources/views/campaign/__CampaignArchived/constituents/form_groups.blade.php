<div id="{{ $form_id }}" class="bg-orange-lighter rounded-lg m-2 border p-4">

@if($groups->count() <= 0)
	Nothing to add.
@else
	<form method="POST" id="contact_form" action="/campaign/save_form_groups/">

		@csrf

		<input type="hidden" name="person_id" value="{{ $person_id }}" />

		<input type="hidden" name="category_id" value="{{ $category_id }}" />

		@foreach($groups as $thegroup)
			<button name="group_id" value="{{ $thegroup->id }}" class="m-1 shadow bg-white text-black rounded-lg hover:bg-blue border hover:text-white px-2 py-1">{{ $thegroup->name }}</button>
		@endforeach

	</form>

@endif
</div>
