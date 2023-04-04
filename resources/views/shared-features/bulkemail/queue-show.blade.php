@extends('office.base')

@section('title')
    Bulk Emailer
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Bulk Email', 'bulkemail_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')




	<div class="text-xl font-sans w-full border-b-4 border-blue p-2">

		<a href="/{{ Auth::user()->team->app_type }}/emails">
			<button type="button" class="float-right bg-grey-darkest text-white px-4 py-2 rounded-lg text-sm ml-2 hover:bg-blue-dark">
				Back
			</button>
		</a>

		<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/queueshow">
			<button type="button" class="float-right bg-red text-white px-4 py-2 rounded-lg text-sm ml-2 hover:bg-red-dark">
				<i class="fas fa-redo-alt mr-2"></i> Refresh
			</button>
		</a>

		<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/edit">
			<button type="button" class="float-right bg-grey-dark text-white px-4 py-2 rounded-lg text-sm ml-2 hover:bg-grey-darker">
				<i class="fas fa-envelope mr-2"></i> See Email
			</button>
		</a>

		@if(!$email->completed_at)
			<a href="/{{ Auth::user()->team->app_type }}/emails/{{ $email->id }}/queuehalt">
				<button type="button" class="float-right bg-black text-white px-4 py-2 rounded-lg text-sm ml-2 hover:bg-grey-darker">
					<i class="fas fa-ban mr-2"></i> Halt
				</button>
			</a>
		@endif

		
		<i class="fas fa-envelope mr-2"></i> {{ $email->name }}
	</div>

	<table class="w-full text-sm cursor-pointer mb-6">
		<tr class="border-b">
			<td class="p-1 w-1/6">
				Subject
			</td>
			<td class="p-1">
				{{ $email->subject }} 
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-1 w-1/6">
				Status
			</td>
			<td class="p-1 font-bold">
				{{ $email->status() }} 
				@if($email->completed_at)
					<span class="text-blue"> ({{ \Carbon\Carbon::parse($email->completed_at)->format("Y-m-d h:i") }})</span>
				@endif
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-1 w-1/6">
				Total
			</td>
			<td class="p-1">
				{{ $queue->count() }} 
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-1 w-1/6">
				Sent
			</td>
			<td class="p-1">
				{{ $email->queuedAndSentCount() }} 
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-1 w-1/6">
				Remaining
			</td>
			<td class="p-1">
				{{ $email->queuedNotSentCount() }} 
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-1 w-1/6">
				Bounces
			</td>
			<td class="p-1">
				xxxx
			</td>
		</tr>
	</table>


	<div class="text-xl font-sans w-full border-b-4 border-blue pb-2">

		The Queue
	</div>

	<table class="w-full text-sm cursor-pointer mb-6">
		<tr class="bg-grey-lighter border-b">
			<td class="p-1 w-8">
				#
			</td>
			<td class="p-1">
				Email
			</td>

			<td class="p-1 text-center">
				Sent?
			</td>

			<td class="p-1 text-center">
				Attempts
			</td>
		</tr>

		@foreach($queue as $theemail)
		<tr class="clickable border-b hover:bg-orange-lightest" data-href="">

			<td class="p-1 text-blue-dark">
				{{ $loop->iteration }}
			</td>

			<td class="p-1 text-blue-dark">
				{{ $theemail->email }}
			</td>
			
			<td class="p-1 text-center">
				@if($theemail->sent)
					<i class="far fa-check-circle text-xs"></i>
				@elseif($theemail->processing)
					<span class="text-grey-dark">Processing</span>
				@endif
			</td>

			<td class="p-1 text-center">
				{{ $theemail->attempts }}
			</td>
		</tr>
		@endforeach
	</table>



<br />
<br />
@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

        var reloadTimer = setInterval(function(){

          var elements = document.getElementsByClassName('status');
          remaining = elements[0].innerHTML.length;
          remaining += 1;

          var newstring = '.'.repeat(remaining);
          if(remaining == 5){
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
