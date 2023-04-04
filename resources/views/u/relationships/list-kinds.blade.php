<?php if (!defined('dir')) define('dir','/u'); ?>

@if($kinds->count() <= 0)

	<div class="text-center">
		
		<div class="p-2 text-grey-dark">
			Nothing found.
		</div>

	</div>

@else

	<table class="text-xs w-full">
		@foreach($kinds as $thekind)

			<tr data-theid="{{ $thekind->id }}"  data-thekind="{{ $thekind->kind }}" class="clickable-select-person cursor-pointer">
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">

					<span class="clickable-kind bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm hover:bg-blue hover:text-white">
						<i class="fas fa-plus-circle mr-2 opacity-75"></i> <span class="thename">{{ $thekind->kind }}</span>
					</span>

				</td>
			</tr>

		@endforeach
	</table>

@endif
