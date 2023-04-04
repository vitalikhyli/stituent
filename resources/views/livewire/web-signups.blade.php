<div>
	@if ($webform->trashed())

		<div class="mt-8 font-bold">
			{{ $webform->name }} (deleted {{ $webform->deleted_at->format('n/j/Y g:ia') }})
			<div wire:click="restore()" class="text-red-500 cursor-pointer">
				Restore
			</div>
		</div>

	@else

		<div class="flex font-bold pb-2 border-b-4 mt-8 w-full">
			<div class="w-1/3 text-xl font-bold ">
				{{ $webform->name }}
			</div>
			<div class="w-2/3 pl-4 mt-1">
				<div wire:click="delete()" class="float-right text-red-500 cursor-pointer">
	    			Delete Form
	    		</div>
				Signups
			</div>
		</div>
	    
	    <div class="flex w-full">
	    	<div class="w-1/3 bg-gray-50 p-4 mt-4 shadow">
	    		
	    		<div class="text-lg mb-2 border-b-4 border-blue-500 font-bold text-blue-500">
		    		1. Set Volunteer Options
		    	</div>
	    		<div class="flex flex-wrap uppercase text-sm">
			        @foreach (CurrentCampaign()->volunteer_options() as $vo)
			        	<div class="w-1/2 text-gray-400 hover:text-gray-900 transition">
			        		
				        		<input id="volunteer_{{ $vo }}" wire:model="volunteers" value="volunteer_{{ $vo }}" type="checkbox" /> 
				        	<label class="cursor-pointer" for="volunteer_{{ $vo }}" style="margin-bottom:0px; font-weight:normal;">
				        		{{ str_replace('_', ' ',$vo) }}
				        	</label>
			        	</div>
			        @endforeach
			    </div>

			    <div class="text-lg mt-8 mb-2 border-b-4 border-blue-500 font-bold text-blue-500">
		    		2. Set Button Text
		    	</div>
			    <div class="mt-2 text-blue-500">
		    		<input type="text" class="border-2 px-4 py-3 rounded-full w-full text-center" wire:model="webform.button" />
		    	</div>
	    		

	    		<div class="text-lg mt-8 mb-2 border-b-4 border-blue-500 font-bold text-blue-500">
		    		3. Preview
		    	</div>
	    		<div class="w-full p-4 border bg-white">
	    			<iframe width="100%" height="500" src="{{ config('app.url') }}/web-forms/{{ $webform->unique_id }}?b={{ $webform->button }}"></iframe>
	    		</div>

	    		

	    		<div class="text-lg mt-8 mb-2 border-b-4 border-blue-500 font-bold text-blue-500">
		    		4. Embed Code
		    	</div>
	    		<div class="text-gray-500 text-sm p-2 border bg-white">
	    			&lt;iframe width="100%" height="500" frameborder="0" src="{{ config('app.url') }}/web-forms/{{ $webform->unique_id }}"&gt;&lt;/iframe&gt;
	    		
	    		</div>
	    	</div>

	    	<div wire:ignore wire:poll class="w-2/3 p-4">
	    		@foreach ($webform->webSignups()->latest()->get() as $websignup)
	    			<div wire:key="{{ rand(1,999999) }}">
		    			@livewire('web-signup', ['websignup' => $websignup], key($loop->index))
		    		</div>
	    		@endforeach
	    	</div>
	    </div>

    @endif
	
</div>
