 <div class="text-grey-dark">

	<div class="whitespace-no-wrap text-base text-grey-dark py-2">

		<i class="w-6 text-center far fa-comments text-xl mr-2"></i>

		@if ($thecontact->call_log)
			<i class="fa fa-phone"></i> Call Log
		@endif

		{{ $thecontact->created_at->format('n/j/Y, g:ia') }}
		@if($thecontact->category)
			
		@endif

				<!-- No Convert to Case Button -->
				
	</div>


				<div class="ml-10 inline-flex flex-wrap mb-2">

<!-- 					<div class="py-1 text-sm flex-1 flex-initial mr-4">
							Between: 
					</div> -->

					<div class="px-2 py-1 mb-1 text-sm flex-1 flex-initial rounded-lg bg-white border mr-2">
							<i class="fas fa-user mr-2"></i>
							@if($thecontact->assignedTo()->id == Auth::user()->id)
								Me
							@else
								{{ $thecontact->assignedTo()->name }}
							@endif
					</div>

					@if($thecontact->people->count() >0)
						@foreach($thecontact->people as $theperson)
							<a href="/campaign/constituents/{{ $theperson->id }}">
								<div class="bg-grey-lighter hover:bg-blue hover:text-white rounded-lg mr-1 mb-1 px-2 py-1 text-sm flex-1 flex-initial">
									{{ $theperson->full_name }}
								</div>
							</a>
						@endforeach
					@endif
				</div>


	<div class="ml-10 text-black">
		@if($thecontact->subject)
			<b class="">
				{{ $thecontact->subject }}
			</b>
		@endif
		@if($thecontact->notes)
			<div class="border-l-4 pl-4 mt-2 text-black">
				{!! $thecontact->notes !!}
			</div>
		@endif
	</div>

</div>
