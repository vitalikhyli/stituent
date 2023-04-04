

	<div id="recipients-list">
		<div class="text-lg font-bold text-blue mt-8 border-b-2 border-grey pl-2">
			{{ $people->count() }} Recipients
			@if ($missing_emails_count)
				<div class="font-normal float-right text-sm text-red p-1 pr-2">
					({{ $missing_emails_count }} more would have been included if they had emails.)
				</div>
			@endif
		</div>

		<div class="w-full pt-3 px-3 text-xs flex flex-wrap text-grey-dark">
			@foreach ($people as $person)
				@if (!$person->email)
					<div class="w-1/2 text-red font-bold whitespace-no-wrap">
				@else
					<div class="w-1/2 whitespace-no-wrap">
				@endif

					{{ $loop->iteration }}.
					{{ $person->title }}
					{{ $person->name }}
					
				</div>
				<div class="w-1/2">
					{{ $person->email }}
				</div>
			@endforeach
		</div>
	</div>
