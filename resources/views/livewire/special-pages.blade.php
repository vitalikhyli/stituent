<div>
	<div class="flex mb-2 mt-16">
		<div class="w-2/3 border-b-2">
			<div class="float-right text-right text-gray-400 pr-8 text-sm">
				<span wire:click="sort('recent')"
					class="pr-2 hover:text-black transition cursor-pointer
					@if ($sort == 'recent')
						font-bold text-black
					@endif
					">
					Recent
				</span>
				<span wire:click="sort('top')"
					class="pr-2 hover:text-black transition cursor-pointer
					@if ($sort == 'top')
						font-bold text-black
					@endif
					">
					Top
				</span>
			</div>
			<div class="text-gray-800 uppercase font-black pb-1">
				Reviewing Other Ideas
			</div>
		</div>
		<div class="w-1/3 border-b-2">
			<div class="text-gray-800 uppercase font-black pb-1">
				Adding a new idea
			</div>
		</div>
	</div>
    <div class="flex" wire:poll.10000ms>
    	<div class="w-2/3 pr-8">
    		@foreach ($special_pages as $sp)
    			<div class="flex w-full hover:bg-gray-50 transition">
    				<div class="text-3xl mr-4 cursor-pointer p-2 w-12 text-center">

    						<i wire:click="toggleStar({{ $sp->id }})" class="fa fa-star

	    						@if($sp->starred) 
	    							hover:text-red-500 text-yellow-500 
	    						@else
	    							text-gray-200 hover:text-yellow-500
	    						@endif
	    						transition"></i>
    					@if ($sp->user_id == Auth::user()->id)
    						<div wire:click="edit({{ $sp->id }})"
    							  class="text-xs text-gray-400 transition hover:text-blue-500">Edit</div>
    					@endif
    				</div>
	    			<div class="py-2 text-gray-500 w-full">
	    				@if ($edit_id != $sp->id)
		    				<div class="w-full">
			    				<div class="float-right text-right">
			    					@if ($sp->stars)
				    					@foreach ($sp->stars as $star)
				    						<i class="fa fa-star text-yellow-500"></i>
				    					@endforeach
			    					@endif
			    				</div>
			    				<b class="text-gray-900">{{ $sp->name }}</b>
			    			</div>
			    			<div class="float-right">
			    				<i class="text-xs text-gray-400">{{ $sp->created_at->diffForHumans() }}</i>
			    			</div>
			    			{{ $sp->description }}
		    			@else
		    				<div class="text-gray-800 w-3/4">
			    				<input type="text" wire:model="edits.name" class="mb-2 p-2 border-2 w-full block font-bold text-black"/>
			    				<textarea wire:model="edits.description" class="mb-2 p-2 border-2 w-full block"></textarea>
			    				<div class="flex w-full">
			    					<div class="w-1/2">
			    						<button wire:click="delete({{ $sp->id }})" class="text-sm text-red-400 text-center cursor-pointer px-4 py-2 hover:text-red-600 transition">Delete</button>
			    					</div>
				    				<div class="w-1/2 text-right">
				    					
				    					<button wire:click="save({{ $sp->id }})" class="text-gray-400 text-center cursor-pointer px-4 py-2 hover:text-gray-600 transition">Cancel</button>

				    					<button wire:click="save({{ $sp->id }})" class="bg-blue-500 text-center text-white cursor-pointer px-4 py-2 rounded hover:bg-blue-600 transition">Save</button>
				    					

				    				</div>
				    			</div>
			    			</div>
		    			@endif
		    			
		    		</div>
		    	</div>
    		@endforeach
    	</div>
    	
    	<div class="w-1/3">
    		<div class="">
    			<div class="text-sm text-gray-500">
    				Any user can submit an idea for a new page on Community Fluency. Other users can then <i class="fa fa-star text-yellow-500"></i> their favorite ideas. Community Fluency staff will review these ideas for feasibility. 
    			</div>
    		</div>
    		<div class="w-full p-4 border-2 rounded-lg shadow mt-2">
	    		<div class="w-full">
		    		<label class="w-full" for="new_name">
		    			New Page Idea<br>
			    		<input id="new_name" autocomplete="off" type="text" wire:model.debounce.1000ms="new_name" 
			    			   class="border-2 p-4 w-full bg-gray-50" />
			    	</label>
			    </div>
			    <div class="w-full">
			    	<label class="w-full" for="new_description">
		    			Details<br>
		    			<textarea id="new_description" wire:model.debounce.1000ms="new_description"
		    					  class="border-2 p-4 w-full h-48"></textarea>
		    		</label>
		    	</div>
		    	<div class="w-full flex items-center">
		    		<!-- <div class="w-1/2 text-center">
			    		<label class="w-full hidden" for="new_anonymous">
				    		<input id="new_anonymous" disabled checked type="checkbox" wire:model="new_anonymous" />
				    		Anonymous
				    	</label>
				    </div> -->
				    <div class="w-full">
				    	<div class="bg-blue-300 
				    		@if ($new_name && $new_description)
				    			hidden
				    		@else
				    			block
				    		@endif
				    		text-center text-white px-4 py-2 rounded">
				    		Add New
				    	</div>

				    	<div class="bg-blue-500 text-center 
				    		@if ($new_name && $new_description)
				    			block
				    		@else
				    			hidden
				    		@endif
				    		text-white cursor-pointer px-4 py-2 rounded hover:bg-blue-600 transition"
				    		wire:click="addNew()">
				    		Add New
				    	</div>
				    </div>
			    </div>
			</div>
			<div class="">
				@if ($deleted->count() > 0)
				<div class="mt-4 font-bold text-gray-800">
					Deleted Ideas
				</div>
				@endif
				@foreach ($deleted as $dsp)
					<div class="flex text-gray-500 p-2 w-full">
						<div class="w-2/3">
							{{ $loop->iteration }}. {{ $dsp->name }}
						</div>
						<div class="text-right w-1/3">
							<button wire:click="restore({{ $dsp->id }})" class="text-sm text-blue-400 text-center cursor-pointer px-4 hover:text-blue-600 transition">Restore</button>
						</div>
					</div>
				@endforeach
			</div>
    	</div>
    </div>
</div>
