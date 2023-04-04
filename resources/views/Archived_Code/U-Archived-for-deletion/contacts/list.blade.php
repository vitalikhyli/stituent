<?php if (!defined('dir')) define('dir','/u'); ?>

@if($people->count() <= 0)
	<div class="text-center w-full">
		<div class="p-2 text-grey-dark">
			Search found nobody
		</div>
	</div>
@else

	
	<table class="text-xs w-full bg-white">

	@foreach($people as $theperson)

		@if(($theperson->person == 1))
			<tr class="border-b hover:bg-orange-lighter cursor-pointer bg-orange-lightest">
		@else
			<tr class="border-b hover:bg-blue-lightest cursor-pointer">
		@endif

			<td class="p-2 w-10 whitespace-no-wrap" valign="top">

				@if($theperson->entity == 1)
					<span data-entity_id="{{ $theperson->id }}" data-person_id="" data-name="{{ $theperson->full_name }}" class="lookup-search-result hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						<i class="fas fa-hotel mr-2"></i> 
						{{ $theperson->full_name }}
					</span>
				@else
					<span data-entity_id="" data-person_id="{{ $theperson->id }}" data-name="{{ $theperson->full_name }}" class="lookup-search-result hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						{{ $theperson->full_name }}
					</span>
				@endif

			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				{{ $theperson->full_address }}
			</td>
		</tr>

	@endforeach
	</table>

@endif