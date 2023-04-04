<div class="bg-blue-darker text-white mb-4 py-2 px-2 font-bold">
	<i class="w-6 fas fa-envelope mr-2 text-center"></i>Recipients
</div>

@if ($input)
	@foreach($input as $key => $term)
		@if(!is_array($term))
			@if (isset($term))
				<div class="p-2 border-b flex text-xs">
					<div class="w-3/5 font-semibold">{{ $key }}</div>
					<div class="text-grey-dark">{{ $key }}</div>
				</div>
			@endif
		@else
				<div class="p-2 border-b flex text-xs">
					<div class="w-3/5 font-semibold">{{ $key }}</div>
					<div class="text-grey-dark">
						@foreach($term as $item)
							{{ $item }},
						@endforeach
					</div>
				</div>

		@endif
	@endforeach
@endif

