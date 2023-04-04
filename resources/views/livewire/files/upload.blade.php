<div class="">

	<div class="p-4 bg-grey-lightest border flex w-full">

		<!----------------------------------/ Upload Form /-------------------------------------->

		<div class="flex-grow">

			<form wire:submit.prevent="uploadFile">

				<div class="flex">


  					<input type="file"
  						   wire:model="theFile"
  						   class="border bg-white p-2 mr-2">

				    @error('theFile') <span class="error">{{ $message }}</span> @enderror

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

			</form>


			<div wire:loading wire:target="theFile">
				Uploading...
			</div>

		</div>

		<!----------------------------------/ Show Files /-------------------------------------->

		@if($the_model)

		<div class="ml-4 h-48 w-1/2 overflow-scroll" wire:loading.remove wire:target="theFile">

			<div class="font-bold border-b-2 pb-1">
				{{ $the_model->files->count() }} Files
			</div>

			@foreach($the_model->files->sortBy('name') as $file)

				<div class="py-1 border-b flex bg-white">

					<div>
						<i class="far fa-file mr-1"></i>
					</div>

					<div class="truncate flex-grow">
						<a target="_blank" href="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/download" target="new">
							{{ $file->name }}
						</a>
					</div>

					<div class="text-grey-dark w-1/4">
						{{ \Carbon\Carbon::parse($file->created_at)->toDateString() }}
					</div>

				</div>

			@endforeach

		</div>
		@endif


	</div>




</div>
