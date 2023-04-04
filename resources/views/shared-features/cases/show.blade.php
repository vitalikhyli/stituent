@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Case: {{ $thecase->subject }}
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/cases/">Cases</a>
    > &nbsp;<b>{{ $thecase->subject }}</b>

@endsection

@section('style')

	@livewireStyles

	<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<div class="text-grey-dark">


	<div class="text-3xl font-sans pb-3 text-black border-b-4">


		<div class="float-right text-sm px-4 py-2 mt-1 font-bold">
			<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/edit">
				Edit
			</a>
		</div>
		

		<div class="float-right text-right text-sm py-2 mr-6 mt-1">
	      <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/print" target="new">
	      	<i class="fa fa-print mr-1"></i>
	      	Print Report
	      </a>
	    </div>

	@if(Auth::user()->permissions->developer)

		<!-- <div class="float-right text-sm mt-2">

	      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white text-red text-center ml-2"/>
	        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Case
	      </button>

	  	</div> -->

  	@endif

  		<div class="font-bold">

			
			<i class="fa fa-folder-open mr-2"></i>
			
			{{ $thecase->subject }}

			@if($thecase->resolved)
				<i class="fas fa-check ml-2 text-blue">Resolved</i> 
			@endif
		</div>

	</div>

	<div class="p-2 text-grey-dark text-sm mb-6 w-full mt-2">

		@if ($thecase->sharedCases()->count() > 0)
		<div class="float-right">
			This case is <span class="font-bold text-blue">shared</span> with
			<span class="font-bold text-blue">
				{{ $thecase->sharedCases->implode('name', ', ') }}
			</span>
		</div>
		@endif

		<span class="text-blue-light">
			@include('shared-features.cases.activity-icons')
		</span>

	</div>


	<div class="flex flex-wrap mt-4">

		<div class="w-3/5 pr-6">
			<div class="flex">
				<div class="w-32 font-bold text-sm text-grey-dark pl-2 mb-2 uppercase">
					Case Info

				</div>
				<div class="text-sm pl-2">
					
				</div>
			</div>

			<table class="text-sm w-full border-b">

				
				
			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Type / Subtype

			        </td>
			        <td class="p-2">

			        	@if ($thecase->type)
			        		<b>{{ $thecase->type }}</b>
			        	@endif
			        	@if ($thecase->subtype)
			        		/ {{ $thecase->subtype }}
			        	@endif

			        </td>
			    </tr>
			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Description

			        </td>
			        <td class="p-2">

			        	<div x-data="{ open: false }">

			        		<div class="float-right uppercase text-xs font-bold">
			        			@if($thecase->priority == "High")
									<span class="bg-red-light text-white px-2 py-1">High Priority</span>
								@elseif($thecase->priority == "Medium")
									<span class="bg-blue-light text-white px-2 py-1">Medium Priority</span>
								@elseif($thecase->priority)
									<span class="bg-green-light text-white px-2 py-1">{{ $thecase->priority }} Priority</span>
								@endif
							</div>

							@php

								$cutoff = 80;
								$words = explode(" ", $thecase->notes);
								$first_part = implode(" ", array_splice($words, 0, $cutoff));
								$other_part = implode(" ", array_splice($words, $cutoff));

							@endphp	

							{!! nl2br($first_part) !!}

							@if(count($words) >= $cutoff)

							    <button @click="open = true" x-show="!open"
							    		class="rounded-lg px-2 py-1 bg-blue text-white text-xs ml-2">
							    	Show More
							    </button>

							    <span x-show="open">{!! nl2br($other_part) !!}</span>

								<button @click="open = false" x-show="open"
							    		class="rounded-lg px-2 py-1 bg-blue text-white text-xs ml-2">
							    	Show Less
							    </button>

							@endif

						</div>

			        </td>
			    </tr>
			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Opened

			        </td>
			        <td class="p-2">
							{{ $thecase->created_at->format('D n/j/Y \a\t g:ia') }}
						({{ $thecase->created_at->diffForHumans() }})
					</td>
				</tr>
			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            People

			        </td>
			        <td class="p-2 pb-0 text-sm">
			        	@livewire('connector', [
											'class' => 'App\Person',
											'model' => $thecase,
											'show_linked' => true
											])
			        </td>
			    </tr>
			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Households

			        </td>
			        <td class="p-2 text-sm">
			        	@livewire('connector-households', ['model' => $thecase])
			        </td>
			    </tr>


			    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Organizations

			        </td>
			        <td class="p-2 text-sm">
			        	@livewire('org-case', ['case_id' => $thecase->id])
			        </td>
			    </tr>
			    
			    
			</table>
			
		</div>

		<div class="w-2/5">
			<div class="font-bold text-sm text-grey-dark pl-2 mb-2 uppercase">
				Office
			</div>
			<div>

				<table class="text-sm w-full border-b">

					<tr class="border-t">
				        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
				            Assigned To

				        </td>
				        <td class="p-2 text-sm">
				        	<i class="fas fa-user mr-1 text-grey-darkest"></i>
							{{ $thecase->assignedTo()->name }}
								
								<a class="px-2 rounded-lg" data-toggle="collapse" href="#assign_person" role="button" aria-expanded="false" aria-controls="assign_person">
									<button class="float-right">
									Change
									</button>
								</a>

							@if(session('assignment'))
								
									<div class="bg-grey-lighter rounded-lg p-4 text-center mt-2">
										Send notification to {{ $thecase->assignedTo()->name }} ({{ $thecase->assignedTo()->email }})?

										<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/notify_user/{{ $thecase->assignedTo()->id }}">
											<button class="rounded-lg bg-blue text-white px-2 py-1 m-2 shadow">Yes</button>
										</a>

									</div>
							@endif

							@if(session('assignment_error'))
								<div class="bg-red text-white rounded-lg p-4 text-center mt-2">
									<i class="fas fa-exclamation-circle"></i> Error in sending notification to {{ $thecase->assignedTo()->email }}
								</div>
							@endif

							@if(session('assignment_success'))
								<div class="bg-blue text-white rounded-lg p-4 text-center mt-2">
									<i class="fas fa-check-circle"></i> Notification sent to {{ $thecase->assignedTo()->email }}
								</div>
							@endif

							<div id="assign_person" class="collapse w-full">
							@foreach($thecase->notAssignedTo() as $theuser)
								<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/assign_user/{{ $theuser->id }}">
									<button class="rounded-lg border px-3 py-2 text-base mr-2 w-full mb-1 shadow hover:bg-blue hover:text-white">
										{{ $theuser->name }}
									</button>
								</a>
							@endforeach
							</div>
				        </td>
				    </tr>


					<tr class="border-t">
				        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
				            Opened By

				        </td>
				        <td class="p-2">
								{{ $thecase->user->short_name }}, {{ $thecase->user->team->name }}
						</td>
					</tr>
				    <tr class="border-t">
				        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
				            Sharing

				        </td>
				        <td class="">
				        	@livewire('case-share', ['case' => $thecase])
				        </td>
				    </tr>
				    <tr class="border-t">
			        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
			            Status

			        </td>
			        <td class="p-2 text-sm">

					    <div class="w-full flex cursor-pointer">
							<div class="w-1/3 text-center border-r">
								@if($thecase->open)
									<span class="font-extrabold uppercase text-blue-dark">Open</span>
								@else
									<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/markas/open" class="text-grey-dark">Open</a>
								@endif
							</div>

							<div class="w-1/3 text-center border-r">
								@if($thecase->held)
									<span class="font-extrabold uppercase text-blue-dark">Held</span>
								@else
									<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/markas/held" class="text-grey-dark">Held</a>
								@endif
							</div>

							
							@if($thecase->resolved)
								<div class="w-1/3 text-center px-2 bg-black py-2 rounded-t-lg">
									<span class="text-grey-lightest ">Resolved</span>
								</div>
							@else
								<div class="w-1/3 text-center">
									<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/markas/resolved" class="text-grey-dark">Resolved</a>
								</div>
							@endif
							
						</div>



						@if($thecase->resolved || $thecase->closing_remarks)

							<div id="show_closing_remarks" class="text-grey-lightest bg-black p-2 pr-3 w-full">
								
								<button id="edit_closing_remarks_button" class="rounded-lg bg-red float-right text-white px-2 py-1 mt-2">
									{{ ($thecase->closing_remarks) ? 'Edit' : 'Add' }}
								</button>

								<div class="mb-2">
									<i class="fas fa-check-circle ml-1 mr-2 mt-3"></i>
									<span class="font-semibold">Closing Remarks</span>
								</div>

								<div class="p-2">
									{!! nl2br($thecase->closing_remarks) !!}
								</div>

							</div>

							<div id="edit_closing_remarks" class="w-full bg-black hidden">

								<div class="text-white px-2">
									<i class="fas fa-check-circle mr-2 mt-3"></i>
									<span class="font-semibold">Closing Remarks</span>
								</div>

								<form action="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/closingremarks/update" method="post">
									@csrf

									<div class="px-2 pt-2">
										<textarea name="closing_remarks" rows="7" class="w-full text-grey-lightest bg-grey-darkest border-white border p-2" placeholder="Write closing remarks here">{!! $thecase->closing_remarks !!}</textarea>
									</div>

									<div class="text-right p-2">
										<button type="submit" class="rounded-lg bg-red text-white px-2 py-1">
											Update
										</button>
									</div>

								</form>
							</div>

						@endif
					</td>
				</tr>

				</table>
				
			</div>
		</div>

	</div>

	



	<div class="flex mt-2">

		<div class="w-3/5 pr-6">

			<div class="text-xl text-black font-sans pb-2 font-black mt-8 bg-white">
				    ADD NOTE
				</div>

			<div class="border-4 rounded-lg p-4">

				
				@if($errors)

					@include('elements.errors')

				@endif

				<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/add_contact">
				@csrf

					<table class="text-base w-full">
		                <tr class="">
		                	<td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-top w-1/6 whitespace-no-wrap">
		                        Linked To
		                    </td>
		                	<td>
		                		<div id="select_people" class="flex flex-wrap">

	                            	@foreach ($thecase->people()->notHouseholds()->get() as $person)

		                            	<div class="whitespace-no-wrap mr-1">
			                                <label class="cursor-pointer font-normal rounded-full px-2 text-sm bg-white">
			                                    <input type="checkbox" name="include_people[]" value="{{ $person->id }}" checked />
			                                    {{ $person->name }}
			                                </label>
			                            </div>

	                                @endforeach

		                        </div>
		                    </td>
		                </tr>
		                <tr class="">
		                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
		                        Type
		                    </td>
		                    <td class="p-1 flex">
		                        <select name="type" class="form-control whitespace-no-wrap w-3/6">

		                             @foreach(Auth::user()->team->contactTypes() as $key => $type)

		                                <option value="{{ $type }}">{{ ucwords($type) }}</option>
		                                
		                             @endforeach

		                         </select>
		                    </td>
		                </tr>
		                <tr class="">
		                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
		                        Date
		                    </td>
		                    <td class="p-1 flex">
		                        <input name="date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" class="bg-grey-lightest datepicker border-2 rounded-lg px-4 py-2 w-1/3"
		                        value="{{ $errors->any() ? old('date') : '' }}" />

		                        <a class="px-2 py-1 rounded-full mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
		                        <div id="show_time_div" class="hidden">
		                            <input name="time" value="{{ \Carbon\Carbon::now()->format('h:i A') }}" class="border-2 rounded-lg px-4 py-2 w-32"/>
		                            <input type="hidden" value="0" name="use_time" id="use_time" />
		                        </div>
		                    </td>
		                </tr>

		                <tr class="">
		                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
		                        Subject
		                    </td>
		                    <td class="p-1">
		                        <input id="contact_subject" name="subject" rows="5" type="text" class="bg-grey-lightest border-2 rounded-lg px-4 py-2 w-full"
		                        value="{{ $errors->any() ? old('subject') : '' }}" />
		                    </td>
		                </tr>
		                <tr class="">
		                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
		                        Notes
		                    </td>
		                    <td class="p-1">
		                        <textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"
		                        onkeydown="myfunc(event)"
		                        >{{ $errors->any() ? old('notes') : '' }}</textarea>
		                    </td>
		                </tr>

		                <tr class="">
		                    <td></td>
		                    <td class="align-middle">

		                        <div id="set_follow_up_div" class="hidden my-2 p-1">

		                            <input type="checkbox" 
		                            autocomplete="off"
		                            name="person_followup"
		                            id="person_followup" />

		                            <i class="fas fa-hand-point-right ml-2"></i>

		                            <span class="text-sm">Follow Up on this Date</span>

		                            <input type="text" 
		                            name="person_followup_on" 
		                            placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" 
		                            class="datepicker border px-2 py-1 rounded-lg" />

		                        </div>



		                        <div class="flex w-full text-sm items-center py-2">

		                            
		                        	<div class="w-1/2">
			                            <a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>
			                        </div>

		                            <div class="w-1/3">
										<label class="checkbox cursor-pointer inline">
											<input type="checkbox" 
												   name="private"
												   autocomplete="off">
											
											<i class="fa fa-lock text-grey-darker" ></i> <span class="font-normal">Only me / admins</span>
											
										</label>
									</div>

									<div class="w-1/6">
										<input type="submit" class=" float-right shadow cursor-pointer hover:bg-blue-dark rounded-full py-2 px-3 text-sm bg-blue text-white" value="Save" />
									</div>

		                        </div>

		                    </td>
		                </tr>
		            </table>


		        </form>
		    </div>
		



			<!-- <form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/add_contact">
				@csrf	
			<table class="text-base w-full border-t">
					<tr class="border-b">
						<td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
							Date
						</td>
						<td class="p-2 flex">
							<input name="date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" class="datepicker border-2 rounded-lg px-4 py-2 w-1/3"/>

							<a class="px-2 py-1 rounded-lg mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
							<div id="show_time_div" class="hidden">
								<input name="time" value="{{ \Carbon\Carbon::now()->format("h:i A") }}" class="border-2 rounded-lg px-4 py-2 w-32"/>
								<input type="hidden" value="0" name="use_time" id="use_time" />
							</div>
						</td>
					</tr>
					<tr class="border-b">
						<td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
							Desc
						</td>
						<td class="p-2">
							<textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
						</td>
					</tr>
					<tr class="">
						<td class="p-2 align-middle" colspan="2">


							<div id="set_follow_up_div" class="hidden my-2 bg-orange-lightest p-2 border rounded-lg">

								<input type="checkbox" 
									   autocomplete="off"
									   name="person_followup"
									   id="person_followup" />

								<i class="fas fa-hand-point-right text-red ml-2"></i>

								<span class="text-sm">Follow Up on this Date</span>

								<input type="text" 
									   name="person_followup_on" 
									   placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" 
									   class="datepicker border px-2 py-1 rounded-lg" />

							</div>


							<div class="-mt-2 pt-3 pb-2 mb-1 text-sm border-b">

								<input type="submit" class=" -mt-1 ml-4 float-right shadow cursor-pointer hover:bg-blue-dark rounded-lg py-1 px-3 text-sm bg-blue text-white" value="Add Note" />

								<a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>

								<div class="ml-2 float-right">
									<label class="checkbox cursor-pointer inline">
										<input type="checkbox" 
											   name="private"
											   autocomplete="off">
										
										<i class="fa fa-lock text-grey-darker" ></i> <span class="font-normal">Only me / admins</span>
										
									</label>
								</div>

							</div>


						</td>
					</tr>
				</table>

			</form> -->


		</div>

		<div class="w-2/5">



			<div class="text-xl text-black font-sans pb-2 border-b-4 font-black w-full mt-8">
			    FILES 
			   
			</div>


				@if($thecase->files->first() )
				<div class="mt-1 text-sm w-full">
					@foreach($thecase->files as $thefile)
						<div class="flex mb-2 cursor-pointer border-grey-lighter rounded-lg py-1 w-full">

							<div class="w-5/6">
								<a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/download" class="text-grey-dark hover:text-blue" target="_new">
								<i class="w-6 text-center far fa-file"></i>
							 	{{ $thefile->name }}
							 	</a>
						 	</div>

						 	<div class="w-1/6 text-right">
								<a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/edit/{{ base64_encode(request()->path()) }}">
									<button class="rounded-lg bg-grey-lighter text-xs text-black px-2 py-1">
										Edit
									</button>
								</a>
							</div>

						 </div>
					@endforeach
				</div>
				@endif

			<div class="text-center">
				<a class="px-2 py-1 rounded-lg" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
				<button class="text-sm rounded-full uppercase font-bold border text-grey-dark hover:text-blue-dark hover:shadow mt-2 px-4 py-2">
					Add Files
				</button>
				</a>

				    <div class="collapse mt-4 p-4 rounded-lg bg-grey-lighter" id="collapseExample">

						<form id="file_upload_form" action="/{{ Auth::user()->team->app_type }}/files/upload/{{ base64_encode(json_encode(['case_id' => $thecase->id])) }}" method="post" enctype="multipart/form-data">
							
							@csrf

						    <div class="m-2">
						    	
						    	<input type="file" name="fileToUpload" id="fileToUpload" class="z-99 float-right h-10 cursor-pointer">

						    	<!-- <label for="fileToUpload">
						    		<button type="button" class="bg-orange-dark rounded-lg text-white px-4 py-2">
						    			Choose File
						    		</button>
						    	</label> -->

						    </div>

						    <button name="submit" class="bg-blue rounded-lg text-white px-4 py-2">
						    Upload <span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-4"></i>
						    </button>
						</form>

					</div>

			</div>
		</div>

	</div>

	<div class="mt-8">

		<div class="text-xl text-black font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
		    NOTES
		    <div class="flex">
		    	<div class="w-4/5">
				    @isset ($contacts)
				        <div class="text-sm font-normal text-grey-dark">
				            Your office has <b>{{ $contacts->count() }}</b> {{ Str::plural('contact', $contacts->count()) }} on this case.
				        </div>
				    @else
				        <div class="text-sm font-normal text-grey-dark">
				            Add a note below.
				        </div>
				    @endisset 
				</div>
				<div class="w-1/5">
					<div class="text-sm font-normal text-grey-dark pl-2">
			            Constituents
			        </div>
				</div>
			</div>
		   
		</div>

		@if($contacts->first())

			<div id="contacts">
				<div id="contacts-content">
					@foreach($contacts as $thecontact)

							@include('shared-features.cases.one-note-case')

					@endforeach
				</div>
			</div>

		@endif

	</div>


<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this case?
          </div>
          <div class="text-left font-bold py-2 text-base">
            This will delete the case and disconnect all {{ ($thecase->contacts->count() > 1) ? number_format($thecase->contacts->count(),0,'.',',') : '' }} contacts and files that are currently linked to this case. (The contacts and files themselves will not be deleted).
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

</div>

@endsection

@section('javascript')

	@livewireScripts

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

<script type="text/javascript">
	

	function uncheckAll() {
  		$('input[type="checkbox"]:checked').prop('checked',false);
	}

	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('/{{ Auth::user()->team->app_type }}/cases_searchpeople/'+{!! $thecase->id !!}+'/'+v, function(response) {
			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}

	function getSearchData_hh(v) {
		if (v == '') {
			$('#list_hh').addClass('hidden');
		}
		// alert('/{{ Auth::user()->team->app_type }}/cases_searchhouseholds/'+{!! $thecase->id !!}+'/'+v);
		$.get('/{{ Auth::user()->team->app_type }}/cases_searchhouseholds/'+{!! $thecase->id !!}+'/'+v, function(response) {
			if (response == '') {
				$('#list_hh').addClass('hidden');
			} else {
				$('#list_hh').html(response);
				$('#list_hh').removeClass('hidden');
			}
		});
	}


	$(document).ready(function() {
		$("#contact_subject").focus();

		$(document).on("click", ".contact_followup", function() {
        	var id = $(this).attr('data-id');
            if ($("[data-id="+id+"]").is(":checked")) {
				$.ajax({
				 	type: "GET",
				 	url: '/{{ Auth::user()->team->app_type }}/followups/done/'+id+'/true', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('bg-orange-lightest');
						$('#followup_'+id).removeClass('text-black');
		                $('#followup_'+id).addClass('text-grey');
		                $('#followup_'+id).addClass('bg-grey-lighter');
		                $('#followup_'+id+"_pending").addClass('hidden');
		                $('#followup_'+id+"_done").removeClass('hidden');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            } else {
				$.ajax({
				 	type: "GET",
				 	url: '/{{ Auth::user()->team->app_type }}/followups/done/'+id+'/false', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('text-grey');
		                $('#followup_'+id).removeClass('bg-grey-lighter');
		                $('#followup_'+id).addClass('bg-orange-lightest');
		                $('#followup_'+id).addClass('text-black');
		                $('#followup_'+id+"_pending").removeClass('hidden');
		                $('#followup_'+id+"_done").addClass('hidden');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            }

        });

		$(document).on("click", "#show_time", function() {
			if($('#show_time_div').hasClass('hidden')) {
				$('#show_time_div').removeClass('hidden');
				$('#use_time').val(1);
			} else {
				$('#show_time_div').addClass('hidden');
				$('#use_time').val(0);
			}
		});


	
	    $("input[type=file]").change(function(e){
            var fileName = e.target.files[0].name;
	    	$("#file_selected").text(fileName); 
	    });

		$( "#file_upload_form" ).submit(function( event ) {
			var fileName = $('#fileToUpload').val();
			if (fileName == '') { event.preventDefault(); }
		});

		$(document).on("click", "#set_follow_up", function() {
			if($('#set_follow_up_div').hasClass('hidden')) {
				$('#set_follow_up_div').removeClass('hidden');
				$('#person_followup').prop('checked', true);
			} else {
				$('#set_follow_up_div').addClass('hidden');
				$('#person_followup').prop('checked', false);
			}
		});

        $(document).on("click", "#person_followup", function() {
            if ($('#person_followup').is(":checked")) {
                $("#set_follow_up_div").removeClass('hidden');
            } else {
                $("#set_follow_up_div").addClass('hidden');
            }
        });

        //////////////////////////////////////////////////////////////////////////////

		$(document).on("click", "#addothers", function() {
			if($('#addothers_form').hasClass('hidden')) {
				$('#existing_linked').html('');
				$('#existing_checkboxes').removeClass('hidden')
				$('#addothers_form').removeClass('hidden');
				$("#cfbar").focus();
			}
		});

		$("#cfbar").focusout(function(){
			window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
		});
		
		$("#cfbar").keyup(function(){
			getSearchData(this.value);
		});

	    $(document).on('click', ".clickable-select-person", function () {
	    	id = $(this).data("theid");
	        
			$.get('/{{ Auth::user()->team->app_type }}/cases/'+{!! $thecase->id !!}+'/linkperson/'+id, function(response) {
				if (response != '') {
					$('#existing_checkboxes_interior').append(response);
				}
			});
	    });

		//////////////////////////////////////////////////////////////////////////////

		$(document).on("click", "#addothers_hh", function() {
			if($('#addothers_form_hh').hasClass('hidden')) {
				$('#existing_linked_hh').html('');
				$('#existing_checkboxes_hh').removeClass('hidden')
				$('#addothers_form_hh').removeClass('hidden');
				$("#cfbar_hh").focus();
			}
		});

		$("#cfbar_hh").focusout(function(){
			window.setTimeout(function() {$('#list_hh').addClass('hidden'); }, 300);
		});
		
		$("#cfbar_hh").keyup(function(){
			getSearchData_hh(this.value);
		});

	    $(document).on('click', ".clickable-select-household", function () {
	    	id = $(this).data("theid");
	        // alert('/{{ Auth::user()->team->app_type }}/cases/'+{!! $thecase->id !!}+'/link_hh/'+id);
			$.get('/{{ Auth::user()->team->app_type }}/cases/'+{!! $thecase->id !!}+'/link_hh/'+id, function(response) {
				if (response != '') {
					$('#existing_checkboxes_interior_hh').append(response);
				}
			});
	    });

 		$(document).on('click', "#edit_closing_remarks_button", function () {
 			$("#show_closing_remarks").addClass('hidden');
 			$("#edit_closing_remarks").removeClass('hidden');
 			$("#edit_closing_remarks").focus();
 		});


	});
</script>

@if(1==2 && Auth::user()->permissions->developer)

<script type="text/javascript">

	$('#contact_notes').summernote({
	  toolbar: [
	    // [groupName, [list of button]]
	    ['style', ['bold', 'italic', 'underline']],
	    ['fontsize', ['fontsize']],
	    ['color', ['color']],
	    ['para', ['ul', 'ol', 'paragraph']]
	  ],
	  height:100
	});

</script>



@endif


@endsection