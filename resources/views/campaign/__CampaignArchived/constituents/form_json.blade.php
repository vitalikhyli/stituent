<form method="POST" id="contact_form" action="/campaign/save_form_json/">

	@csrf

	<input type="text" name= "v" value="{{ $col_val }}" class="border rounded-lg p-2 font-bold shadow"/>

	<input type="hidden" name="id" value="{{ $id }}" />

	<input type="hidden" name="column" value="{{ $col }}" />

	<input type="hidden" name="json_col" value="{{ $json_col }}" />

	<input type="hidden" name="model" value="{{ $model }}" />

	<button class="bg-blue text-white text-sm rounded-full p-2">
		Save
	</button>

</form>
