<div class="text-center">

		    <div class="inline-block overflow-scroll w-5/6 mt-2 h-64">


		    	<div class="table preview">

			    	@foreach($preview as $row)

				   		<div class="table-row">

					    	@foreach($upload->columns as $key => $column)
								<div class="
											@if($loop->parent->first)
												bg-grey-darkest text-grey-lighter 
											@else
												bg-grey-lightest 
											@endif
											@if($loop->parent->iteration == $preview_count-2)
												opacity-75
											@endif
											@if($loop->parent->iteration == $preview_count-1)
												opacity-50
											@endif
											@if($loop->parent->iteration == $preview_count)
												opacity-25
											@endif
											table-cell border-r border-b border-grey text-xs p-1 whitespace-no-wrap
											">
									@isset($row[$key])
										{{ mb_strimwidth($row[$key], 0, 10, "...") }}
									@endisset
								</div>

					    	@endforeach

				    	</div>

			    	@endforeach

		    	</div>

		    	<div class="px-2 text-sm font-bold text-blue">

		    		{{ $upload->count }} totals rows
		    		
		    	</div>

			</div>

		</div>