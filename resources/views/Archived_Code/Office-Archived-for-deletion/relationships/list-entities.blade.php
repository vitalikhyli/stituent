<?php if (!defined('dir')) define('dir','/office'); ?>

@if($entities->count() <= 0)

	<div class="text-center">
		
		<div class="p-2 text-grey-dark">
			Nothing found.
		</div>

	</div>

@else

	
	<table class="text-xs w-full">
		@foreach($entities as $theentity)

			<tr data-theid="{{ $theentity->id }}" class="clickable-select-person cursor-pointer">
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">

					<span data-theid="{{ $theentity->id }}" data-thename="{{ $theentity->name }}" class="clickable-entity bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm  hover:bg-blue hover:text-white">
						<i class="fas fa-plus-circle mr-2 opacity-75"></i>  {{ $theentity->name }}
					</span>

				</td>
			</tr>

		@endforeach
	</table>

@endif
