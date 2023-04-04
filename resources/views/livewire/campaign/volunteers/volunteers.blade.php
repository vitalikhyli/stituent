<div>

	<div>

		<div class="flex pb-2">

			<div class="flex-shrink py-1 text-2xl font-bold border-b-4 pb-2">
				Volunteers ({{ $volunteers->count() }})
			</div>

			<div class="flex-grow text-right border-b-4 pb-2">
				<!-- <i class="fas fa-search mr-2 w-4"></i> -->
				<input type="input"
					   class="border p-2 text-sm text-black w-1/3"
					   placeholder="Search" 
					   wire:model.debounce="search"
					   id="search" />

				<button class="rounded-lg bg-red text-xs ml-1 text-white p-2 text-center font-normal">
					+ New
				</button>

			</div>

		</div>


		<div class="flex flex-wrap text-sm p-2">

			<div class="w-1/4">

				<input type="hidden" name=""  value="0" />

				<span class="capitalize text-sm text-blue whitespace-no-wrap pr-2">
					
					<label for="types_any" class="font-normal">

						<input id="types_any" name="" type="checkbox" checked value="1"
							   wire:model="types_any" />

						<span class="ml-1">
							Any
						</span>

					</label>
				</span>

			</div>

			@foreach($volunteer_options as $option)

				<div class="w-1/4">


					<span class="capitalize text-sm text-blue whitespace-no-wrap pr-2">
						
						<label for="volunteer_{{ $option }}" class="font-normal">

							<input wire:model="types"
								   value="{{ $option }}"
								   id="volunteer_{{ $option }}" name="volunteer_{{ $option }}" type="checkbox" checked value="1" />

							<span class="ml-1">
								{{ str_replace('_', ' ', str_replace('-', ' ', $option)) }}
							</span>

						</label>
					</span>

				</div>

			@endforeach

		</div>

	</div>

	<div class="pb-1">

		@if(!$volunteers->first())

			<div class="py-2 text-grey-dark">
				None
			</div>

		@else

			<div class="table text-sm">

				<div class="table-row bg-grey-lighter">

					<div class="table-cell border-b py-1 px-1">
						Email
					</div>

					<div class="table-cell border-b py-1 px-1">
						Name
					</div>

					<div class="table-cell border-b py-1 px-1">
						Types
					</div>

					<div class="table-cell border-b py-1 px-1 w-12">
						Opps
					</div>

				</div>

				@foreach($volunteers as $vol)

					<div class="table-row">

						<div class="table-cell border-b py-2 px-1 text-red hover:underline">
							<div class="truncate w-48">

								<a href=""
								   class="">
								   <i class="w-4 text-center far fa-user mr-1"></i>
								   {{ $vol->email }}
								</a>
							</div>
						</div>

						<div class="table-cell border-b py-2 px-1">
							<div class="truncate w-1/2">
							   {{ $vol->username }}
							</div>
						</div>

						<div class="table-cell border-b py-2 px-1 text-grey-dark">
							@if($vol->types)
								{{ implode(', ', $vol->types) }}
							@endif
						</div>

						<div class="table-cell border-b py-2 px-1 text-right pr-2">
							@if(!$vol->opportunities->first())
								<span class="text-grey">-</span>
							@else
								{{ number_format($vol->opportunities->count()) }}
							@endif
						</div>

					</div>

				@endforeach

			</div>

		@endif

	</div>

</div>