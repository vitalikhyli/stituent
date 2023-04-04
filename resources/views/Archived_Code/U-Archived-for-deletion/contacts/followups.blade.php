@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Follow Ups
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Follow Ups', 'followups_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection


@section('main')

<?php 
	function urlLook($u, $a, $b) {
		if(!empty(strpos(url()->current(),$u))) {
			return $a;
		} else {
			return $b;
		}
	}
?>

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-full">

			<div class="flex float-right">

				<a class="mx-1" href="{{dir}}/followups/pending">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/followups/pending','bg-blue-darker text-white','') }}">
						Pending
				</div>
				</a>

				<a class="mx-1" href="{{dir}}/followups/done">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/done','bg-blue-darker text-white','') }}">
						Completed
				</div>
				</a>

			</div>


			<div class="text-2xl font-sans">
				Follow Ups
				<span class="text-lg ml-1">
					@if($total > 0)
						({{ $total }})
					@endif
				</span>
			</div>
		</div>
	</div>
	

@if($followups->count() <= 0)

	No follow ups right now!

@else


@if($followups instanceof \Illuminate\Pagination\LengthAwarePaginator)
	<div class="float-right">
		{{ $followups->links() }}
	</div>
@endif

<table class="w-full">

<tr class="border-b border-grey-dark bg-grey-lighter font-semibold uppercase text-sm">
	<td class="p-2">
		Done?
	</td>
	<td class="p-2 w-1/6">
		Followup On
	</td>
	<td class="p-2">
		Connected To
	</td>
	<td class="p-2">
		Notes
	</td>
</tr>

@foreach($followups as $item)
	<tr id="followup_{{ $item->id }}" class=" border-b hover:bg-orange-lightest cursor-pointer">
		<td class="p-2">

	<label for="{{ $item->id }}" class="float-right font-normal px-2 whitespace-no-wrap">
		<input type="checkbox" data-id="{{ $item->id }}" class="contact_followup" id="{{ $item->id }}" name="{{ $item->id }}" value="1" {{ ($item->followup_done) ? 'checked' : '' }} /> <span class="text-sm">Done</span>
	</label>

		</td>
		<td class="p-2">
			@if($item->followup_on)
				{{ \Carbon\Carbon::parse($item->followup_on)->format("D, M j") }}
			@else
				--
			@endif
		</td>

		<td class="p-2 w-1/3">
			@if($item->case_id)
					<a href="{{dir}}/cases/{{ $item->case_id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black uppercase">
						<i class="far fa-folder-open mr-2"></i>
						{{ \App\WorkCase::find($item->case_id)->subject }}
					</button>
					</a>
			@else
				@foreach($item->people as $theperson)
					<a href="{{dir}}/constituents/{{ $theperson->id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 py-1 text-sm text-black">
						<i class="far fa-user mr-2"></i>
						{{ $theperson->full_name }}
					</button>
					</a>
				@endforeach
			@endif
		</td>

		<td class="p-2 w-2/3">
			@if($item->subject)
				<div class="font-semibold">{{ $item->subject }}</div>
			@endif
				{{ $item->notes }}
		</td>
	</tr>
@endforeach
</table>

@endif


<br />
<br />
@endsection

@section('javascript')

<script type="text/javascript">
		$(document).on("click", ".contact_followup", function() {
        	var id = $(this).attr('data-id');
            if ($("[data-id="+id+"]").is(":checked")) {
				$.ajax({
				 	type: "GET",
				 	url: '{{dir}}/followup_done/'+id+'/true', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
						$('#followup_'+id).removeClass('text-black');
		                $('#followup_'+id).addClass('opacity-25');
		                $('#followup_'+id).addClass('bg-grey-light');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            } else {
				$.ajax({
				 	type: "GET",
				 	url: '{{dir}}/followup_done/'+id+'/false', 
				 	success: function(response, status, xhr){ 
	            		num_follow_ups = response;
		                $('#followup_'+id).removeClass('opacity-25');
		                $('#followup_'+id).removeClass('bg-grey-light');
		                $('#followup_'+id).addClass('text-black');
		                updateLeftCounter(num_follow_ups);
				 	}
				});
            }

        });
</script>

@endsection
