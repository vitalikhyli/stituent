<?php if (!defined('dir')) define('dir','/u'); ?>

<div id="list">

@if($people->count() <= 0)
	<div class="text-center">
		<div class="p-2 text-grey-dark">
			Search found nobody
		</div>

			<a href="{{dir}}/constituents_new/{{ $search_value }}">
				<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white shadow"><i class="fas fa-plus-circle"></i> Create New Person: {{ $search_value }}
			</button></a>

	</div>
@else

	
	<table class="text-sm w-full">

		

		<tr class="border-b bg-grey-lighter uppercase">
			<td class="p-2">
				<span class="font-bold text-xl pl-2">{{ number_format($total_count) }}</span> @lang('People')
			</td>
			<td class="p-2" colspan="2">

				@lang('Address')
				<span class="font-bold text-xl pl-2">&nbsp;</span> 

				@if(isset($search_value) && ($search_value != null))


					<a href="{{dir}}/constituents_new/{{ $search_value }}">
						<button class="bg-blue rounded-lg px-4 py-2 text-white shadow text-sm float-right"><i class="fas fa-plus-circle"></i> New Unlinked Person "{{ $search_value }}"
					</button></a>

					
				@endif
			</td>
			
		</tr>

		

	@foreach($people as $theperson)

		@if(($theperson->person))
			<tr class="border-b hover:bg-orange-lightest cursor-pointer bg-orange-lightest">
		@else
			<tr class="border-b hover:bg-blue-lightest cursor-pointer">
		@endif

			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				<a href="{{dir}}/constituents/{{ $theperson->id }}">
					<span class="{{ ($theperson->person) ? 'border border-grey shadow' : '' }} hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">
						{{ $theperson->full_name }} ({{ $theperson->age }})
					</span>
				</a>
			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				{{ $theperson->full_address }}
			</td>
			<td class="p-2 text-xs text-right {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				@if(($theperson->person == 1) && (isset($mode_all)))
					{{ Auth::user()->team->shortname }}
				@endif
			</td>
		</tr>

	@endforeach
	</table>

@endif
</div>