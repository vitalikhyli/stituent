<div id="list">


@if($entities->count() <= 0)

	<div class="text-center">
		<div class="p-2 text-grey-dark">
			Search found nothing
		</div>

		@isset($search_value)
		<a href="/{{ Auth::user()->team->app_type }}/entities/new/{{ $search_value }}">
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
			<th class="p-2">Partnerships</th>
		</tr>


		@foreach($entities as $entity)

			<tr class="border-b hover:bg-blue-lightest group">
				<td class="text-grey align-top pl-2 pt-2 w-1">{{ $loop->iteration }}</td>

				<td class="p-2" valign="top">
						<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">
						<span class="hover:bg-blue hover:text-white bg-grey-lighter rounded-full m-1 px-2 py-1 text-sm">

							<i class="fas fa-hotel mr-2"></i> 

							{!! $entity->name !!}

						</span>
						</a>
				</td>
				<td class="p-2 w-1/6" valign="top">
					@if (!$entity->type)
						<div class="group-hover:opacity-100 opacity-0">
							<form action="/u/entities/{{ $entity->id }}/edit-type" method="POST">
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

				<td class="p-2" valign="top">

					<a class="float-right text-xs border rounded-full text-grey px-2 py-1" href="/u/entities/{{ $entity->id }}/partnerships/new">
						New
					</a>


					@if($entity->partnerships)
						<ol class="-ml-6">

						@foreach ($entity->partnershipTypes() as $partnershipType)
							<div class="font-bold">{{ $partnershipType->name }}</div>
						

							@foreach ($entity->partnerships->where('partnership_type_id',$partnershipType->id) as $partnership)
							
								<div class="ml-4 flex mb-2 w-full">
									<!-- @if ($partnership->partnershipType)
										<b>{{ $partnership->partnershipType->name }}</b> - 
									@endif -->


									<div class="w-12 mx-2 text-blue-dark whitespace-no-wrap">
											<i class="fas fa-user"></i> ({{ count($partnership->contacts) }})
									</div>

									{{ $partnership->program }}

									@if ($partnership->contacts)

										@if(false)
										@foreach ($partnership->contacts as $contact)
										<div class="ml-4">
											@if(($contact['name']) && ($contact['email']))
													{{ $contact['name'] }}, {{ $contact['email'] }}
											@elseif($contact['name'])
												{{ $contact['name'] }}
											@elseif($contact['email'])
												{{ $contact['email'] }}
											@endif
										</div>
										@endforeach
										@endif

	

									@endif
								</div>
							@endforeach

						@endforeach

							<!-- @foreach ($entity->departmentPartnerships as $partnership)
								<li>
									@if ($partnership->partnershipType)
										<b>{{ $partnership->partnershipType->name }}</b> - 
									@endif
									{{ $partnership->program }}
									@if ($partnership->contacts)

										@foreach ($partnership->contacts as $contact)
											{{ $contact['name'] }}, {{ $contact['email'] }}<br>
										@endforeach

									@endif
								</li>
							@endforeach -->
						</ol>
					@endif
					@if ($entity->departmentPartnerships()->count() > 0)
						<b class="pl-6">{{ $entity->departmentPartnerships()->count() }} Service Learning Partnerships</b>
					@endif
				</td>
			</tr>

		@endforeach
	

</table>

@endif
</div>