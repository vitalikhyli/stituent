@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Edit Email
@endsection

@section('breadcrumb')

    <a href="/office">Home</a> > <a href="/office/emails">Bulk Emails</a> > Edit Email

@endsection

@section('style')

<link href="https://cdn.jsdelivr.net/npm/froala-editor@3.0.4/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />

	<style>
	
		.group-checkbox input:checked ~ .position {
			display: block;
		}
		.group-checkbox .position {
			display: none;
		}

	</style>

@endsection

@section('main')


<!-- Include stylesheet -->


<!-- Create the editor container -->


	<form id="bulk-email-form" method="POST" action="{{dir}}/emails/{{$email->id}}/update">
		{{ csrf_field() }}

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">

		<div class="float-right">
			<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>

			<input type="submit" formaction="{{dir}}/emails/{{$email->id}}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>

			@if(!$email->queued)
				<input type="submit" formaction="{{dir}}/emails/{{$email->id}}/update/queue" "name="update" value="Save and queue" class="rounded-lg px-4 py-2 border bg-red-dark text-white text-center ml-2"/>
			@endif

		</div>

			<span class="text-2xl">
				@if($email->queued)
					Your Email
				@else
					Edit Bulk Email
				@endif
			</span>
		</div>
	</div>

	<div class="text-grey-darker">

		<div class="text-xl text-black mt-8 border-b-4 font-bold border-grey flex pb-2">

				<span class="text-grey-dark text-lg pr-2">1.</span> Title

			
		</div>

		<div class="flex">
			<div class="w-1/6 pl-8 font-bold">

			</div>
			<div class="w-5/6 p-2 text-base">
				<input type="text" name="name" class="border rounded-lg p-2 w-full font-semibold bg-grey-lightest text-black" value="{{ $errors->any() ? old('name') : $email->name }}" />
				<div class="ml-2 mt-2 text-sm text-grey-darker">
					Created {{ \Carbon\Carbon::parse($email->created_at)->format('n/j/Y g:ia') }}
					({{ $email->created_at->diffForHumans() }})
				</div>
			</div>
		</div>
					
		@include('office.bulkemail.recipients-form')

		@include('elements.errors')

		<div class="text-xl font-bold text-black mt-8 border-b-4 border-grey pb-2">
			<span class="text-grey-dark text-lg pr-2">4.</span> Contents 
		</div>

		<div class="">

			<table class="w-full">
				

				<div class="text-left flex">
					<div class="w-1/6 pl-8 pt-4 text-sm uppercase font-bold text-grey-darker">
						Subject
					</div>
					<div class="w-5/6 p-2 text-left">
						@if($email->queued)
							<div class="font-semibold">
								{{ $email->subject }}
							</div>
						@else
							<input type="text" name="subject" placeholder="Email Subject Line" class="border rounded-lg p-2 w-full font-semibold" value="{{ $errors->any() ? old('subject') : $email->subject }}" />
						@endif
					</div>
				</div>

				@if(!$email->queued)
					<div class="text-left flex">
						<div class="w-1/6 pl-8 pt-2 text-sm uppercase font-bold text-grey-darker">
							Merge Field
						</div>
						<div class="w-5/6 p-2 text-left">
							<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2" value="full_name">+ Full Name</div>
							<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2" value="title">+ Title</div>
							<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2" value="first_name">+ First Name</div>
							<div class="dynamic-field inline border rounded-full px-2 py-1 text-sm hover:bg-grey-lighter cursor-pointer mr-2" value="last_name">+ Last Name</div>
						</div>
					</div>
				@endif


				<div class="text-left flex">

					<div class="w-1/6 pl-8 px-2 py-2 align-top text-sm uppercase pt-8 font-bold text-grey-darker">
						Body
					</div>

					<div class="w-5/6 text-left p-2">

						@if(!$email->queued)

							<textarea class="froala-editor" id="froala-editor" name="content" rows="6" id="email-content">{{ $email->content }}</textarea>

						@else

							<div class="font-semibold p-1 text-right text-grey-darker text-sm p-2">
								Your email has be queued/sent so it is not editable.
							</div>
							<div class="border p-4 mb-2 shadow">
								{!! $email->content !!}
							</div>

						@endif

					</div>
				</div>


				<div class="text-left flex border-t mt-2 pt-2">

					<div class="w-1/6 pl-8 px-2 py-2 align-top text-sm font-bold text-grey-darker align-top">

						<span class="uppercase">
							Plain Text
						</span>

						<div class="text-grey-dark italic text-xs mt-2 font-semibold">
							A plain text version is needed to send properly formatted emails.
						</div>

					</div>

					<div class="w-5/6 text-left p-2 align-top">

						@if(!$email->queued)

							

							<label class="checkbox-inline pt-1 text-blue font-semibold text-sm">
								<input type="checkbox" name="refresh_plain" {{ ($email->refresh_plain) ? 'checked' : '' }}>
								Automatically refresh plain text based on HTML version
							</label>

							<textarea name="content_plain" rows="18" id="email-content-plain" class="border p-2 rounded-lg w-full mt-4 text-grey-darker text-sm font-mono">{{ $email->content_plain }}</textarea>


						@else

							<div class="font-semibold p-1 text-right text-grey-darker text-sm p-2">
								Your email has be queued/sent so it is not editable.
							</div>

							<div class="border p-4 mb-2 text-grey-darker text-sm font-mono shadow">
								{!! nl2br($email->content_plain) !!}
							</div>

						@endif

					</div>
				</div>
			</table>

		</div>
	</div>

@if(!$email->queued)
	<div class="p-4 text-center w-full">
		<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
		<input type="submit" formaction="{{dir}}/emails/{{$email->id}}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>

		<a href="{{dir}}/emails/{{$email->id}}/delete">
			<button type="button" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2"/>
				Delete This Email
			</button>
		</a>

	</div>
@endif

</form>



<br />
<br />

@endsection

@section('javascript')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@3.0.4/js/froala_editor.pkgd.min.js"></script>


<script>

	function check_uncheck_checkbox() {
		$('input[type="checkbox"]').each(function() {
			this.checked = false;
		});
  		var form = $('#bulk-email-form');
  		$.post('/office/emails/update-recipients-count', form.serialize(), function(response) {
  			setBillCount(response);
  		});
	}
  	var froala = new FroalaEditor('textarea.froala-editor');

  	$(document).on('change', '#bulk-email-form input, #bulk-email-form select', function() {
  		var form = $('#bulk-email-form');
  		$.post('/office/emails/update-recipients-count', form.serialize(), function(response) {
  			setBillCount(response);
  		});
  	});

  	$(document).on('click', '#bulk-email-form .dynamic-field', function() {
  		var val = $(this).attr('value');
  		froala.html.insert('{'+val+'}');
  	});

  	$(document).on('click', '#clear_all', function() {
		check_uncheck_checkbox();
  	});

  	$(document).on('click', '#bulk_email_array', function() {
		$('#bulk-email-form').submit();
  	});

  	$(document).on('click', '#button_add', function() {
  		$('.tab-button').removeClass('bg-blue-darker');
  		$('.tab-button').addClass('bg-blue');
  		$(this).removeClass('bg-blue');  		
		$(this).addClass('bg-blue-darker');
  		$('.form-panel').addClass('hidden');
		$('#panel_add').removeClass('hidden');
  	});

  	$(document).on('click', '#button_remove', function() {
  		$('.tab-button').removeClass('bg-blue-darker');
  		$('.tab-button').addClass('bg-blue');
  		$(this).removeClass('bg-blue');  		
		$(this).addClass('bg-blue-darker');
  		$('.form-panel').addClass('hidden');
		$('#panel_remove').removeClass('hidden');
  	});

  	$(document).on('click', '#button_filter', function() {
  		$('.tab-button').removeClass('bg-blue-darker');
  		$('.tab-button').addClass('bg-blue');
		$(this).removeClass('bg-blue');  		
		$(this).addClass('bg-blue-darker');
  		$('.form-panel').addClass('hidden');
		$('#panel_filter').removeClass('hidden');
  	});

  	function setBillCount(newcount) {
		//alert(newcount);
		var that = this;
		var original_count = $('#bulk-email-recipients-count').attr('value');
		//alert(original_count);
		var diff = Math.abs(original_count - newcount);
		//alert(diff);
		$({ Counter: original_count }).animate({ Counter: newcount }, {
		    duration: 1000,
		    easing: 'swing',
		    step: function () {
		      	bill_count = Math.ceil(this.Counter);
		      	// $('#bulk-email-recipients-count').text(bill_count);
		      	$('.bulk-email-recipients-count').text(bill_count);
		    },
		    complete: function() {
		    	bill_count = newcount;
		    	// $('#bulk-email-recipients-count').text(bill_count);
		    	// $('#bulk-email-recipients-count').attr('value', bill_count);
		    	$('.bulk-email-recipients-count').text(bill_count);
		    	$('.bulk-email-recipients-count').attr('value', bill_count);
		    }
		});
	}

	

</script>


@endsection
