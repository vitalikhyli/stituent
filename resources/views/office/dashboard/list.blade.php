<?php if (!defined('dir')) define('dir','/office'); ?>

@if($people->count() <= 0)
	<div class="text-center w-full">
		<div class="p-2 text-grey-dark">
			Search found nobody
		</div>

			<a href="{{dir}}/constituents_new/{{ $search_value }}">
				<button class="bg-blue-darker rounded-lg px-4 py-2 text-white shadow"><i class="fas fa-plus-circle"></i> Create New Person: {{ $search_value }}
			</button></a>

	</div>
@else

	
	<table class="text-xs w-full bg-white">

		@if(isset($search_value))
		<tr class="border-b-4 border-blue bg-blue">
			<td class="p-2 text-right" colspan="3">

				<a href="{{dir}}/constituents_new/{{ $search_value }}">
					<button class="bg-blue-darker rounded-lg px-2 py-1 text-white shadow text-sm"><i class="fas fa-plus-circle"></i> New Unlinked Person "{{ $search_value }}"
				</button></a>

			</td>
		</tr>
		@endif

	@foreach($people as $theperson)

		@if(($theperson->person == 1))
			<tr class="border-b hover:bg-orange-lighter cursor-pointer bg-orange-lightest">
		@else
			<tr class="border-b hover:bg-blue-lightest cursor-pointer">
		@endif

			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
					@if($theperson->entity == 1)
						<a href="{{dir}}/entities/{{ $theperson->id }}">
					@else
						<a href="{{dir}}/constituents/{{ $theperson->id }}">
					@endif
					<span class="hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">

						@if($theperson->entity == 1)
							<i class="fas fa-hotel mr-2"></i> 
						@endif

						{{ $theperson->full_name }}
					</span>
					</a>
			</td>
			<td class="p-2 w-10 whitespace-no-wrap" valign="top">
				{{ $theperson->full_address }}
			</td>
		</tr>

	@endforeach
	</table>

@endif