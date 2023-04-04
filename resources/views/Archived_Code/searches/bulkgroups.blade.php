@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Open Cases
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Labels', 'reports_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Bulk-Apply Groups
			</div>
		</div>
	</div>

<form method="post" action="{{dir}}/bulkgroups">
<div class="text-left mb-2 border-b pb-4 w-full flex">

	<div class="flex flex-1">

			@csrf

		<span class="mr-2 text-blue">
		Use this search:
		</span>

			<select name="list_to_use">
				<option value="0">--</option>
				@foreach($list_options as $thelist)
					<option value="{{ $thelist->id }}" {{ ($thelist->id == $list_to_use ) ? 'selected' : '' }}>{{ $thelist->name }}</option>
				@endforeach
			</select>

		<span class="mx-2 text-blue">
		With this group:
		</span>

			<select name="group_to_use">
				<option value="0">--</option>
				@foreach($group_options as $thegroup)
					<option value="{{ $thegroup->id }}" {{ ($thegroup->id == $group_to_use ) ? 'selected' : '' }}>{{ $thegroup->name }}</option>
				@endforeach
			</select>

			<button class="rounded-lg bg-blue text-white px-2 py-1 text-sm ml-2">
				Show List
			</button>
	</div>

</div>
</form>

@if(isset($output) &&($output->count() >0))

    @if($output instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<nav class="-mt-2 aria-label="Page navigation example">
			{{ $output->appends(request()->except('page'))->links() }} 
		</nav>
	@endif

	<table class="w-full">
	@foreach($output as $thevoter)
		<tr class="border-b">
			<td class="p-2">
				@if(IDisVoter($thevoter->id))
					<button data-action="add" data-group_id="{{ $group_to_use }}" data-person_id="{{ $thevoter->id }}" class="toggle-group bg-grey rounded-lg text-white px-2 py-1 text-sm">
						{{ \App\Group::find($group_to_use)->name }}
					</button>
				@else
					@if(\App\Person::find($thevoter->id)->memberOfGroup($group_to_use))
						<button data-action="remove" data-group_id="{{ $group_to_use }}" data-person_id="{{ $thevoter->id }}" class="toggle-group bg-blue rounded-lg text-white px-2 py-1 text-sm">
							{{ \App\Group::find($group_to_use)->name }}
						</button>
					@else
						<button data-action="add" data-group_id="{{ $group_to_use }}" data-person_id="{{ $thevoter->id }}" class="toggle-group bg-grey rounded-lg text-white px-2 py-1 text-sm">
							{{ \App\Group::find($group_to_use)->name }}
						</button>
					@endif
				@endif
			</td>
			<td class="p-2">
				{{ $thevoter->id }}
			</td>
			<td class="p-2">
				@if(IDisPerson($thevoter->id))
					{{ \App\Person::find($thevoter->id)->team_id }}
				@endif
			</td>
			<td class="p-2">
				{{ $thevoter->full_name }}
			</td>
			<td class="p-2">
				{{ $thevoter->full_address }}
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
	$(document).ready(function() {

	    $(document).on("click", ".toggle-group", function() {

			var person_id = $(this).attr('data-person_id');
	    	var group_id = $(this).attr('data-group_id');
	    	var action = $(this).attr('data-action');
	    	var team_id = {!! Auth::user()->team->id !!};

	    	var url = '{{dir}}/bulkgroups/'+action+'/'+group_id+'/'+person_id+'/'+team_id;

	        $.get(url, function(response) {
	            $('[data-person_id="'+person_id+'"]').replaceWith(response);
	        }); 

        });

    });

</script>

@endsection
