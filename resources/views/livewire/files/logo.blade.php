<div>
	
    @if($display && Auth::user()->team->logo_img)

    	<div class="text-center -mt-2">

	    	<img class=""
	    		 style="max-height:200px;"
	    		  src="{{ config('app.url').'/storage/user_uploads/logos_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT).'/'.Auth::user()->team->logo_img }}"
	    		  wire:click="displayFalse()" />

	    </div>

    @elseif(!Auth::user()->team->logo_img)

    	@if(Auth::user()->permissions->admin || Auth::user()->permissions->developer)

    		@if(!$formMode)
				<div class="text-grey-dark text-right cursor-pointer text-sm py-1"
					 wire:click="$toggle('display')"
					 wire:key="set-logo">
					Set Logo
				</div>
			@endif

		@endif

    @endif

    @if(!$display || $formMode)

		<div class="p-4 bg-grey-lightest border flex w-full">

			<div class="w-1/2 pr-4">

				<div class="mb-4 text-sm text-grey-dark pr-2 mt-2">

					<i class="fas fa-info-circle text-blue mr-1"></i> Use this form to add your own logo to your front page. You will also be able to use this image elsewhere, for instance when generating a case report.

				</div>

				<form wire:submit.prevent="uploadFile">

					<div class="flex">

						<div class="font-bold py-2 pr-4 whitespace-no-wrap">Upload New:</div>

	  					<input type="file"
	  						   wire:model="theFile"
	  						   class="border bg-white p-2 mr-2 w-4/5">

					    @if($theFile)
						    <div class="p-1">
							    <button type="submit"
							    		class="rounded-lg px-2 py-1 bg-blue text-white"
							    		>
							    		Upload
							    </button>
							</div>
						@endif

					</div>

				    @error('theFile')
				    	<div class="text-red font-bold py-2">
				    		<i class="fas fa-exclamation-triangle"></i> {{ $message }}
				    	</div>
				    @enderror

				</form>


				<div wire:loading wire:target="theFile">
					Uploading...
				</div>

			</div>

			<div class="w-1/2">

				@if($confirmDelete)

					<div class="absolute z-10 bg-white p-4 border-2 border-black -ml-4 mr-2">

						<div>
							Are you sure you want to delete <span class="font-bold">{{ substr($confirmDelete, 11) }}?</span>
						</div>

						<div class="pt-2 border-t mt-4 text-right">

							<button class="rounded-lg bg-blue text-grey-lightest px-4 py-1 border border-grey-dark mr-2"
									wire:click="deleteIsConfirmed('{{ $confirmDelete }}')">
								Yes
							</button>

							<button class="rounded-lg bg-grey-lighter text-grey-darker px-4 py-1 border border-grey-dark"
									wire:click="$set('confirmDelete', '')">
								No
							</button>

						</div>

					</div>

				@endif


				<div class="font-bold py-2">Choose Existing:</div>

				@foreach($logos as $file)

					<div class="py-1 px-2 border-b flex bg-white hover:bg-blue-lightest cursor-pointer">

						<div>

							
							<button class="rounded-lg bg-grey-lightest text-blue px-2 py-1 mr-2 whitespace-no-wrap"
									wire:click="setLogo('{{ $file }}')">
								Choose <i class="far fa-file mx-2"></i>
							</button>

							
						</div>

						<div class="truncate flex-grow">
							
							{{ substr($file, 11) }}

						</div>

						<div class="pl-8 text-xs">
							
							<button class="rounded-lg bg-grey-lightest text-red px-2 py-1"
									wire:click="$set('confirmDelete', '{{ $file }}')">
								Delete
							</button>

						</div>

					</div>

				@endforeach

					<div class="py-1 px-2 border-b flex bg-white hover:bg-blue-lightest hover:text-white cursor-pointer"
						 wire:click="setLogo('')">

						<div>
							<i class="fa fa-times text-red mr-1"></i>
						</div>

						<div class="truncate flex-grow text-red">
							
							No Logo

						</div>

					</div>

			</div>

		</div>

    @endif
</div>
