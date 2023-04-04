<div class="border-b-4 text-xl pb-1 mt-4 ">

	<div class="float-right text-sm text-grey-dark pt-1">
		Total IDs: {{ number_format($support_sum) }}
	</div>

	<b>Support</b> Stats
</div>

@if($support_max > 0)

	<table class="w-full">
		<tr class="cursor-pointer">
			<td class="p-2 pr-3 text-right uppercase text-sm text-grey-dark whitespace-no-wrap">Yes</td>
			<td class="flex-shrink text-xs uppercase pr-2">
				<a href="/campaign/voters?filter_by_support=%3D+1">View</a>
			</td>
			<td class="py-2 w-3/4">
				@if($support[1] > 0)
					<div style="width:{{ $support[1]/$support_max*100 }}%" class="graphbar-horizontal bg-green-dark text-white text-xs py-1 px-2 rounded-r shadow text-right">
						{{ $support[1] }}
					</div>
				@endif
			</td>				
		</tr>
		<tr class="cursor-pointer">
			<td class="p-2 pr-3 text-right uppercase text-sm text-grey-dark whitespace-no-wrap">Lean Yes</td>
			<td class="flex-shrink text-xs uppercase pr-2">
				<a href="/campaign/voters?filter_by_support=%3D+2">View</a>
			</td>
			<td class="py-2 w-3/4">
				@if($support[2] > 0)
					<div style="width:{{ $support[2]/$support_max*100 }}%" class="graphbar-horizontal bg-yellow-dark text-white text-xs py-1 px-2 rounded-r shadow text-right">
						{{ $support[2] }}
					</div>
				@endif
			</td>
		</tr>
		<tr class="cursor-pointer">
			<td class="p-2 pr-3 text-right uppercase text-sm text-grey-dark whitespace-no-wrap">Undecided</td>
			<td class="flex-shrink text-xs uppercase pr-2">
				<a href="/campaign/voters?filter_by_support=%3D+3">View</a>
			</td>
			<td class="py-2 w-3/4">
				@if($support[3] > 0)
					<div style="width:{{ $support[3]/$support_max*100 }}%" class="graphbar-horizontal bg-orange text-white text-xs py-1 px-2 rounded-r shadow text-right">
						{{ $support[3] }}
					</div>
				@endif
			</td>
		</tr>
		<tr class="cursor-pointer">
			<td class="p-2 pr-3 text-right uppercase text-sm text-grey-dark whitespace-no-wrap">Lean No</td>
			<td class="flex-shrink text-xs uppercase pr-2">
				<a href="/campaign/voters?filter_by_support=%3D+4">View</a>
			</td>
			<td class="py-2 w-3/4">
				@if($support[4] > 0)
					<div style="width:{{ $support[4]/$support_max*100 }}%" class="graphbar-horizontal bg-red text-white text-xs py-1 px-2 rounded-r shadow text-right">
						{{ $support[4] }}
					</div>
				@endif
			</td>					
		</tr>
		<tr class="cursor-pointer">
			<td class="p-2 pr-3 text-right uppercase text-sm text-grey-dark whitespace-no-wrap">No</td>
			<td class="flex-shrink text-xs uppercase pr-2">
				<a href="/campaign/voters?filter_by_support=%3D+5">View</a>
			</td>
			<td class="py-2 w-3/4">
				@if($support[5] > 0)
					<div style="width:{{ $support[5]/$support_max*100 }}%" class="graphbar-horizontal bg-red-dark text-white text-xs py-1 px-2 rounded-r shadow text-right">
						{{ $support[5] }}
					</div>
				@endif
			</td>
		</tr>
	</table>

@else

	<div class="text-grey-dark p-2">
		No support data yet
	</div>

@endif