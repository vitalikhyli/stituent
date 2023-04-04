@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Bulk Emailer
@endsection

@section('breadcrumb')

    <a href="/office">Home</a> > Bulk Emails

@endsection

@section('style')

	<style>


	</style>

	@livewireStyles

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue mb-2">
	<div class="text-2xl font-sans w-full">
		 @lang('Bulk Emails')
	</div>

	@include('shared-features.bulkemail.index-nav')

</div>

	<div class="mb-4 flex">

		<div>
			<a href="/{{ Auth::user()->team->app_type }}/emails/new">
				<button type="button" class="bg-blue text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-dark">
					New Blank Email
				</button>
			</a>
		</div>

		<div class="text-right flex-grow">
			@livewire('bulkemail.processing')
		</div>

	</div>

	

@foreach($codes as $thecode)

	<div class="border-b-2 border-blue text-blue py-1">
		<i class="fas fa-folder mr-2"></i>
		{{ $thecode->name }}
	</div>


	@if($thecode->emails->count() > 0)
		<div class="mb-2">
			@include('shared-features.bulkemail.emails-table', ['emails' => $thecode->emails])
		</div>
	@else
		<div class="text-grey-dark text-sm italic mb-2">
			No emails are part of this folder... <a href="/{{ Auth::user()->team->app_type }}/emails/codes/{{ $thecode->id }}/delete">Delete?</a>
		</div>
	@endif

@endforeach


	<div class="border-b-2 border-blue text-red-dark py-1">
		<i class="fas fa-folder mr-2"></i>
		Templates

		<div class="pt-1 float-right text-sm text-grey-darker italic text-red-dark">
			To create a template, just make the first word of any email name <span class="font-bold">"TEMPLATE"</span>
		</div>

	</div>
	<div class="mb-4">
		@include('shared-features.bulkemail.templates-table', ['emails' => $templates])
	</div>

	<div class="border-b-2 border-blue text-blue py-1">
		<i class="fas fa-folder mr-2"></i>
		Emails
	</div>
	<div class="mb-2">
		@include('shared-features.bulkemail.emails-table', ['emails' => $emails])
	</div>

<br />
<br />
@endsection

@section('javascript')

	@livewireScripts

	<script type="text/javascript">

		$(document).ready(function() {

	        var reloadTimer = setInterval(function(){

	          var elements = document.getElementsByClassName('status');
	          remaining = elements[0].innerHTML.length;
	          remaining += 1;

	          var newstring = '.'.repeat(remaining);
	          if(remaining == 4){
	          	newstring = '';
	          }

	          	for (i = 0; i < elements.length; i++) {
				  elements[i].innerHTML = newstring;
				}

	        }, 1000);

		    $(".clickable").click(function() {
		        window.location = $(this).data("href");
		    });

		});
	</script>

@endsection
