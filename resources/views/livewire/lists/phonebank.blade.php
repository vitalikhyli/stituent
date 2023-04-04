<div>

    <div class="w-full md:flex text-lg font-bold pb-2 border-b-2 border-blue">

    	<div class="md:w-3/4 text-3xl md:truncate">

			<span class="text-blue">
				<i class="fas fa-phone mr-1"></i> List:
			</span>

    		{{ $list->name }}
    	</div>
		

		<div class="md:w-2/5 text-blue mt-4 text-right flex">

			<div class="flex-grow whitespace-no-wrap">
				{{ number_format($count) }} Voters
			</div>


			<div class="w-flex shrink  text-base text-black font-normal pl-2 text-right whitespace-no-wrap">
				
				<input class="font-bold border text-center py-1 w-10 text-blue-500" type="text" wire:model.debounce.1000ms="perpage" />
					Per page

			</div>

			<div class="pl-2 flex">

			    <select wire:model="page"
			    		wire:loading.class="opacity-50"
			    		class="font-normal text-sm text-black">

				    @for($page = 1; $page <= round($count/$perpage, 0); $page++)

				    	<option value="{{ $page }}">Go to Page {{ $page }}</option>

				    @endfor

			    </select>


			</div>

		</div>
		
	</div>


	@if($voters instanceof \Illuminate\Pagination\LengthAwarePaginator )

		<div class="">
			{{ $voters->links() }}
		</div>

	@endif

	@if(Auth::user()->permissions->developer)

		<div class="bg-red-lightest border px-4 py-2 mt-2">
			<div class="font-bold text-blue">
				Developer Only:
			</div>
			<div>
				Rendered in <span class="font-bold">{{ number_format($timer_a,2) }}</span> seconds <u>duration of:</u> $this->list->voters()->paginate()
			</div>
			<div>
				Rendered in <span class="font-bold">{{ number_format($timer,2) }}</span> seconds next part before return view
			</div>
		</div>

	@endif

	<div class="text-grey-dark text-sm mt-8">


		@foreach ($voters as $voter)

			<div class="md:flex w-full">

				<div class="md:w-1/6 text-right center pt-4 pr-8 text-center md:text-left"
				     wire:poll.10000ms>

					@if($voter->reserved_by_user)

						<button class="rounded-full bg-orange text-white hover:bg-orange-dark px-4 py-2 text-lg border w-full"
						wire:click="clearReserve()">
							<i class="fas fa-thumbs-up mr-1"></i>
							DONE
						</button>

						<div class="p-2 text-sm text-grey-darker text-center md:text-left">
							<div class="font-medium">
								Reserved for you
							</div>
							<div>
								{{ trim(str_replace('ute', '', str_replace('ond', '', str_replace('from now', '', \Carbon\Carbon::parse($voter->reserved_expires_at)->diffForHumans(['parts' => 2]))))) }}
							</div>
						</div>

					@elseif(!$voter->reserved_by_others)

						<button class="rounded-full bg-blue text-white px-4 py-2 text-lg border hover:bg-blue-darker w-full"
						wire:click="reserve('{{ $voter->id }}')">
							<i class="fas fa-phone mr-1"></i>
							START
						</button>

					@else

						<button class="rounded-full bg-grey-lightest text-grey-darkest px-4 py-2 text-lg border hover:bg-white hover:text-grey-light w-full">
							<i class="fas fa-user-times"></i>
							IN USE
						</button>

					@endif

					<div wire:loading.class="text-grey-lighter" class="text-white text-center text-xs py-1">
						Checking...
					</div>

				</div>

				<div class="md:w-5/6"
					 wire:key="voterform_{{ $voter->id }}">

					<!-- https://forum.laravel-livewire.com/t/child-component-not-rendering-new-data-from-parent/633 -->

					@if($voter->reserved_by_user)

						@include('livewire.lists.phonebank-detail-edit',
						[
							'voter' => $voter,
							'iteration' => $loop->iteration + ($voters->currentPage() - 1) * $perpage,
							'cf_plus_phones' => $data[$voter->id]['cf_plus_phones'],
							'cf_plus_cell' => $data[$voter->id]['cf_plus_cell'],
							'participant_phone' => $data[$voter->id]['participant_phone'],
							'participant_email' => $data[$voter->id]['participant_email'],
						])

					@else

						@include('livewire.lists.phonebank-detail-static',
						[
							'voter' => $voter
						])

					@endif					

				</div>

			</div>


		@endforeach

	</div>


</div>