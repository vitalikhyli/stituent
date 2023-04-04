

@if($people->count() <= 0)
	<div class="text-center">
		
		<div class="p-2 text-grey-dark">
			Search found nobody
		</div>

	</div>
@else

	
	<table class="text-xs w-full">

		@if(isset($search_value))
		<tr class="border-b-4 border-blue bg-blue-darker">
			<td class="p-2 text-right" colspan="4">

				<div class="float-left text-white text-base p-2">
					Link this case to...
				</div>

			</td>
		</tr>
		@endif

	@foreach($people as $theperson)

		@if($theperson->person)
			<tr data-theid="{{ $theperson->id }}" class="clickable-select-person border-b hover:bg-orange-lighter cursor-pointer bg-orange-lightest">
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<span class="bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm border shadow">
						<i class="fas fa-user-circle mr-1"></i> {{ $theperson->full_name }}
					</span>
				</td>
				<td class="p-2 w-10 whitespace-no-wrap overflow-x-hidden" valign="top">
					{{ $theperson->full_address }}
				</td>
			</tr>
		@else
			<tr data-theid="{{ $theperson->id }}" class="clickable-select-person border-b hover:bg-blue-lightest cursor-pointer">
			
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<span class="bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						{{ $theperson->full_name }}
					</span>
				</td>
				<td class="p-2 w-10 whitespace-no-wrap overflow-x-hidden" valign="top">
					{{ $theperson->full_address }}
				</td>
			</tr>
		@endif

	@endforeach
	</table>

@endif