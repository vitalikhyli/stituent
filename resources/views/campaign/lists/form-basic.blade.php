<form action="/{{ Auth::user()->team->app_type }}/lists/" method="POST">
@csrf

	<div class="border-b-4 border-blue text-xl pb-2">

		

	</div>

	<div class="text-base py-2 px-4 bg-blue-lightest text-blue-darker mb-2 inline-block w-full rounded-b-lg shadow">

	    <input type="hidden" name="mode_basic" />

		<div class="flex items-center">
			<div class="mr-2">
				<input name="search" id="search" autocomplete="off" size="30" type="text" class="border border-grey rounded-lg p-2 text-black" value="{{ isset($_GET['search']) ? $_GET['search'] : null }}" placeholder="Quick Search" />
			</div>
			<button type="submit" class="rounded-lg bg-blue text-white px-2 py-1 my-1 border">
				Go
			</button>
			<div class="text-sm toggle_search ml-2 text-blue-dark cursor-pointer">
				Advanced Search
			</div>					
		</div>

	</div>

</form>