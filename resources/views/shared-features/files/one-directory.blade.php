<div class="w-full mb-2">
	<div class="group p-1 border-b-2 w-full border-blue text-blue hover:bg-orange-lightest">
	
		<i class="w-6 fa fa-folder text-xl mr-2 text-center"></i>
		{{ $dir->name }}



		<div class="float-right">
			@if(!$dir->open)
			
				<a href="?open={{ $dir->id }}">
					<i class="fas fa-plus-circle"></i>
				</a>
			
			@else
				<a href="?close={{ $dir->id }}">
					<i class="fas fa-minus"></i>
				</a>
			@endif
		</div>

		<div class="opacity-0 group-hover:opacity-100 float-right mr-2">
	        <button type="button" data-id="{{ $dir->id }}" class="add-submodel bg-grey-light px-2 py-1 rounded-lg text-black hover:text-black text-xs">
                New
            </button>
        </div>

		<div class="opacity-0 group-hover:opacity-100 float-right mr-2">
			<a href="/{{ Auth::user()->team->app_type }}/files/directories/{{ $dir->id }}/edit">
		        <button type="button" data-id="{{ $dir->id }}" class="bg-grey-light px-2 py-1 rounded-lg text-black hover:text-black text-xs ml-2">
	                Edit
	            </button>
        	</a>
        </div>

		<div class="float-right">

	        <button type="button" data-id="{{ $dir->id }}" class="opacity-0 group-hover:opacity-100  upload-file bg-grey-light text-black hover:text-black px-2 py-1 rounded-lg text-xs ml-2">
                Upload
            </button>

        </div>

		<div class="float-right">

	        <button type="button" data-id="{{ $dir->id }}" class="opacity-0 group-hover:opacity-100  select-mode bg-grey-light text-black hover:text-black px-2 py-1 rounded-lg text-xs">
                Select
            </button>

        </div>
        
		<div class="float-right bg-grey-light px-2 mr-2 shadow hidden" id="add-submodel-form-{{ $dir->id }}">

				<form action="/{{ Auth::user()->team->app_type }}/files/directories/add" method="post" class="flex">
                     @csrf
                   	<input type="type" name="name" class="rounded-lg px-2 py-1 border font-bold m-2 w-5/6" placeholder="New Directory Name"  id="add-submodel-form-input-{{ $dir->id }}" />
                   	<input type="hidden" name="parent_id" value="{{ $dir->id }}" />
                   	<div class="pt-2">
                		<button type="submit" class="rounded-lg bg-blue text-white px-2 py-1">
                    	Save
                	</button>
                	</div>
               </form>

        </div>

		<div class="float-right bg-grey-light p-2 mr-2 shadow hidden" id="upload-form-{{ $dir->id }}">

			<form action="/{{ Auth::user()->team->app_type }}/files/upload/{{ base64_encode(json_encode(['directory_id' => $dir->id])) }}" method="post" enctype="multipart/form-data">
				
				@csrf

		    	<label for="fileToUpload_{{ $dir->id }}" class="font-normal">
		    		
<!-- 		    		<button type="button" class="bg-grey rounded-lg text-black px-4 py-1">
		    			Choose File
		    		</button>
		    		<input type="file" name="fileToUpload" id="fileToUpload_{{ $dir->id }}" class="opacity-0 z-99 float-right absolute" />
 -->
		    		<input type="file" name="fileToUpload" id="fileToUpload_{{ $dir->id }}" class="" />

		    	</label>

			    <button name="submit" class="bg-blue rounded-lg text-white px-4 py-1">
			    Go <span id="file_selected_{{ $dir->id }}" class="font-bold"></span><i class="fas fa-file-upload ml-2"></i>
			    </button>

			</form>

		</div>


        	<button type="button" data-id="{{ $dir->id }}" class="move-here hidden bg-blue text-white px-2 py-1 rounded-lg text-xs ml-2">
                <i class="fas fa-arrow-left"></i> Move Selected Here
            </button>

	</div>
	<div class="mt-1 ml-1 w-full">
		@if($dir->open)

			<div class="table">
				@foreach($dir->files as $thefile)

					@include('shared-features.files.one-file', compact($thefile))

				@endforeach
			</div>

			@foreach($dir->subModels() as $thesub)
			    <div class="pl-8 mt-4 w-full">
			    	@include('shared-features.files.one-directory', ['dir' => $thesub])
			    </div>
			@endforeach

		@endif
	</div>


</div>


