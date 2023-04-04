<div>

	<div x-data="{ endorsements: @entangle('endorsementFormVisible') }">

		@if(!$user_has_endorsed)

			<div @click="endorsements = true"
				 x-show="!endorsements"
				 class="border bg-grey-lighter p-4 py-2 mb-4 cursor-pointer hover:bg-orange-lightest"
				 wire:key="add_a_testimonial">
				<i class="fas fa-plus mr-1"></i> Add a Testimonial
			</div>

		@else

			<div class="border bg-blue-lightest p-4 py-2 mb-4 cursor-pointer text-center text-blue"
				  wire:key="thank_you">
				<i class="fas fa-thumbs-up mr-1"></i> Thanks for your Testimonial!
			</div>

		@endif


	    <div x-show="endorsements">

			<div class="bg-grey-lightest p-4 border shadow">

				<form wire:submit.prevent="addEndorsement">
				
					<div class="flex mb-2">
						<div class="w-24">
							Name:
						</div>
						<div class="flex-shrink">
							<div>
								<input type="text"
									   class="border px-2 py-1"
									   wire:model="endorsement_name" />
							</div>
						</div>
					</div>

					<div class="flex mb-4">
						<div class="w-24">
							Title:
						</div>
						<div class="flex-shrink">
							<div>
								 <input type="text"
									 	class="border px-2 py-1"
									 	wire:model="endorsement_title" />
							</div>
						</div>
					</div>

					@error('endorsement_title')
						<div class="text-red font-bold mb-2">You can be anonymous, but a Title is required.</div>
					@enderror

					<div class="mb-2">
						<textarea placeholder="What do you like about Community Fluency?"
								  rows="8"
								  class="border p-2 w-full"
								  wire:model="endorsement_notes"></textarea>

 						@error('endorsement_notes')
 							<div class="text-red font-bold mb-2">Notes are required, and must be at least 10 characters long.</div>
						@enderror

					</div>

					<div class="text-sm text-blue mb-2">
						<b>Please note</b> that all testimonials may be used in email, print or other marketing materials.
					</div>

					<div class="pt-4 text-center border-t">

						<button @click="endorsements = false"
								class="rounded-lg bg-grey-darkest hover:bg-black text-white px-6 text-sm py-2"
								type="button">
							Cancel
						</button>

						<button class="rounded-lg bg-blue hover:bg-blue-dark text-white px-6 text-sm py-2">
							Add Your Testimonial!
						</button>

					</div>

					<div class="pt-2 text-center"
						 >



					</div>

				</form>

			</div>

	    </div>

	    @if($comments->first())

			<div class="mt-8">

				<div class="text-xl text-blue text-center flex">

					<div class="flex-grow -mt-1 text-left">
						<i class="fas fa-star text-sm mr-2 opacity-50"></i>
						<i class="fas fa-star text-sm mr-2 opacity-75"></i>
						<i class="fas fa-star text-sm mr-2"></i>
					</div>

					<div>
						What Others Said
					</div>

					<div class="flex-grow -mt-1 text-right">
						<i class="fas fa-star text-sm ml-2"></i>
						<i class="fas fa-star text-sm ml-2 opacity-75"></i>
						<i class="fas fa-star text-sm ml-2 opacity-50"></i>
					</div>

				</div>

				@foreach($comments as $comment)

					<div class="{{ (!$loop->last) ? 'border-b' : '' }} py-4">

						<div class="font-bold text-lg">
							@if(!$comment->name)
								Anonymous
							@else
								{{ $comment->name }}
							@endif
							@if(Auth::user()->id == $comment->user_id)
								<button class="rounded-lg bg-blue text-white px-2 py-1 text-xs float-right">
									You!
								</button>
							@endif
						</div>
						<div class="text-grey-darker">
							{{ $comment->title }} 
							@if($comment->state != strtoupper(Auth::user()->team->account->state))
								({{ $comment->state }})
							@endif
						</div>
						<div class="text-grey-dark border-l-4 pl-4 mt-1">
							"{!! nl2br($comment->notes) !!}"
						</div>

					</div>

				@endforeach

			</div>

		@endif

	</div>


</div>