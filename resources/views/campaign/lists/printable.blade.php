@extends('print')

@section('main')

	<div id="map" class="w-full border-2 border-t-0" style="height: 600px;"></div>

	<table class="w-full">
		@foreach ($households as $household_id => $household)

			<tr class="border-b-2">
				<td colspan="100" class="pt-8 align-top p-2 font-bold uppercase text-lg">
					{{ $household->first()->address_line_street }}
				</td>
			</tr>
			@foreach ($household as $voter)
			
				<tr class="border-b">
					<td class="align-top p-2 pl-16">
						<div class="rounded-full w-8 h-8 pt-1 font-bold border-black border-2 items-center text-center">
							{{ $voter->support }}
						</div>
					</td>
					<td class="align-top p-2">{{ $voter->name }} ({{ $voter->age }})</td>
					
					<td class="align-top p-2 whitespace-no-wrap">{{ $voter->readable_phone }}</td>
					<td class="align-top p-2">
						@if ($voter->tags())
							@if ($voter->tags->count() > 0)
								<div class="mb-1">
									@foreach ($voter->tags as $tag)
										<span class="bg-gray-500 text-white p-1 m-1">
											{{ $tag->name }}
										</span>
									@endforeach
								</div>

							@endif
						@endif

						<table>
							@foreach ($voter->actions as $action)
								<tr>
									<td class="px-1 align-top">{{ $action->created_at->format('n/j/y') }}</td>
									<td class="px-1 align-top whitespace-no-wrap"><b>{{ $action->name }}</b></td>
									<td class="px-1 align-top">{{ $action->details }}</td>
								</tr>
							@endforeach
						</table>
					</td>
					<td class="align-top p-2 border-l">{{ $voter->campaign_notes }}</td>
					<td class=" w-1/3"></td>
				</tr>
			@endforeach

		@endforeach
	</table>

@endsection

@section('javascript')


 	@include('campaign.lists.map')

 	@include('campaign.lists.export-modal-js')
 				
@endsection