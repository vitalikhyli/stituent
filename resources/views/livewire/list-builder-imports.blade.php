<div class="flex text-grey-dark">
	<div class="w-1/4"></div>
	<div class="w-3/4">

		@foreach ($imports as $import)

			@php
				$import_used = isset($input['full_imports'][$import->id]) &&
				 isset($input['full_imports'][$import->id]['use']) &&
				 $input['full_imports'][$import->id]['use'];
			@endphp

			<div class="flex w-full pt-2">
				<div class="w-1/3 relative uppercase text-sm">

					

					@if ($import_used)
						<div class="absolute pin-l -ml-6 text-base text-blue-light">
							<i class="fa fa-check-circle"></i>
						</div>
						<div class="font-bold text-blue">
							{{ $import->name }}
						</div>
					@else
						{{ $import->name }}
					@endif

				</div>
				<div class="w-2/3 flex text-sm">

					<div class="flex w-full
						@if ($import_used)
							border-b
						@endif
						">
						<div class="relative text-sm w-24" wire:ignore >
							<label for="include_archived" class="font-normal">
								<input type="checkbox"
									   id="include_archived"
									   wire:model="input.full_imports.{{ $import->id }}.use"
									   value="1" 
									    /> <span class="px-1">Use</span>

							</label>
						</div>
						@if ($import_used && count($import->columns) > 0)
							<div class="flex-1">
								
								<select wire:model="input.full_imports.{{ $import->id }}.new_filter">
									<option value="">
										Add Filter ({{ count($import->columns) }} Columns)
									</option>
									@foreach ($import->columns as $column)
										<option value="{{ $column }}">Filter by: {{ $column }}</option>
									@endforeach
								</select>

								@php 
									$filter_arr = $input['full_imports'][$import->id];
								@endphp

								<div class="w-full p-2">
									@if (isset($filter_arr['filters'])) 
										@foreach ($filter_arr['filters'] as $filtercol => $filterval)

											@php
												$filteroptions = $import->filterOptions($filtercol);
											@endphp

											<div class="flex mb-1">
												<div class="w-1/2 pt-1">
													{{ $loop->iteration }}. {{ $filtercol }}:
												</div>
												<div class="flex-1">
													<select class="border p-1 w-full" wire:model="input.full_imports.{{ $import->id }}.filters.{{ $filtercol }}">
														<option value="">{{ count($filteroptions) }} options</option>
														@foreach ($filteroptions as $filteroption => $filtercount)
															<option value="{{ $filteroption }}">
																{{ $filteroption }} ({{ $filtercount }})
															</option>
														@endforeach
													</select>
												</div>
												<div wire:click="removeFilter({{ $import->id }}, '{{ $filtercol }}')" class="w-8 text-red-300 hover:text-red-500 cursor-pointer">
													<i class="m-2 fa fa-times"></i>
												</div>
											</div>
										@endforeach
									@endif
								</div>
								
									
							</div>
							
						@endif
					</div>

				</div>

			</div>

		@endforeach

		

	</div>


</div>