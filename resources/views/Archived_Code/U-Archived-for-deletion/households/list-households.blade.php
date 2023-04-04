<?php if (!defined('dir')) define('dir','/u'); ?>

<div id="list">
	<div class="mt-4 w-full">
		<div class="font-normal uppercase text-sm mb-2 float-right">
			{{ number_format($households->count(),0,'.',',') }} Households
		</div>
		<table class="text-sm w-full">
		<tr class="border-b bg-grey-lighter">
			<td class="p-2">Household</td>
			<td class="p-2">Residents</td>
			<td class="p-2">Cases</td>
		</tr>
		@foreach ($households as $thehousehold)

			<tr class="border-b cursor-pointer {{ (!$thehousehold->external) ? 'bg-orange-lightest' : '' }}">

				<td class="p-2 whitespace-no-wrap align-top">
					
					<a href="{{dir}}/households/{{ $thehousehold->id }}">
						<div class="inline-block bg-grey-lighter hover:bg-blue hover:text-white rounded-full mr-2 px-2 py-1 flex-initial cursor-pointer {{ (!$thehousehold->external) ? 'border border-grey shadow' : '' }}">
							<i class="fas fa-home text-base mx-2"></i>
							{{ $thehousehold->full_address }}
						</div>
					</a>

				</td>

				<td class="p-2 break-words align-top" style="word-break: break-word;">

					<span class="text-xs text-grey-darker">
						<span class="mr-2">
							({{ $thehousehold->total_residents }}) 
						</span>
						@for ($i = 0; $i < $thehousehold->total_residents; $i++)
						    <i class="fas fa-user"></i>
						@endfor
					</span>

				</td>

				<td class="p-2">
					@if(!$thehousehold->external)
						<?php
							$cases_count = \App\Household::find($thehousehold->id)->cases->count();
						?>
						@if($cases_count > 0)
							<span class="mr-2">
								({{ $cases_count }}) 
							</span>
							@for ($i = 0; $i < $cases_count; $i++)
							    <i class="fas fa-folder "></i>
							@endfor
						@endif
					@endif
				</td>
			</tr>
		@endforeach
		</table>
	</div>
</div>