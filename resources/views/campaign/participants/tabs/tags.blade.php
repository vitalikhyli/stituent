<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
	Tags
</div>

<div class="pl-2">

	@if(!$participant->tags->first())

		<span class="text-grey-dark">none</span>

	@else

		@foreach($participant->tags as $tag)
		
			<div class="uppercase text-sm text-blue pr-4 pt-2">
				
					<i class="fas fa-check-circle mr-2 text-blue"></i> {{ $tag->name }}

			</div>

		@endforeach

	@endif

</div>