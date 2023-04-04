	<div class="inline-flex w-full">

		<div class="border-r pr-8 text-right w-5/6 pt-4">

			@if($models->first())

				<form autocomplete="off">

					<input id="search" autocomplete="off" type="text" placeholder="&#xf002; @lang('Search Files')" style="font-family:Font Awesome\ 5 Free, Arial" data-toggle="dropdown" class="w-2/3 appearance-none px-6 py-3 bg-grey-lighter border-2 border-grey text-black focus:border-2 text-lg" />

				</form>

<!-- 				<div class="text-right pl-12 py-4 text-grey-darker text-xs italic">
					Use <span class="font-bold text-black">"for:"</span> to narrow down searches with a name, as in: <span class="font-bold text-black">"MassHealth for:{{ Auth::user()->first_name }}"</span>
				</div> -->

			@endif

		</div>

		<div class="w-1/2 pt-4 pb-2">


			<button id="main_upload_file" class="rounded-lg bg-blue text-sm uppercase text-white px-4 py-2 ml-8 hover:bg-blue-dark">
				Upload New File
			</button>


			<div class="ml-8 mt-2 bg-grey-lightest p-2 mr-2 hidden border" id="upload_form">

				<form action="/{{ Auth::user()->team->app_type }}/files/upload/" method="post" enctype="multipart/form-data">
					
					@csrf

			    	<label for="fileToUpload" class="font-normal">

<!-- 			    		<button type="button" class="border bg-grey-lighter rounded-lg text-black px-4 py-1">
			    			Choose File
			    		</button>

			    		<input type="file" name="fileToUpload" id="fileToUpload" class="opacity-0 z-99 float-right absolute" />
 -->
			    		<input type="file" name="fileToUpload" id="fileToUpload" class="" />

			    	</label>

			    	<input type="hidden" value="1" name="edit_after_upload" />

				    <button name="submit" class="bg-blue rounded-lg text-white px-4 py-1">
				    	Go <span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-2"></i>
				    </button>

				</form>

			</div>


		</div>

	</div>