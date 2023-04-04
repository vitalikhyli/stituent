<div class="flex w-full">

	<div class="w-2/3">

		<!-- <pre>{{ print_r($results) }}</pre> -->

		@if(isset($results[$model->id]))
			<div class="table w-full">

				@foreach($results[$model->id] as $key => $val)
					<div class="table-row">

						<div class="table-cell p-1 border-b border-dashed border-grey-dark text-right w-1/4
							@if($val > 0)
								text-blue font-bold
							@else
								text-grey
							@endif">
							{{ number_format($val) }}
						</div>

						<div class="table-cell p-1 border-b border-dashed border-grey-dark font-bold
							@if($val > 0)
								text-blue
							@else
								text-grey
							@endif">
							{{ $key }}
						</div>

					</div>
				@endforeach

			</div>
		@endif
	</div>

</div>