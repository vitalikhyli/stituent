<table class="w-full text-sm align-top cursor-pointer">
	<tr class="bg-red-lightest border-b">
		<td class="p-1 w-1/5">
			
		</td>
		<td class="p-1 w-64">
			Name
		</td>
		<td class="p-1 w-1/6">
			
		</td>
	</tr>


	@foreach($emails as $theemail)
		<tr class="clickable {{ (!$loop->last) ? 'border-b' : '' }} hover:bg-orange-lightest {{ ($theemail->queued) ? 'opacity-50' : 'font-semibold' }}" data-href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/edit">

			<td class="p-2 whitespace-no-wrap">
				
				<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/edit">
 					<button class="bg-red-lightest px-2 py-1 text-xs rounded-lg text-grey-darkest hover:bg-blue-dark hover:text-white font-normal">
 						@if($theemail->queued)
 							<i class="fas fa-search mr-1"></i> View
 						@else
 							<i class="fas fa-edit mr-1"></i> Edit
 						@endif
 					</button>
				</a>


			</td>

			<td class="p-2 font-semibold text-red-dark">
				{{ $theemail->name }}
			</td>

			<td class="p-2 font-semibold text-red-dark text-right w-1/6">
				<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $theemail->id }}/copy">
					<button class="bg-red-lightest px-3 py-1 text-xs rounded-lg text-grey-darkest hover:bg-blue-dark hover:text-white font-normal ml-2 whitespace-no-wrap">
						<i class="fas fa-copy mr-1"></i> Use a copy of this
					</button>
				</a>
			</td>


			</tr>
	@endforeach
</table>