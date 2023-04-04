<div class="table w-full">
	@foreach($files as $thefile)

		@include('shared-features.files.one-file', compact($thefile))

	@endforeach
</div>