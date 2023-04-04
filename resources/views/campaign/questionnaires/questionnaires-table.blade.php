	<div class="mt-2 w-full table">

			<div class="border-b p-2 table-row bg-grey-lighter text-sm uppercase">
				<div class="table-cell w-1/3 p-1">
					Name
				</div>

				<div class="table-cell p-1">
					Assigned
				</div>

				<div class="table-cell w-20">
					Due
				</div>

				<div class="table-cell w-20 p-1">
					Done?
				</div>

				<div class="table-cell w-1/4 p-1">
					Progress
				</div>

			</div>

			@foreach($questionnaires as $questionnaire)

				<div class="p-2 table-row text-grey-darker text-sm">
					<div class="table-cell border-b py-2 pl-1">
						<a href="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/edit">
							<i class="text-center fas fa-poll-h text-xl mr-1"></i> {{ $questionnaire->name }}
						</a>
					</div>

					<div class="table-cell border-b py-2">
						{{ $questionnaire->user->name }}
					</div>

					<div class="table-cell border-b py-2 {{ (($questionnaire->due_soon || $questionnaire->past_due) && !$questionnaire->done) ? 'text-red' : '' }}">

						@if(!$questionnaire->due)
							?
						@else
							{{ $questionnaire->due }}
						@endif

						@if($questionnaire->due_soon && !$questionnaire->done)
							<div class="">Due Soon</div>
						@endif

						@if($questionnaire->past_due && !$questionnaire->done)
							<div class="font-bold">Past Due</div>
						@endif

					</div>

					<div class="table-cell border-b py-2">
						@if(!$questionnaire->done)
							Pending
						@else
							<i class="fas fa-check-circle text-blue"></i>
						@endif
					</div>

					<div class="table-cell border-b py-2">
						

						@if($questionnaire->percent_done == 0)
							<div class="">
								{{ $questionnaire->questions_total }} Questions
							</div>
						@else
							<div class="bg-blue-lighter">
								<div style="width:{{ $questionnaire->percent_done }}%" class="bg-blue text-white text-sm py-1 px-2 rounded-r">
									{{ $questionnaire->questions_done }} of {{ $questionnaire->questions_total }} ({{ $questionnaire->percent_done }}%)
								</div>
							</div>
						@endif

					</div>



				</div>
				
			@endforeach
		</div>