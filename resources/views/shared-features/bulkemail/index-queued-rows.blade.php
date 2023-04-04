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

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		 @lang('Bulk Emails')
	</div>

	@include('shared-features.bulkemail.index-nav')

</div>

	<table class="w-full text-sm cursor-pointer mb-6 cursor-pointer">
		<tr class="bg-grey-lighter border-b">
			<td class="p-2">
				Updated
			</td>
			<td class="p-2">
				Email
			</td>
			<td class="p-2">
				Person_id
			</td>
			<td class="p-2">
				Voter_id
			</td>
			<td class="p-2">
				Processing
			</td>
			<td class="p-2">
				...Start
			</td>
			<td class="p-2">
				Attempts
			</td>
			<td class="p-2">
				Sent
			</td>
			<td class="p-2">
				Test
			</td>
		</tr>

		@foreach($queued_rows as $row)

			<tr class="border-b hover:bg-orange-lightest">

				<td class="p-2">
					{{ \Carbon\Carbon::parse($row->updated_at)->format('n/j/y h:i A') }}
				</td>
				<td class="p-2">
					{{ $row->email }}
				</td>
				<td class="p-2">
					{{ $row->person_id }}
				</td>
				<td class="p-2">
					{{ $row->voter_id }}
				</td>
				<td class="p-2">
					{{ $row->processing }}
				</td>
				<td class="p-2">
					{{ \Carbon\Carbon::parse($row->processing_start)->format('n/j/y h:i A') }}
				</td>
				<td class="p-2">
					{{ $row->attempts }}
				</td>
				<td class="p-2">
					{{ $row->sent }}
				</td>
				<td class="p-2">
					{{ $row->test }}
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
