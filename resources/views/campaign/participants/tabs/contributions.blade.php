<div class="">

	<div class="border-b-4 border-grey-light text-grey-darkest text-base font-medium bg-grey-lightest py-1 px-2 mt-2 mb-2 rounded-t-lg">


		<div class="float-right">
			<button type="button" id="add-donation-button" class="rounded-lg bg-blue text-white px-2 py-1 text-sm uppercase" data-toggle="modal" data-target="#add-donation">
				Add
			</button>
		</div>

		<div class="text-grey-dark pt-1 pr-2  float-right text-sm">
			Total: ${{ number_format($participant->donations->sum('amount'),2,'.',',') }}
		</div>



		Contributions


	</div>



	@if(!$participant->donations->first())

		<div class="text-grey-dark text-sm italic pl-2">None</div>

	@else

		<div class="pl-2">

			@foreach($participant->donations()->orderBy('date', 'desc')->get() as $donation)

				<div class="flex text-sm {{ (!$loop->last) ? 'border-b' : '' }}">


					<div class="flex-1 p-2">
						{{ $donation->date }}
					</div>

					<div class="flex-1 p-2 truncate text-right">
						${{ number_format($donation->amount,2,'.',',') }}
					</div>

					<a href="/{{ Auth::user()->team->app_type }}/donations/{{ $donation->id }}/edit">
						<div class="flex-1 p-2">
							Edit
						</div>
					</a>

				</div>

			@endforeach

		</div>

	@endif

</div>

<br clear="all" />