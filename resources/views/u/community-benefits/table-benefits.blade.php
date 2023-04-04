<div class="font-bold text-2xl border-b-4 border-blue py-2 mb-6">
		Community Benefits FY{{ substr($benefits->first()->fiscal_year, 2, 2) }}

		<a href="/u/community-benefits/new/{{ (!$mode) ? 'basic' : $mode }}/{{ $selected_year }}">
			<button type="button" class="float-right bg-blue text-white px-4 py-2 rounded-full font-normal text-sm ml-2 hover:bg-blue-dark shadow-sm">
				Add {{ ($mode == 'pilot') ? 'PILOT ' : '' }}Program
				@if($selected_year)
					for FY{{ substr($selected_year,2,2) }}
				@endif
			</button>
		</a>

	</div>

	<table class="table">

		<tr class="">
			<th class="w-6"></th>
			<th class="w-12"></th>
			<th class="w-1/2 uppercase text-grey-dark text-sm">Program</th>
			<th class="w-1/5 uppercase text-grey-dark text-sm">Payment</th>
			<th class="w-1/5 uppercase text-grey-dark text-sm">Community</th>
		</tr>

		@foreach ($benefits as $benefit)
			<tr class="line-div">
				<td class="text-grey">{{ $loop->iteration }}.</td>

				<td>
					<a href="/{{ Auth::user()->team->app_type }}/community-benefits/{{ $benefit->id }}/edit">
						<button class="rounded-lg bg-grey-lighter px-2 py-1 text-xs uppercase">
							Edit
						</button>
					</a>
				</td>

				<td>

					@if($benefit->pilot)
						<i class="fas fa-paper-plane text-green mr-1" data-toggle="tooltip" data-placement="top" title="PILOT"></i>
					@endif

					<span class="line-name-div font-bold">{{ $benefit->program_name }}</spans>
					<br>
					<span class="text-grey-dark text-sm">{{ $benefit->program_description }}</span>
				</td>
				<td>

					@if(!$benefit->value)
						<div class="font-bold">
							No Value
						</div>
					@else
						<div class="font-bold">
							${{ number_format($benefit->value) }}


							<span class="text-sm text-grey-dark font-normal">
								@if($summary[$benefit->fiscal_year]['total'] > 0)
									({{ round(($benefit->value / $summary[$benefit->fiscal_year]['total']) * 100, 2) }}%)
								@endif
							</span>

						</div>
					@endif

					@if($benefit->value_type)
						<div class="text-sm text-grey-dark">
							{{ $benefit->valueTypePretty }}
						</div>
					@endif

					@if($benefit->time_frame)
						<div class="text-sm text-grey-dark">
							{{ $benefit->time_frame }}
						</div>
					@endif

				</td>
				<td class="w-1/3">
					
					@if($benefit->beneficiaries)
						<div>
							<i>Beneficiaries:</i> {{ $benefit->beneficiaries }}
						</div>
					@endif
					
					<span class="text-grey-dark text-sm">
						@if($benefit->initiators)
							<div><i>Initiators:</i> {{ $benefit->initiators }}</div>
						@endif
						@if($benefit->partners)
							<div><i>Partners:</i> {{ $benefit->partners }}</div>
						@endif
					</span>
					
								
							
				</td>
			</tr>
		@endforeach

	</table>