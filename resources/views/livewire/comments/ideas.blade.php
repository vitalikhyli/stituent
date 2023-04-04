<div>

	<div x-data="{ ideas: @entangle('ideasFormVisible') }">

		<div @click="ideas = true"
			 x-show="!ideas"
			 class="border bg-grey-lighter p-4 py-2 mb-4 cursor-pointer hover:bg-orange-lightest">
			<i class="fas fa-plus mr-1"></i> Add an Idea
		</div>

		<div x-show="ideas">

			<div class="bg-grey-lightest p-4 border shadow mb-4">

				<form wire:submit.prevent="addIdea">
					<div class="mb-1">
						<input type="text"
							   name="subject"
							   placeholder="Subject" 
							   class="border p-2 w-full"
							   wire:model="add_subject" />
					</div>

					@error('add_subject')
						<div class="text-red font-bold mb-2">A subject is required, and must be at least 5 characters long.</div>
					@enderror

					<div class="mb-1">
						<textarea name="notes"
								  placeholder="Notes" 
								  class="border p-2 w-full"
								  wire:model="add_notes"></textarea>
					</div>

					@error('add_notes')
						<div class="text-red font-bold mb-2">Notes are required, and must be at least 10 characters long. Please be specific about your idea.</div>
					@enderror

					<div class="text-right">

						<button @click="ideas = false"
								class="rounded-lg bg-grey-darkest hover:bg-black text-white px-3 py-1 text-sm"
								type="button">
							Cancel
						</button>


						<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
							Add Your Idea
						</button>

					</div>

				</form>

			</div>

		</div>

	</div>

	@if($comments->first())

		@foreach($comments as $comment)

			@include('livewire.comments.include-one-comment', ['comment' => $comment, 'mode' => 'open'])

		@endforeach

	@endif

	@if($closed->first())

		<div class="text-center text-white border-red bg-grey-darkest border-b-2 px-2 py-1 mt-4 rounded-t-lg">
			Answered Comments
		</div>

		<div class="bg-grey-lighter px-4 py-2">

			@foreach($closed as $comment)

				@include('livewire.comments.include-one-comment', ['comment' => $comment, 'mode' => 'closed'])

			@endforeach

		</div>

	@endif

	@if(Auth::user()->permissions->developer && $trashed->first())

		<div class="text-center text-white border-red bg-grey-darkest border-b-2 px-2 py-1 mt-4 rounded-t-lg">
			Deleted (Dev-Only)
		</div>

		<div class="bg-grey-light px-4 py-2">

			@foreach($trashed as $comment)

				@include('livewire.comments.include-one-comment', ['comment' => $comment, 'mode' => 'trashed'])

			@endforeach

		</div>

	@endif

</div>