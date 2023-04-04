<div class="pb-2 {{ ($comment->closed_at) ? '' : '' }}"
	 wire:key="comment_{{ $mode }}_{{ $comment->id }}">

	<div class="text-sm mb-1 {{ (!$comment->closed_at) ? 'text-blue' : 'text-red' }}">
		{{ \Carbon\Carbon::parse($comment->created_at)->format('F j, Y') }}
	</div>

	<div class="flex">

		@if($comment->score >= 0)
			<div class="border-r-2 border-blue">

				<div class="text-center bg-blue text-white p-2 inline-block w-10 text-sm font-bold text-sm">
					{{ $comment->score }}
				</div>

				<div class="py-1 bg-grey-lighter text-grey-darker text-center uppercase border-b" style="font-size:50%;">
					Net
				</div>

			</div>
		@else
			<div class="border-r-2 border-red">

				<div class="text-center bg-red text-white p-2 inline-block w-10 text-sm font-bold text-sm">
					{{ $comment->score }}
				</div>

				<div class="py-1 bg-grey-lighter text-grey-darker text-center uppercase border-b" style="font-size:50%;">
					Net
				</div>

			</div>
		@endif

		<div class="pl-4 w-full">

			<div class="font-bold text-lg">
				{{ $comment->subject }}
			</div>

			<div class="text-grey-dark">
				{{ $comment->notes }}
			</div>


			@if($comment->closed_at)

				<div class="text-red border border-dashed border-red mt-6 rounded-lg shadow -ml-10">
					
					<div class="text-sm border-b py-2 px-4 text-right bg-grey-lightest rounded-lg">
						Closed {{ \Carbon\Carbon::parse($comment->closed_at)->diffForHumans() }}
					</div>

					<div class="text-blue p-4 bg-white rounded-b-lg">

						{!! nl2br($comment->closing_notes) !!}

						<div class="font-medium text-grey-dark text-sm pt-2 text-right">
							- {{ $comment->closedByName }}
						</div>

					</div>

				</div>

			@endif


			@if(Auth::user()->permissions->developer)

				<div x-data="{ dev_{{ $comment->id }} : false }"
					 class="text-right">

					<button @click="dev_{{ $comment->id }} = !dev_{{ $comment->id }}"
						 class="text-xs rborder bg-grey-lighter cursor-pointer hover:bg-orange-lightest px-2 py-1 mt-2 mb-2 border">
						Developer Toggle
					</button>

					<div x-show="dev_{{ $comment->id }}">

						<div class="border border-grey-darkest text-sm rounded-lg bg-grey-lighter">

							<div class="px-2 py-1 bg-black text-white rounded-t-lg">Dev</div>

							<div class="p-2 w-full">

								<form wire:submit.prevent="closeComment({{ $comment->id }})">

									<div class="w-full">
										<textarea name="closing_notes"
												  class="border p-2 w-full"
												  rows="3"
												  placeholder="Closing Notes"
												  wire:model="closing_notes.{{ $comment->id }}"></textarea>
									</div>


									<div class="text-right">

										<span class="text-grey-darker mr-2 text-xs">
											As {{ Auth::user()->name }}
										</span>

										<button type="button"
												wire:click="softDeleteToggle({{ $comment->id }})"
												class="rounded-lg bg-black text-sm text-white px-2 py-1">
											@if(!$comment->deleted_at)
												Soft Delete
											@else
												Un-Delete
											@endif
										</button>

										<button type="button"
												wire:click="reopenComment({{ $comment->id }})"
												class="{{ ($comment->closed_at) ? 'visible' : 'hidden' }} rounded-lg bg-black text-sm text-white px-2 py-1">
											Re-Open
										</button>

										<button class="rounded-lg bg-black text-sm text-white px-2 py-1">
											Set as Closed
										</button>

									</div>

								</form>

							</div>

						</div>

					</div>

				</div>

			@endif


		</div>

	</div>


	<div class="text-right {{ (!$loop->last) ? 'border-b-2' : '' }}">

		<div class="{{ (!$comment->closed_at) ? 'visible' : 'hidden' }} flex">

			<div class="flex-grow text-right pr-2 text-grey-dark text-xs pt-1 italic">
				Agree?
			</div>

			<div class="cursor-pointer px-3 py-1 text-sm border-r hover:bg-orange-lightest"
				 wire:click="vote({{ $comment->id }}, 'up')">
				<i class="fas fa-thumbs-up text-blue"></i>
				<span class="ml-1 text-xs text-grey-dark">
					{{ number_format($comment->up) }}
					@if($comment->upVoteMe)
						including you
					@endif
				</span>
			</div>

			<div class="cursor-pointer px-3 py-1 text-sm hover:bg-orange-lightest"
				 wire:click="vote({{ $comment->id }}, 'down')">
				<i class="fas fa-thumbs-down text-red"></i>
				<span class="ml-1 text-xs text-grey-dark">
					{{ number_format($comment->down) }}
					@if($comment->downVoteMe)
						including you
					@endif
				</span>
			</div>

		</div>

	</div>

</div>