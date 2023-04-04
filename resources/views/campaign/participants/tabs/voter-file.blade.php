<div>

	<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">

			<button type="button" class="float-right rounded-lg bg-grey uppercase text-xs text-white px-2 py-1" data-toggle="tooltip" data-placement="left" title="This is the data that Community Fluency gets from the public voter file. You can make changes to it in your system on the left, but the voter file data will stay the same.">
				<i class="fas fa-question"></i>
			</button>

		Voter Data

		@if($participant)
			<a href="?edit_voter_id=1"
			   class="text-blue text-xs">| Edit Voter Link</a>
		@endif
		
	</div>

	@if ($model)

	<div class="font-mono">

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Name
			</div>
			<div class="flex-grow text-blue-dark">
				@if($model->full_name_middle)
					{{ $model->full_name_middle }}
				@elseif($model->full_name)
					{{ $model->full_name }}
				@else
					{{ trim($model->first_name.' '.$model->middle_name).' '.$model->last_name }}
				@endif
			</div>
		</div>


		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Voter ID
			</div>
			<div class="flex-grow text-blue-dark">
				{{ $model->id }}
			</div>
		</div>


		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Registered
			</div>
			<div class="flex-grow text-blue-dark">

				@if(!$model->registration_date)
					-
				@else
					<div>
						{{ \Carbon\Carbon::parse($model->registration_date)->toDateString() }}
						<span>
							({{ \Carbon\Carbon::parse($model->registration_date)->diffForHumans() }})
						</span>
					</div>
				@endif

			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Party
			</div>
			<div class="flex-grow text-blue-dark">
				{{ $model->party}}
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Address
			</div>
			<div class="flex-grow text-blue-dark">
				{{ $model->address_number }}
				{{ $model->address_fraction }}
				{{ $model->address_street }}
				{{ $model->address_apt }}
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				City
			</div>
			<div class="flex-grow text-blue-dark">
				{{ $model->address_city }}
				{{ $model->address_state }}
				{{ $model->address_zip }}
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Ward
			</div>
			<div class="flex-grow text-blue-dark">
				@if(!$model->ward)
					-
				@else
					{{ $model->ward }}
				@endif
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Precinct
			</div>
			<div class="flex-grow text-blue-dark">
				@if(!$model->precinct)
					-
				@else
					{{ $model->precinct }}
				@endif
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Born / Age
			</div>
			<div class="flex-grow text-blue-dark">
				@if($model->dob)
					{{ \Carbon\Carbon::parse($model->dob)->toDateString() }} - 
				@endif
				Age {{ $model->age }}
			</div>
		</div>

		<div class="flex pl-1 py-1 text-sm border-b border-grey-light">
			<div class="w-1/4 text-grey-dark uppercase pr-4 text-right">
				Gender
			</div>
			<div class="flex-grow text-blue-dark">
				{{ $model->gender}}
			</div>
		</div>

	</div>

	@endif

</div>