@extends(Auth::user()->team->app_type.'.base')
<?php if (!defined('dir')) define('dir',Auth::user()->team->app_type); ?>

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

				<a class="mx-1" href="/{{dir}}/followups/pending">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/followups/pending','bg-blue-darker text-white','') }}">
						Pending
					
				</div>
				</a>

				<a class="mx-1" href="/{{dir}}/followups/done">
				<div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ urlLook('/done','bg-blue-darker text-white','') }}">
						Completed
				</div>
				</a>

			</div>


			<div class="text-2xl font-sans">
				Completed Follow Ups
				<span class="text-lg ml-1">
					@if($total > 0)
						({{ $total }})
					@endif
				</span>
			</div>
		</div>
	</div>
	


@if($followups_done->count() <= 0)

	None.

@else
	

@if($followups_done instanceof \Illuminate\Pagination\LengthAwarePaginator)
	<div class="float-right">
		{{ $followups_done->links() }}
	</div>
@endif

<table class="w-full">

<tr class="border-b border-grey-dark bg-grey-lighter font-semibold uppercase text-sm">
	<td class="p-1">
		Done?
	</td>
	<td class="p-1">
		User
	</td>
	<td class="p-1 w-1/6">
		Followup On
	</td>
	<td class="p-1">
		Connected To
	</td>
	<td class="p-1">
		Notes
	</td>
</tr>

@foreach($followups_done as $item)
	<tr id="followup_{{ $item->id }}" class="opacity-25 bg-grey-light border-b hover:bg-orange-lightest cursor-pointer text-sm">

		<td class="p-1 text-sm text-left">

			<label for="{{ $item->id }}" class="font-normal px-2 whitespace-no-wrap">
				<input type="checkbox" data-id="{{ $item->id }}" class="contact_followup" id="{{ $item->id }}" name="{{ $item->id }}" value="1" {{ ($item->followup_done) ? 'checked' : '' }} /> Done
			</label>

		</td>

		<td class="p-1 text-grey-dark">
			{{ $item->user->username }}
		</td>
		
		<td class="p-1">
			@if($item->followup_on)
				{{ \Carbon\Carbon::parse($item->followup_on)->format("D, M j") }}
			@else
				--
			@endif
		</td>

		<td class="p-1">
			@if($item->case_id)
				<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $item->case_id }}">
				<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-lg m-1 px-2 py-1 text-sm text-blue">
					{{ \App\WorkCase::find($item->case_id)->shortened_subject }}
					<i class="fa fa-folder-open ml-2"></i>
				</button>
				</a>
			@else
				@foreach($item->people as $theperson)
					<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}">
					<button class="hover:bg-blue hover:text-white bg-grey-lighter border rounded-full m-1 px-2 pr-3 py-1 text-sm text-grey-darkest">
						<i class="fa fa-user mr-2"></i>
						{{ $theperson->full_name }}
					</button>
					</a>
				@endforeach
			@endif
		</td>

		<td class="p-1 w-1/2">
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
				 	url: '/{{dir}}/followups/done/'+id+'/true', 
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
				 	url: '/{{dir}}/followups/done/'+id+'/false', 
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
