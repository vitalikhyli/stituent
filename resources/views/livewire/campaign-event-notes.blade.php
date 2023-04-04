<div class="w-full">
	<textarea class="border-2 p-2 block w-full" rows="3" wire:model="new_note"></textarea>

    <div wire:click="addNote()" class="text-right cursor-pointer text-blue-400 text-sm font-bold hover:text-blue-500">
    	Add Note
    </div>

    @foreach ($notes as $note)

    	<div class="my-8 relative">
    		<div class="text-xs -mt-2 px-1 ml-4 absolute pin-l bg-white text-gray-400">
    			{{ $note->created_at->format('n/j/Y \@ g:ia') }}
    		</div>
    		<div class="text-xs -mt-2 px-1 mr-4 absolute pin-r bg-white text-gray-400">
    			{{ $note->created_at->diffForHumans() }}
    		</div>
	    	<div class="border-2 rounded-lg p-4 text-gray-800">
	    		{!! nl2br($note->content) !!}
	    	</div>
	    	<div class="text-right text-sm text-gray-400 pr-4">
	    		@if (Auth::user()->id == $note->user_id)
	    			<span wire:click="deleteNote('{{ $note->id }}')" class="hover:text-red-500 text-red-300 cursor-pointer mr-2 text-xs">
	    				Delete
	    			</span>
	    		@endif
	    		{{ $note->user->name }}
	    	</div>
	    </div>
    @endforeach

    @if ($trashed->count() > 0)
	    <div class="text-center border-b-2 mt-8 text-red-500">
			Deleted
		</div>
	@endif
    @foreach ($trashed as $note)
    	

    	<div class="my-8 relative opacity-50">
    		<div class="text-xs ml-4 absolute pin-l bg-white text-gray-400">
    			{{ $note->deleted_at->format('n/j/Y \@ g:ia') }}
    		</div>
	    	<div class="text-right text-sm text-gray-400 pr-4">
	    		@if (Auth::user()->id == $note->user_id)
	    			<span wire:click="restoreNote('{{ $note->id }}')" class="hover:text-red-500 text-red-300 cursor-pointer mr-2 text-xs">
	    				Restore
	    			</span>
	    		@endif
	    		{{ $note->user->name }}
	    	</div>
	    </div>
    @endforeach
</div>
