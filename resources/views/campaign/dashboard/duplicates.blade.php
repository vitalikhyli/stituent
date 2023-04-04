@if($data->duplicate_voter_ids->count() > 0)

	<div class="rounded-lg bg-red-lightest border">

		<div class="font-medium bg-red-light text-white rounded-lg-t px-2 py-1">
			<i class="fas fa-exclamation-triangle text-base -mt-1 inline mx-1"></i> Duplicates Found <div class="text-sm font-mono float-right pt-1">({{ $data->duplicate_voter_ids->count() }})</div>
		</div>

		<div class="text-sm p-4 italic text-grey-darker text-justify">
			You have participants in your database that have the same voter id. This can lead to confusion and errors when creating lists.
		</div>

		<div class="px-4 py-2">

			@foreach($data->duplicate_voter_ids as $id => $participants)

				<div class="{{ (!$loop->last) ? 'mb-4' : 'mb-2' }}">

					<div class="font-bold flex border-b-2 pb-2">
						<div class="flex-grow">
							{{ $id }}
						</div>

						<div class="text-right">
							@if(Auth::user()->developer)
								<a href="/campaign/participants/{{ $participants[0]->id }}/merge?merge_with={{ $participants[1]->id }}">
									<button class="font-normal text-xs uppercase rounded-lg bg-blue hover:bg-blue-darker text-white px-2 py-1">
										Merge {{ ($participants->count() > 2) ? 'Top 2' : '' }}
									</button>
								</a>
							@else
								<button class="font-normal text-xs uppercase rounded-lg bg-blue hover:bg-blue-darker text-white px-2 py-1 opacity-50">
									Merge
								</button>
							@endif
						</div>

					</div>

					@foreach($participants as $participant)
							<a href="/campaign/participants/{{ $participant->id }}"
							   class="text-blue">

							   <li>
								{{ $participant->full_name }} <span class="text-sm font-mono float-right">#{{ $participant->id }}</span>
							</li>
							</a>
					@endforeach

				</div>

			@endforeach

		</div>

	</div>

@endif