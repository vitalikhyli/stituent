@if(!$people->first())
	<div class="text-center w-full">
		<div class="p-2 text-grey-dark">
			Search found nobody
		</div>
	</div>
@else

	
	<table class="text-xs w-full bg-white">

	@foreach($people as $theperson)

		@if($theperson->entity)
			<tr class="border-b hover:bg-blue-lightest cursor-pointer">
			
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<span data-entity_id="{{ $theperson->id }}" data-person_id="" data-name="{{ $theperson->full_name }}" class="call-search-result hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						<i class="fas fa-hotel mr-2"></i> 
						{{ $theperson->full_name }}
					</span>
				</td>
				<td class="p-2 w-10 whitespace-no-wrap overflow-x-hidden" valign="top">
					{{ $theperson->full_address }}
				</td>
			</tr>
		@elseif($theperson->person)
			<tr class="border-b hover:bg-orange-lighter cursor-pointer bg-orange-lightest">
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<span data-entity_id="" data-person_id="{{ $theperson->id }}" data-name="{{ $theperson->full_name }}" class="call-search-result bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm border shadow">
						<i class="fas fa-user-circle mr-1"></i> {{ $theperson->full_name }}
					</span>
				</td>
				<td class="p-2 w-10 whitespace-no-wrap overflow-x-hidden" valign="top">
					{{ $theperson->full_address }}
				</td>
			</tr>
		@else
			<tr class="border-b hover:bg-blue-lightest cursor-pointer">
			
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<span data-entity_id="" data-person_id="{{ $theperson->id }}" data-name="{{ $theperson->full_name }}"class="call-search-result bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
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