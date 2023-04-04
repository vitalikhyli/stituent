@if(!$prospects->first())

	<div class="mt-6 text-xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
		Prospects
	</div>

	<div class="text-grey-dark py-2">
		No prospects yet
	</div>

@else

	@foreach($prospect_types as $type)
		<div class="mt-6 text-xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
			@if(!$type)
				(No Type)
			@else
				{{ $type }}
			@endif
		</div>

		<div class="table">

			<div class="table-row text-sm w-full">

				<div class="table-cell p-2 bg-grey-lighter uppercase border-b">
					Prospect
				</div>
				<div class="table-cell py-2 bg-grey-lighter uppercase border-b">
					Progress
				</div>

				<div class="table-cell p-2 bg-grey-lighter uppercase border-b">
					Latest
				</div>

				<div class="table-cell p-2 bg-grey-lighter uppercase border-b">
					Next
				</div>

				<div class="table-cell p-2 bg-grey-lighter uppercase border-b">
					User
				</div>

			</div>


			@foreach($prospects->where('type', $type) as $opportunity)
				<div class="table-row text-sm w-full mb-1">

					<div class="table-cell p-2 align-middle border-b w-1/4 truncate">
						<a href="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}">
							{{ mb_strimwidth($opportunity->entity->name, 0, 20, "...") }}
						</a>
					</div>

					<div class="table-cell pt-3 pr-2 align-middle border-b w-1/3">

						<div class="bg-green rounded-l rounded-r w-full h-1 ">	

							<div class="text-right z-50" style="width:{{ $opportunity->progressPercentage }}%;">
								
								<div class="dot text-white bg-blue border-1 border-blue float-right -mt-2 p-2 text-xs">
									<div class="-mt-1">{{ $opportunity->highestStep }}</div>
								</div>

							</div>

						</div>

					</div>

					<div class="table-cell p-2 align-middle border-b w-1/6">
						Emailed
					</div>

					<div class="table-cell p-2 align-middle border-b w-1/6">
						Call
					</div>

					<div class="table-cell p-2 align-middle border-b text-xs text-blue">
						Ryan
					</div>

				</div>
			@endforeach

		</div>

	@endforeach

@endif