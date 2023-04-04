<div>

	@if($upload->integrated_count != $upload->count)


		<div class="text-3xl font-bold pb-2">
			Integrating File...
		</div>

		<div>
            <div wire:poll.1000ms class="flex w-5/6 items-center">

            <div class="w-4/5 rounded-full h-4 border">
                <div class="bg-blue-light h-4 rounded-full" style="width: {{ round(($upload->integrated_count/$upload->count) * 100) }}%;"></div>
            </div>
            <div class="w-1/5 pl-6 text-blue-light font-bold text-2xl">
                {{ round(($upload->integrated_count/$upload->count) *100) }}%
            </div>

        </div>

	    <div class="flex">
	        @foreach(str_split(str_pad($upload->integrated_count, 7,'0',STR_PAD_LEFT)) as $key => $char)
	            <div class="font-mono font-bold {{ ($key >= 7- strlen($upload->integrated_count)) ? 'text-blue' : 'text-grey' }} border-t-4 border-l border-grey-dark px-2 py-2 bg-white shadow-lg">{{ $char }}</div>
	        @endforeach
	        <div class="text-lg font-bold p-2">Records</div>
	    </div>


	@else


		<div class="text-3xl font-bold border-b-4 border-blue pb-2">
			
			Summary

		</div>

		<div class="w-full flex">

			<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
				<a href="/{{ Auth::user()->team->app_type }}/useruploads/{{ $upload->id }}/import" class="text-white hover:text-white">
					<div>
						<i class="fas fa-check-circle mr-1"></i> Import
					</div>
				</a>
			</div>

			<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
				<a href="/{{ Auth::user()->team->app_type }}/useruploads/{{ $upload->id }}/match" class="text-white hover:text-white">
					<div>
						<i class="fas fa-check-circle mr-1"></i> Match
					</div>
				</a>
			</div>

			<div class="cursor-pointer bg-blue text-white p-2 text-center uppercase text-sm border-r-2 border-white w-1/3">
				<i class="fas fa-check-circle mr-1"></i> Integrate
			</div>

		</div>


		<div class="text-xl font-bold border-b-4 border-blue pb-2 mt-4">
			
			Results for Upload: <span class="text-grey-dark">{{ $upload->name }}</span>

		</div>

		<div class="table text-lg font-bold">
			@if(\App\Team::find(Auth::user()->current_team_id)->app_type == 'campaign')

				<div class="table-row">
					<div class="table-cell border-b py-1 text-blue text-right w-12">
						{{ number_format($summary['participants_matches']) }}
					</div>
					<div class="pl-4 table-cell border-b py-1 text-grey-dark w-1/3">
						Participants Matched
					</div>
				    <div class="table-cell border-b py-1 text-red">
				    	@if($summary['participants_matches_vf'] > 0)
							{{ number_format($summary['participants_matches_vf']) }}
							<span class="text-sm font-normal pl-2">Voter File {{ Str::plural('Match', $summary['participants_matches_vf']) }}</span>
						@endif
					</div>

				</div>

				<div class="table-row">
					<div class="table-cell border-b py-1 text-blue text-right w-12">
						{{ number_format($summary['participants_new']) }}
					</div>
					<div class="pl-4 table-cell border-b py-1 text-grey-dark w-1/3">
						Participants Created

						<div class="text-blue text-sm">
							
							@if($upload->new_rules == 'if-email')
								Created because file records had emails
							@endif
							@if($upload->new_rules == 'if-email-or-voter-email')
								Created because file records had emails or Voter File matches had emails
							@endif

						</div>

					</div>
				    <div class="table-cell border-b py-1 text-red">
				    	@if($summary['participants_new_vf'] > 0)
							{{ number_format($summary['participants_new_vf']) }}
							<span class="text-sm font-normal pl-2">Voter File {{ Str::plural('Match', $summary['participants_new_vf']) }}</span> 
						@endif
					</div>
				</div>

			@endif

			@if(\App\Team::find(Auth::user()->current_team_id)->app_type != 'campaign')

				<div class="table-row">
					<div class="table-cell border-b py-1 text-blue text-right w-12">
						{{ number_format($summary['people_matches']) }}
					</div>
					<div class="pl-4 table-cell border-b py-1 text-grey-dark w-1/3">
						People Matched
					</div>
				    <div class="table-cell border-b py-1 text-red">
				    	@if($summary['people_matches_vf'] > 0)
							{{ number_format($summary['people_matches_vf']) }}
							<span class="text-sm font-normal pl-2">Voter File {{ Str::plural('Match', $summary['people_matches_vf']) }}</span>
						@endif
					</div>

				</div>

				<div class="table-row">
					<div class="table-cell border-b py-1 text-blue text-right w-12">
						{{ number_format($summary['people_new']) }}
					</div>
					<div class="pl-4 table-cell border-b py-1 text-grey-dark w-1/3">
						People Created

						<div class="text-blue text-sm">
							
							@if($upload->new_rules == 'if-email')
								Created because file records had emails
							@endif
							@if($upload->new_rules == 'if-email-or-voter-email')
								Created because file records had emails or Voter File matches had emails
							@endif

						</div>

					</div>
				    <div class="table-cell border-b py-1 text-red">
				    	@if($summary['people_new_vf'] > 0)
							{{ number_format($summary['people_new_vf']) }}
							<span class="text-sm font-normal pl-2">Voter File {{ Str::plural('Match', $summary['people_new_vf']) }}</span> 
						@endif
					</div>
				</div>

			@endif


			<div class="table-row">
				<div class="table-cell border-b py-1 text-blue text-right w-12">
					{{ number_format($summary['skipped']) }}
				</div>
				<div class="pl-4 table-cell border-b py-1 text-grey-dark w-1/3">
					Skipped
				</div>
			    <div class="table-cell border-b py-1 text-red">

				</div>
			</div>

			<div class="table-row">
				<div class="table-cell py-1 text-right w-12">
					{{ number_format($summary['total']) }}
				</div>
				<div class="pl-4 table-cell py-1">
					Total
				</div>

			</div>

		</div>

		<table class="table text-sm">
			<tr class="bg-grey-lightest uppercase text-xs border-b-4 border-blue ">
				<th>#</th>
				<th>Line</th>
				<th>Voter ID</th>
				@if(\App\Team::find(Auth::user()->current_team_id)->app_type != 'campaign')
					<th>Constituent</th>
				@endif
				@if(\App\Team::find(Auth::user()->current_team_id)->app_type == 'campaign')
					<th>Participant</th>
				@endif
				<th class="text-gray-500">Data</th>
			</tr>
			@foreach ($records as $record)
				<tr>
					<td class="w-8 text-grey-dark">{{ $loop->iteration }}.</td>
					<td class="w-8">{{ $record->line }}</td>
					<td class="w-24 text-red">{{ $record->voter_id }}</td>

					@if(\App\Team::find(Auth::user()->current_team_id)->app_type != 'campaign')
					<td class="w-64">
						@if ($record->person)
							<a href="/office/constituents/{{ $record->person->id }}">
								{{ $record->person->name }}
							</a>

							@if($record->person->upload_id == $upload->id)
								<span class="whitespace-no-wrap text-blue text-xs rounded-full bg-yellow-light p-1"><i class="fas fa-star text-blue"></i> New</span>
							@endif

						@endif
					</td>
					@endif

					@if(\App\Team::find(Auth::user()->current_team_id)->app_type == 'campaign')
					<td class="w-64">
						@if ($record->participant)

							<a href="/campaign/participants/{{ $record->participant->id }}">
								{{ $record->participant->name }}
							</a>

							@if($record->participant->upload_id == $upload->id)
								<span class="whitespace-no-wrap text-blue text-xs rounded-full bg-yellow-light p-1"><i class="fas fa-star text-blue"></i> New</span>
							@endif

						@endif
					</td>
					@endif

					<td class="text-xs text-gray-500">
						@foreach($record->data as $key => $val)
							@if($loop->iteration % 2 != 0)
								<div class="flex">
							@endif

								<div class="w-1/2">{{ $val }}</div>

							@if($loop->iteration % 2 == 0)
								</div>
							@endif
						@endforeach
					</td>
				</tr>
			@endforeach
		</table>

	@endif

</div>