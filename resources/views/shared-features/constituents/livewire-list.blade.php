
<div class="w-full">

	@if($total_count < 1)
	
		<div class="text-grey-dark py-2">
			No search results
		</div>

	@else

	<table class="text-sm w-full">

	@foreach($people as $theperson)

		
		<tr class="border-b cursor-pointer">

			<td class="text-grey-dark text-center w-1 px-2">
				{{ $loop->iteration }}.
			</td>

			<td class="w-4 p-2 text-xs text-right {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				@if(($theperson->is_person) && (isset($mode_all)))
					<i class="fas fa-check-circle w-4 text-grey-dark" title="Linked"></i>
				@endif
			</td>

			<td class="py-2 whitespace-no-wrap" valign="top">
				<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}" class="text-blue-dark">

					@if($theperson->archived_at)
						<i class="fa fa-archive"></i>
					@endif

					@if($theperson->is_person)
						<span class="rounded-lg bg-orange-lightest border py-1 px-2 -ml-1">
							<i class="fas fa-user mr-1 text-grey-darker"></i>
							{{ $theperson->name }}
						</span>
					@else
						<span class="">{{ $theperson->name }}</span>
					@endif

				</a>
			</td>

			<td class="w-4 p-2 text-xs whitespace-no-wrap text-left {{ (isset($narrow) ? 'hidden' : '') }}" valign="top">
				@if($theperson->deceased)
					<span class="text-xs uppercase text-red">Desceased</span>
				@elseif(isset($input['age']) && isset($input['age_operator']))
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

			
			@if(!empty($parties))
				<td>{{ $theperson->party }}</td>
			@endif

<!-- 			<td class="pr-2 text-right">
				<i class="fa fa-info-circle text-grey"></i>
			</td> -->

		</tr>

	@endforeach
	</table>

@endif


</div>