<table class="w-full text-sm align-top cursor-pointer">
	<tr class="bg-grey-lighter border-b">
		<td class="p-1 w-1/5">
			
		</td>
		<td class="p-1 w-64">
			Name
		</td>

		<td class="p-1 w-64">
			Sent From
		</td>

		<td class="p-1 w-32">
			Created
		</td>

		<td class="p-1 text-right">
			Recipients
		</td>

		<td class="p-1 text-right">
			Progress
		</td>

	</tr>


	@foreach($emails as $theemail)
		<tr class="clickable {{ (!$loop->last) ? 'border-b' : '' }} hover:bg-orange-lightest" data-href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/edit">

			<td class="p-2 whitespace-no-wrap">
				
				<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/edit">

					@if($theemail->queued)
	 					<button class="bg-grey-lighter px-2 py-1 text-xs rounded-lg text-grey-darkest hover:bg-blue-dark hover:text-white font-normal">
	 						<i class="fas fa-search mr-1"></i> View
	 					</button>
					@else
	 					<button class="bg-blue px-2 py-1 text-xs rounded-lg text-white hover:bg-blue-dark hover:text-white font-normal">
	 						<i class="fas fa-edit mr-1"></i> Edit
	 					</button>
	 				@endif
				</a>

				<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/copy">
					<button class="bg-grey-lighter px-2 py-1 text-xs rounded-lg text-grey-darkest hover:bg-blue-dark hover:text-white font-normal">
						<i class="fas fa-copy mr-1"></i> Copy
					</button>
				</a>
			</td>

			<td class="p-2 font-semibold text-black">
				{{ $theemail->name }}
			</td>

			<td class="p-2 font-normal">
				{{ $theemail->sent_from }}
			</td>

			<td class="p-2 text-grey-dark text-xs">
				{{ \Carbon\Carbon::parse($theemail->created_at)->format('n/j/y g:i A') }}
			</td>

			<td class="p-2 text-right">
				@if($theemail->expected_count)
					{{ $theemail->expected_count }}
				@else
					<span class="text-grey-dark">
						{{ $theemail->queuedRecipients()->count() }}
					</span>
				@endif
			</td>

			<td class="p-2 text-grey-dark text-right text-xs">

				{{ number_format($theemail->queuedAndSentCount()) }} / {{ number_format($theemail->queuedCount()) }}

			</td>

		</tr>
	@endforeach
</table>