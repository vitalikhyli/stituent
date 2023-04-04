<div id="constituents-list" form="{{ base64_encode(serialize($input)) }}">

@if($total_count <= 0)
	<div class="text-center">

		@isset($search_value)
			<div class="p-2 text-grey-dark">
				Search found nobody
			</div>
			<a href="/{{ Auth::user()->team->app_type }}/constituents/new/{{ $search_value }}">
				<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white shadow"><i class="fas fa-plus-circle"></i> Create New @lang('Constituent'): 
					
						{{ $search_value }}

			</button></a>
		@else
			<a href="/{{ Auth::user()->team->app_type }}/constituents/new">
				<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white shadow">
					<i class="fas fa-plus-circle"></i> 
					Create New @lang('Constituent')
				</button>
			</a>
		@endisset

	</div>
@else
	
	<div class="p-2 bg-blue-lighter flex text-right w-full">

		<div class="mx-1 text-left flex-grow">
			@if($total_count_people)
				<span class="text-blue text-xs">{{ number_format($total_count_people) }} linked </span> 
			@endif
			@if($total_count_people && $total_count_voters)
				<span class="text-blue text-xs">+</span>
			@endif
			@if($total_count_voters)
				<span class="text-blue text-xs">{{ number_format($total_count_voters) }} others </span> 
			@endif
		</div>

		<div class="mx-1 text-right">

			@if(Auth::user()->permissions->developer)

				<a href="/{{ Auth::user()->team->app_type }}/emails/master?{{ http_build_query(request()->all()) }}" class="text-grey-lightest rounded-full bg-blue hover:text-white px-4 py-1 hover:bg-blue text-xs uppercase">
					<i class="fa fa-envelope"></i> Master Email List
				</a>

			@elseif(!isset($input['linked']))
			
				<span class="text-grey-lightest rounded-full bg-blue px-4 py-1 text-xs uppercase opacity-50 cursor-pointer" data-toggle="tooltip" data-placement="top" title="Switch to Linked Only to Assign People to Master Email List">
					<i class="fa fa-envelope"></i> Master Email List
				</span>
			@else
				<a href="/{{ Auth::user()->team->app_type }}/emails/master?{{ http_build_query(request()->all()) }}" class="text-grey-lightest rounded-full bg-blue hover:text-white px-4 py-1 hover:bg-blue text-xs uppercase">
					<i class="fa fa-envelope"></i> Master Email List
				</a>
			@endif
		</div>

		<div class="mx-1 text-right">
			<a href="/{{ Auth::user()->team->app_type }}/exports?{{ http_build_query(request()->all()) }}" class="text-grey-lightest rounded-full bg-blue hover:text-white px-4 py-1 hover:bg-blue text-xs uppercase">
				<i class="fa fa-file-csv"></i> Export
			</a>
		</div>

	</div>

	<table class="text-sm w-full">

		

		<tr class="border-b bg-grey-lighter uppercase">

			<td class="p-2 " colspan="3">
				<span class="font-bold text-xl pl-2">{{ number_format($total_count) }}</span> 
				@if($total_count == 1)
					@lang('Constituent')
				@else
					@lang('Constituents')
				@endif
			</td>

			<td class="p-2" colspan="2">

				
				<span class="font-bold text-xl pl-2">&nbsp;</span> 

				@if(isset($search_value) && $search_value)
					<a href="/{{ Auth::user()->team->app_type }}/constituents/new/{{ $search_value }}">
						<button class="bg-blue rounded-lg px-4 py-2 text-white shadow text-sm float-right"><i class="fas fa-plus-circle"></i> New Unlinked @lang('Constituent') "{{ $search_value }}"
					</button></a>
				@else
					<a href="/{{ Auth::user()->team->app_type }}/constituents/new">
						<button class="bg-blue rounded-lg px-4 py-2 text-white shadow text-sm float-right"><i class="fas fa-plus-circle"></i> New @lang('Constituent')
						</button>
					</a>
				@endisset
			</td>
			@isset($input['parties'])
				<td>@lang('Party')</td>
			@endisset
			<td>
				
			</td>
			
		</tr>

 

	@foreach($people as $theperson)

		@if(($theperson->is_person))
			<tr class="border-b cursor-pointer bg-orange-lightest">
		@else
			<tr class="border-b cursor-pointer">
		@endif

			<td class="text-grey text-center w-1">
				{{ $loop->iteration }}.
			</td>

			<td class="w-4 p-2 text-xs text-right {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				@if(($theperson->is_person) && (isset($mode_all)))
					<i class="fas fa-check-circle w-4 text-grey-dark" title="Linked"></i>
				@endif
			</td>

			<td class="p-2 whitespace-no-wrap" valign="top">
				<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}">

					@if($theperson->is_person)
						<span class="text-black">
					@else
						<span class="">
					@endif

						@if ($theperson->archived_at)
							<i class="fa fa-archive"></i>
						@endif
						{{ $theperson->name }} 



					</span>
				</a>
			</td>

			<td class="w-4 p-2 text-xs text-left {{ (isset($narrow) ? 'hidden' : '') }}" valign="top" style="white-space: nowrap;">
				@if(isset($input['age']) && isset($input['age_operator']))
					<b>({{ $theperson->age }}<span class="text-xs">{{ $theperson->gender }}</span>)</b>
				@else
					{{ $theperson->age }}<span class="text-xs">{{ $theperson->gender }}</span>
				@endisset
			</td>

			<td class="p-2 pl-4 text-sm" valign="top">
				
				<div class="truncate" style="width:300px;">

					@isset($input['street'])
						<b>{{ $theperson->address_number }} {{ $theperson->address_street }}</b>
					@else
						{{ $theperson->address_number }} {{ $theperson->address_street }}
					@endisset

					@isset($input['municipalities'])
						<b>{{ $theperson->address_city }}</b>, 
					@else
						{{ $theperson->address_city }}, 
					@endisset

					{{ $theperson->address_state }} 
					@isset($input['zips'])
						<b>{{ $theperson->address_zip }}</b>
					@else
						{{ $theperson->address_zip }}
					@endisset

				</div>

			</td>

			
			@isset($input['parties'])
				<td>{{ $theperson->party }}</td>
			@endisset

<!-- 			<td class="pr-2 text-right">
				<i class="fa fa-info-circle text-grey"></i>
			</td> -->

		</tr>

	@endforeach
	</table>

@endif
</div>