<div id="list">
	<div class="mt-4 w-full">
		<div class="font-normal uppercase text-sm mb-2 float-right">
			{{ number_format($households->count(),0,'.',',') }} Households
		</div>
		<table class="text-sm w-full">
		<tr class="border-b bg-grey-lighter">
			<td class="p-2">Household ID</td>
			<td class="p-2 whitespace-no-wrap"># Residents</td>
			<!-- <td class="p-2">JSON</td> -->
			<td class="p-2">Residents</td>
			<td class="p-2"></td>
		</tr>
		@foreach ($households as $thehousehold)
		@if($thehousehold->internal)
			<tr class="border-b hover:bg-blue-light hover:text-white cursor-pointer bg-orange-lightest">
		@else
			<tr class="border-b hover:bg-blue-light hover:text-white cursor-pointer">
		@endif
				<td class="p-2 whitespace-no-wrap align-top">
					{{ $thehousehold->full_address }}
				</td>
				<td class="p-2 align-top">
					{{ $thehousehold->residents_count }}
				</td>
				<td class="p-2 break-words align-top" style="word-break: break-word;">
					<?php
						$residents = json_decode($thehousehold->residents, true);
						//Remove Duplicates (People/Voters with same voter_id)
						$residents = collect($residents)->filter(function($item) {
							$person = \App\Person::where('voter_id', $item)
												 ->where('team_id', Auth::user()->team->id);
						    if ($person->first() == null) { return true; }
						});
					?>
					
					<div class="flex flex-wrap">
					@foreach($residents as $theresident_id)
						@if(!is_numeric($theresident_id))
							<a href="/campaign/constituents/{{ $theresident_id }}">
							<div class="flex-1 flex-initial m-1 bg-grey-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-base">
								<i class="fas fa-unlink text-sm mr-2"></i>
								{{ \App\Voter::where('id',$theresident_id)->first()->full_name }}

							</div>
							</a>
						@else
							<a href="/campaign/constituents/{{ $theresident_id }}">
							<div class="flex-1 flex-initial m-1 bg-orange-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-base">
								<i class="fas fa-user-circle text-sm mr-2"></i>
								{{ \App\Person::where('id',$theresident_id)->first()->full_name }}
							</div>
							</a>
						@endif
					@endforeach
					</div>

				<!-- 	<span class="text-xs text-grey-darker">
					{{ $thehousehold->residents }}
					</span> -->

				</td>
				<td class="p-2 break-words align-top" style="word-break: break-word;">
					{{ $thehousehold->voter_residents }}
				</td>
				<td>
				</td>
			</tr>
		@endforeach
		</table>
	</div>
</div>