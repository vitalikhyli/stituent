@extends(Auth::user()->team->app_type.'.base')

@section('title')
   	User Uploads
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>User Uploads</b>
@endsection

@section('styles')
	@livewireStyles
@endsection

@section('main')


	<div class="text-3xl font-bold border-b-4 pb-2">
		My Uploads

		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">
			<div class="text-center m-8">

				@livewire('user-upload-to-master.new-user-upload', ['upload_id' => null])
								

			</div>
		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">User Uploads</span> lets you import data files and integrate them with your Community Fluency database.
			</div>
		</div>
	</div>

	


<!-- 	<div class="text-2xl font-bold border-b-4 pb-2 mt-6">
        Uploads
    </div> -->

    @if($uploads->first())

    <div x-data="{ open: false, id: null, filename: null }">

	    <table class="table text-sm">
	        <tr class="bg-grey-lightest uppercase text-sm text-grey-darker">

	            <td>Uploaded</td>
	            <td>File Name</td>
	            <td  class="text-right">File Size</td>
	            <td  class="text-right"># Records</td>
	            <td class="bg-blue-lightest border-l text-center">Uploaded</td>
	            <td class="bg-blue-lightest border-l text-center">Imported</td>
				<td class="bg-blue-lightest border-l text-center">Matched</td>
	            <td class="bg-blue-lightest border-l text-center">Integrated</td>
	            <td class="bg-blue-lightest border-l text-center">&nbsp;</td>
	        </tr>
	        @foreach ($uploads as $upload)

		        <tr class="border-b">

		            <td class="text-grey-dark">
		            	@if(\Carbon\Carbon::parse($upload->created_at)->isToday())
							today at {{ \Carbon\Carbon::parse($upload->created_at)->format("g:i a") }}
		            	@else
		            		{{ \Carbon\Carbon::parse($upload->created_at)->format("n/j/y") }}
		            	@endif
		            </td>
		            <td>
						<a href="/campaign/useruploads/{{ $upload->id }}/latest">
							{{ (!$upload->name) ? 'Unnamed' : $upload->name }}
						</a>
					</td>
		            <td class="text-right">
		            	@if($upload->file_size < 1000)
		            		< 1 k
		            	@else
		            		{{ number_format(round(($upload->file_size / 1000))) }} k
		            	@endif
		            </td>
		            <td class="text-right font-bold">
		            	{{ number_format($upload->count) }}
		            </td>
		            <td class="text-center">
		            	<i class="fas fa-check-circle text-blue"></i>
		            </td>
		            <td class="text-center">
		            	@if($upload->count == $upload->imported_count)
		            		<i class="fas fa-check-circle text-blue"></i>
		            	@endif
		            </td>
		            <td class="text-center">
		            	@if($upload->count == $upload->matched_count)
		            		<i class="fas fa-check-circle text-blue"></i>
		            	@endif
		            </td>
		            <td class="text-center">
		            	@if($upload->count == $upload->integrated_count)
		            		<i class="fas fa-check-circle text-blue"></i>
		            	@endif
		            </td>

		            <td class="text-center">

					    <div @click="open=true;id={{ $upload->id }};filename='{{ addslashes($upload->name) }}'"
					         class="text-xs text-red text-center cursor-pointer">
		            		Delete
		            	</div>

		            </td>

		        </tr>

	        @endforeach

	    </table>

			<!-- MODAL / This example requires Tailwind CSS v2.0+ -->
		    <div x-show="open"
		         x-cloak
		         x-transition:enter="transition ease-out duration-500"
		         x-transition:enter-start="opacity-0 transform"
		         x-transition:enter-end="opacity-100 transform"
		         x-transition:leave="transition ease-in duration-200"
		         x-transition:leave-end="opacity-0 transform"
		         class="fixed pin-t pin-l ml-8 z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title"
		         role="dialog" aria-modal="true">

		      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
	
		        <div class="fixed inset-0 bg-gray-dark bg-opacity-75 transition-opacity" aria-hidden="true"></div>

		        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

		        <div class="inline-block align-bottom bg-white border-4 border-grey-darker rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
		          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
		            <div class="sm:flex sm:items-start">
		              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-lighter sm:mx-0 sm:h-10 sm:w-10">
		                <!-- Heroicon name: outline/exclamation -->
		                <svg class="h-6 w-6 text-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
		                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
		                </svg>
		              </div>
		              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
		                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
		                  Delete <span x-html="filename"></span> (File #<span x-html="id"></span>)?
		                </h3>
		                <div class="mt-2">
		                  <p class="text-gray-500">
		                    Please confirm that you would like to delete this upload.
		                  </p>
		                </div>
		              </div>
		            </div>
		          </div>
		          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

		            <a href=""
		               x-bind:href="'/{{ Auth::user()->team->app_type }}/useruploads/'+id+'/delete'">

		                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue text-base font-medium text-white hover:bg-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red sm:ml-3 sm:w-auto sm:text-sm">
		                  Yes
		                </button>

		            </a>

		            <button @click="open=false"
		                    type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
		              Cancel
		            </button>

		          </div>
		        </div>
		      </div>
		    </div>
		    <!-- END MODAL -->

	    </div>

    @endif

@endsection


@section('javascript')

 	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#add-tag').on('shown.bs.modal', function () {
			    $('#name').focus();
			})  

		});

	</script>

@endsection