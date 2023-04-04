<div class="mt-4">

	<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">
		CF Voter Profile
	</div>

	@if ($model)

		@if(!$model->profile)

			<div class="text-grey-dark italic text-center text-sm p-2">
				No Profile Information Available
			</div>

		@else


			@if($model->profile->reliable_state)
				<div class="p-2 border-b text-orange-dark mono text-sm">

					<i class="fas fa-check-circle mr-2 text-orange-dark"></i>
					Reliably votes in <b>State</b> elections

					<span class="float-right" title="Explanation" data-toggle="tooltip" data-placement="top" ><i class="far fa-question-circle"></i>
					</span>

				</div>
			@endif

			@if($model->profile->reliable_local)
				<div class="p-2 border-b text-orange-dark mono text-sm">

					<i class="fas fa-check-circle mr-2 text-orange-dark"></i>
					Reliably votes in <b>Local</b> elections

					<span class="float-right" title="Explanation" data-toggle="tooltip" data-placement="top" ><i class="far fa-question-circle"></i>
					</span>

				</div>
			@endif

			@if($model->profile->recently_registered)
				<div class="p-2 border-b text-orange-dark mono text-sm">

					<i class="fas fa-check-circle mr-2 text-orange-dark"></i>
					<b>Registered</b> within the last year

					<span class="float-right" title="Explanation" data-toggle="tooltip" data-placement="top" ><i class="far fa-question-circle"></i>
					</span>

				</div>
			@endif

		@endif

	@endif

</div>

    