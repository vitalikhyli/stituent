<div id="list">


@if($entities->count() <= 0)

	<div class="text-center">
		<div class="p-2 text-grey-dark">
			No Organizations added yet.
		</div>

		@isset($search_value)
		<a href="/{{ Auth::user()->team->app_type }}/organizations/new/{{ $search_value }}">
			<button class="hover:bg-blue-dark bg-blue rounded-lg px-4 py-2 text-white shadow"><i class="fas fa-plus-circle"></i> Create New Entity: {{ $search_value }}
		</button></a>
		@endisset

	</div>

@else


<table class="text-sm w-full">


		<tr>
			<th class="p-2"></th>
			<th class="p-2">Organization</th>
			<th class="p-2">Type</th>
			<th class="p-2">People</th>
			<th class="p-2">Cases</th>
		</tr>


		@foreach($entities as $entity)

			<tr class="border-b hover:bg-blue-lightest group">
				<td class="text-grey align-top pl-2 pt-2 w-1">{{ $loop->iteration }}</td>

				<td class="p-2" valign="top">
						<a href="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}">
						<span class="hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">

							<i class="fas fa-hotel mr-2"></i> 

							{!! $entity->name !!}

						</span>
						</a>
				</td>
				<td class="p-2 w-1/6" valign="top">
					@if (!$entity->type)
						<div class="group-hover:opacity-100 opacity-0">
							<form action="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/edit-type" method="POST">
								@csrf
								<div class="flex">
									<div class="w-4/6">
										<select name="type" class="edit-type border-2">
											<option value="">-- Select Type --</option>
											@foreach ($entity_types as $type)	
												@if ($type)
													<option value="{{ $type }}">{{ $type }}</option>
												@endif
											@endforeach
										</select>
										<div class="new-type" style="display:none;">
											<input autocomplete="off" name="new_type" type="text" class="border-2 h-6 w-full" />
											<span class="new-type-hide hover:text-black w-2/6 pl-2 text-xs pt-1 cursor-pointer">
												Hide
											</span>
											<button type="submit" class="float-right mr-2 mt-1 hover:text-black w-2/6 pl-2 text-xs pt-1 cursor-pointer border rounded-full px-2 py-1">
												Save
											</button>
										</div>
									</div>
									<div class="new-type-show hover:text-black w-2/6 pl-2 text-xs pt-1 cursor-pointer">
										New
									</div>
								</div>
							</form>
						</div>
					@else
						{{ $entity->type }}
					@endif
				</td>

				<td>
					@foreach ($entity->people as $person)
						<div class="">
							{{ $person->name }}
						</div>
					@endforeach
				</td>

				<td>
					@foreach ($entity->cases as $case)
						<div class="text-blue-400">
							<a href="/{{ Auth::user()->app_type }}/cases/{{ $case->id }}">
								{{ $loop->iteration }}.
								@if ($case->date)
									{{ $case->date->format('n/j/Y') }} - 
								@endif
								{{ $case->name }}
							</a>
						</div>
					@endforeach
				</td>
			</tr>

		@endforeach
	

</table>

@endif
</div>