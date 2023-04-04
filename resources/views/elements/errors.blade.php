@if ($errors->any())
<div class="bg-red rounded-lg text-white m-4 shadow-lg">
	<div class="mb-4 text-lg pt-4 pl-6 border-b-2 border-red-light pb-2 uppercase">Some problems here...</div>
	<div class="p-3">
	<ul>
	@foreach ($errors->all() as $the_error)
			<li class="mb-4">{{ $the_error }}</li>
	@endforeach
	</ul>
	</div>
</div>
@endif