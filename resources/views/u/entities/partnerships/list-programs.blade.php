@if($programs->count() <= 0)

	<div class="text-center">
		
		<div class="p-2 text-grey-dark">
			Nothing found.
		</div>

	</div>

@else

	<table class="text-xs w-full">
		@foreach($programs as $theprogram)

			<tr>
				<td class="p-2 w-10 whitespace-no-wrap" valign="top">

					<span class="clickable-program bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm hover:bg-blue hover:text-white cursor-pointer" data-theprogram="{{ $theprogram }}">
						<i class="fas fa-plus-circle mr-2 opacity-75"></i> <span class="thename">{{ $theprogram }}</span>
					</span>

				</td>
			</tr>

		@endforeach
	</table>

@endif
