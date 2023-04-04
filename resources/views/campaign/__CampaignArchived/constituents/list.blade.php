<div id="list" class="w-100">
	<div class="font-normal uppercase text-sm mb-2 float-right">
		Showing {{ number_format($people->count(),0,'.',',') }} @lang('Constituents')
	</div>
	<table class="text-sm w-full">
		<tr id="table_header" class="border-b bg-grey-lighter uppercase">
			<td class="p-2 {{ (isset($narrow) ? 'hidden' : '') }}">
				ID
			</td>
			<td class="p-2 whitespace-no-wrap">
				@lang('Voter')
			</td>
			<td class="p-2 whitespace-no-wrap">
				@lang('Voting Address')
			</td>
			<td class="p-2 whitespace-no-wrap text-xs" valign="top">
				@lang('Current Support')
			</td>
			<td class="p-2 {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				
			</td>
		</tr>
	@foreach($people->take(50) as $theperson)
	@if(($theperson->person == 1))
		<tr class="border-b hover:bg-orange-lighter cursor-pointer bg-orange-lightest">
	@else
		<tr class="border-b hover:bg-blue-lightest cursor-pointer">
	@endif
			<td class="p-2 w-10 whitespace-no-wrap {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				{{ $theperson->id }}
			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					<a href="/campaign/constituents/{{ $theperson->id }}">
					<span class="hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						{{ $theperson->full_name }}
					</span>
					</a>
			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				{{ $theperson->full_address }}
			</td>
			<td class="p-2 w-10 whitespace-no-wrap text-center" valign="top">
				@if ($theperson->support == null)
					<span class="text-grey">no data</span>
				@else
					@if(is_string($theperson->support))
						{{ SupportNumberToEnglish(json_decode($theperson->support,true)['campaign_1']) }}
					@else
						{{ SupportNumberToEnglish($theperson->support->campaign_1) }}
					@endif
				@endif
			</td>
			<td class="p-2 text-xs text-right {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				@if(($theperson->person == 1) && (isset($mode_all)))
					{{ Auth::user()->team->shortname }}
				@endif
			</td>
		</tr>

	@endforeach
	</table>
</div>