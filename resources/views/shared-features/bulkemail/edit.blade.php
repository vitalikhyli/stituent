@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit Email
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > <a href="/{{ Auth::user()->team->app_type }}/emails">Bulk Emails</a> > Edit Email

@endsection

@section('style')

<!-- <link href="https://cdn.jsdelivr.net/npm/froala-editor@3.0.4/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" /> -->


<!-- <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet"> -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
  

	<style>
	
		.group-checkbox input:checked ~ .position {
			display: block;
		}
		.group-checkbox .position {
			display: none;
		}

	</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.css" rel="stylesheet"></link>

	<style>

		/* The switch - the box around the slider */
		.switch {
		  position: relative;
		  display: inline-block;
		  width: 60px;
		  height: 34px;
		}

		/* Hide default HTML checkbox */
		.switch input {
		  opacity: 0;
		  width: 0;
		  height: 0;
		}

		/* The slider */
		.slider {
		  position: absolute;
		  cursor: pointer;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  background-color: #ccc;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		.slider:before {
		  position: absolute;
		  content: "";
		  height: 26px;
		  width: 26px;
		  left: 4px;
		  bottom: 4px;
		  background-color: white;
		  -webkit-transition: .4s;
		  transition: .4s;
		}

		input:checked + .slider {
		  background-color: #2196F3;
		}

		input:focus + .slider {
		  box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round {
		  border-radius: 34px;
		}

		.slider.round:before {
		  border-radius: 50%;
		}
</style>

@endsection

@section('main')


	@if(session('test_success'))
		<div class="rounded-lg bg-blue text-white p-8 mb-4">
			<i class="fas fa-check-circle mr-2"></i> Test email has been added to the queue.
		</div>
	@endif

	<form id="bulk-email-form" method="POST" action="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/update">

		@csrf 

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">

		<div class="float-right">


			@if(!$email->queued)

				<a href="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/delete">
					<button type="button" class="rounded-lg px-4 py-2 text-red text-center ml-2 text-sm"/>
						<i class="fas fa-exclamation-triangle mr-2"></i> 
						@if(!$email->isTemplate)
							Delete Email
						@else
							Delete Template
						@endif
					</button>
				</a>

			@endif
			

		<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>
		
		<input type="submit" formaction="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>



			@if(!$email->queued && !$email->isTemplate)
				<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/update/test" name="update" value="Test" class="rounded-lg px-4 py-2 bg-red text-white text-center ml-2"/>
				<i class="fas fa-envelope mr-2"></i> Send Test
				</button>

			
				<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/update/queue" name="update" value="Queue" class="rounded-lg px-4 py-2 bg-red-dark text-white text-center ml-2"/>
				<i class="fas fa-envelope mr-2"></i> Send All Emails
				</button>

			@endif

		</div>

			<span class="text-2xl">

				@if($email->queued)
					Your Email
				@elseif($email->isTemplate)
					Edit Template
				@else
					Edit Bulk Email
				@endif
			</span>
		</div>
	</div>

	@include('elements.errors')
	
	<div class="flex text-grey-darker">

		<div class="w-2/3">
			<div class="text-lg text-black mt-8 border-b-2 font-bold border-grey flex">
				Set-Up
			</div>

			<div class="flex">
				<div class="w-1/6 py-2 font-bold">
					Created
				</div>
				<div class="w-5/6 p-2 text-base">
					{{ \Carbon\Carbon::parse($email->created_at)->format('n/j/Y g:ia') }}
						({{ $email->created_at->diffForHumans() }})
				</div>
			</div>

			<div class="flex">
				<div class="w-1/6 pt-4 font-bold">
					Title
				</div>
				<div class="w-5/6 p-2 text-base">
					<input type="text" name="name" class="border rounded-lg p-2 w-full font-semibold bg-grey-lightest text-black" value="{{ $errors->any() ? old('name') : $email->name }}" />

					@if($email->isTemplate)
						<div class="p-2 px-4 bg-grey-lightest text-black text-xs uppercase mt-1">
							You are working on a <span class="font-bold text-red-dark">TEMPLATE.</span> Rename it to make it a normal email.
						</div>
					@endif
				</div>
			</div>

			<div class="flex">
				<div class="w-1/6 pt-4 font-bold">
					Tracker Code (optional)
				</div>
				<div class="w-5/6 p-2 text-base">
					<div class="flex">
						<div class="w-1/2">
							<select id="previous-tracker-codes" class="form-control">
								<option value="">- Previous Tracker Codes -</option>
								@foreach ($previous_tracker_codes as $tracker_code)
									<option value="{{ $tracker_code }}">{{ $tracker_code }}</option>
								@endforeach
							</select>
						</div>
						<div class="w-1/2">
							<input id="old-tracker-code" type="text" name="old_tracker_code" class="border rounded-lg p-2 w-full font-semibold bg-grey-lightest text-black" value="{{ $errors->any() ? old('old_tracker_code') : $email->old_tracker_code }}" />
						</div>
					</div>
				</div>
			</div>

			<!-- <div class="flex">
				<div class="w-1/6 pt-4 font-bold align-middle">
					Folder
				</div>
				<div class="w-5/6 p-2 pt-4 text-base">
					
					<select name="code_id">

						<option value="" {{ (!$email->bulk_email_code_id) ? 'selected' : '' }}>-- None --</option>

						@foreach($codes as $code)
							<option value="{{ $code->id }}" {{ ($code->id == $email->bulk_email_code_id) ? 'selected' : '' }}>{{ $code->name }}</option>	
						@endforeach

					</select>

					<input type="text" name="code_new" class="border rounded-lg p-1 font-semibold bg-grey-lightest text-black text-sm" placeholder="Or New Folder" />

				</div>
			</div> -->

			<div class="text-lg font-bold text-black mt-8 border-b-2 border-grey">

				<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/print" target="new">
					<div class="mr-1 text-center text-sm float-right">
						<i class="fas fa-print mr-1"></i> View Printable
					</div>
				</a>

				Contents 
			</div>

			@include('shared-features.bulkemail.edit-content-form')


		</div>

		@if(!$email->isTemplate)
		

			<div class="w-1/3">

				<div id="find-constituents" style="transition: .3s;" class="mt-4 pb-4 shadow ml-4 bg-white border-l-2  border-r-2 border-b-2 border-blue-light">


					@if(!$email->queued)
						@if(true)
						@include('shared-features.bulkemail.query-form-bulkemail')
						@endif
					@else
					
						@include('shared-features.bulkemail.query-form-bulkemail-static')

					@endif
					

					@if($email->completed_at)
						<div class="p-4 text-sm text-grey-darker">
							@foreach ($email->queuedRecipients as $queued_recipient)
								{{ $loop->iteration }}. 
								@if ($queued_recipient->person)
									<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $queued_recipient->person->id }}">
										{{ $queued_recipient->person->name }}, 
									</a>
								@endif
								{{ $queued_recipient->email }}<br>
							@endforeach
						</div>
					@else
						@include('shared-features.bulkemail.recipients-list')
					@endif

				</div>


			</div>

		
		@endif
		

	</div>
	

	<div class="mt-4 p-4 text-center w-full border-t-4 border-blue">
					
		<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>
		
		<input type="submit" formaction="/{{ Auth::user()->team->app_type }}/emails/{{$email->id}}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

	</div>



</form>



<br />
<br />

@endsection

@section('javascript')


<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>




<script type="text/javascript">

	$(document).ready(function() {

		@if(!$email->isTemplate)
			createSlimSelects();	
		@endif
		
	});

	function createSlimSelects() {
	
		new SlimSelect({
		  select: '#slim-select-municipality',
		  placeholder: 'Municipalities'
		})
		new SlimSelect({
		  select: '#slim-select-has_received_emails',
		  placeholder: 'Has Received Emails'
		})	
		new SlimSelect({
		  select: '#slim-select-has_not_received_emails',
		  placeholder: 'Has Not Received Emails'
		})	
		@foreach($categories as $cat)
			new SlimSelect({
			  select: '#slim-select-category-{!! $cat->id !!}',
			  placeholder: '{{ addslashes($cat->name) }}'
			})
		@endforeach
	}


	
	function getRecipients() {
	  	var data = $('[query_form="true"]').serialize();
	  	$('#recipients-list').addClass('opacity-50');
  		$.get('/{{ Auth::user()->team->app_type }}/emails/update-recipients', data, function(response) {
  			$('#recipients-list').replaceWith(response);
  		});
	}

	$('[query_form="true"]').keyup(delay(function (e) {
		getRecipients();
	}, 500));

	$(document).on('change', '[query_form="true"]', function() {
		getRecipients();
  	});

  	$(document).on('change', '#previous-senders', function() {
  		var email = $('option:selected', this).attr('email');
  		var name =  $('option:selected', this).attr('name');
  		$('#sent-from').val(name);
  		$('#sent-from-email').val(email);
  	});

  	$(document).on('change', '#previous-tracker-codes', function() {
  		var code = $('option:selected', this).val();
  		$('#old-tracker-code').val(code);
  		$('#previous-tracker-codes-form').val(code);
  		$('#previous-tracker-codes-form').change();
  	});


  	$(document).on('click', '#bulk-email-form .dynamic-field', function() {

		// https://maxrohde.com/2014/03/28/insert-text-at-caret-position-in-summernote-editor-for-bootstrap/

		the_field = '{% ' + $(this).attr('value') + ' %}';

		$('#summernote').summernote('saveRange'); 
		$('#summernote').summernote('restoreRange');
		$('#summernote').summernote('focus');
		$('#summernote').summernote('insertText', the_field);

	});

	$(document).on('click', '#bulk-email-form .dynamic-picture', function() {
		url = $(this).attr('data-url');
		filename = $(this).attr('data-filename');
		$('#summernote').summernote('saveRange'); 
		$('#summernote').summernote('restoreRange');
		$('#summernote').summernote('focus');
		$('#summernote').summernote('insertImage', url, filename);
		$('#images').toggleClass('hidden');
	});

	// SUMMERNOTE API: https://summernote.org/deep-dive/

	$('#summernote').summernote({
	  toolbar: [
	    // [groupName, [list of button]]
	    ['style', ['bold', 'italic', 'underline', 'clear']],
	    ['font', ['strikethrough', 'superscript', 'subscript']],
	    ['fontname', ['fontname']],
	    ['fontsize', ['fontsize']],
	    ['color', ['color']],
	    ['para', ['ul', 'ol', 'paragraph']],
	    ['height', ['height']],
	    ['insert', ['link', 'picture', 'hr']],
	    ['view', ['fullscreen', 'codeview', 'help']],


	  ],
	  height: 400,
	  callbacks: {

		  onImageUpload : function(files, editor, welEditable) {
	 
	             for(var i = files.length - 1; i >= 0; i--) {
	                     sendFile(files[i], this);
	            }
	        }
	    }
	}).on("summernote.enter", function(we, e) {
		// https://stackoverflow.com/questions/37567346/how-to-change-enter-behaviour-in-summernote
		$(this).summernote("pasteHTML", "<br>");

		e.trigger($.Event("keydown", {
            keyCode: 13, // ENTER
        }));
		e.preventDefault();
	});

	function sendFile(file, el) {
		var form_data = new FormData();
		form_data.append('file', file);
		form_data.append('_token', '{{ csrf_token() }}');
		$.ajax({
		    data: form_data,
		    type: "POST",
		    url: '/{{ Auth::user()->team->app_type }}/files/upload-image',
		    cache: false,
		    contentType: false,
		    processData: false,
		    success: function(url) {
		        $(el).summernote('editor.insertImage', url);
		    }
		});
	}


	$(document).on('click', '#add_image', function() {
		$('#images').toggleClass('hidden');
	});

</script>


@endsection

