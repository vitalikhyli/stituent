<div class="border-b-4 text-xl pb-1 mt-4 ">
	<b>Votes</b> Needed

	<div class="float-right">
		<a href="/campaign/campaigns/{{ $campaign_current->id }}/edit?votes_needed=highlight">
			<button class="rounded-lg bg-blue hover:bg-blue-darker text-white px-2 py-1 text-sm">
				@if($campaign_current->votes_needed)
					Change Vote Goal
				@else
					Set Your Vote Goal
				@endif
			</button>
		</a>
	</div>
</div>

	
@if($campaign_current->votes_needed && $campaign_current->votes_needed > 0)

	@if($support_max > 0)

		<div class="flex w-full text-sm">

			<div class="pr-2 py-2 flex-shrink font-semibold">
				{{ number_format($support[1]) }}
				<span class="font-normal">of</span> {{ number_format($campaign_current->votes_needed) }}
			</div>

			<div class="bg-grey-lighter flex-grow">

				<div class="bg-blue p-2 align-center text-white text-lg font-bold"
					 style="height:100%;width:{{ $support[1]/$campaign_current->votes_needed * 100 }}%;">
					 <span class="bg-blue-darkest px-1">
					 {{ round($support[1]/$campaign_current->votes_needed * 100, 2) }}%
					</span>
				</div>

			</div>

		</div>

	@else

		<div class="text-grey-dark p-2">
			No support data yet
		</div>

	@endif

@endif