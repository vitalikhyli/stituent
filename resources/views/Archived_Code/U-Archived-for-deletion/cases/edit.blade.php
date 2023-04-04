@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Case ID # {{ $thecase->id }}
@endsection

@section('breadcrumb')

	{!! Auth::user()->Breadcrumb('Edit Case', 'edit_case') !!}


@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="{{dir}}/cases/{{ $thecase->id }}/save">
	@csrf

	<div class="text-xl font-sans border-b-4 border-blue pb-3">


		<button class="rounded-full bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save
		</button>

		<button formaction="{{dir}}/cases/{{ $thecase->id }}/save/close" class="rounded-full bg-blue-dark hover:bg-blue-darker text-white float-right text-sm px-8 py-2 mt-1 shadow mr-2">
			Save and Close
		</button>

		<i class="fa fa-folder mr-2"></i>
		@lang('Case') # {{ $thecase->id }}
			
	</div>


	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Type
			</td>
			<td class="p-2 flex">
				<select name="type" id="type">
					<option {{ ($thecase->type == '') ? 'selected' : '' }} value="">--</option>
					@foreach($available_types as $thetype)
						<option {{ ($thecase->type == $thetype) ? 'selected' : '' }} value="{{ $thetype }}">{{ $thetype }}</option>
					@endforeach
				</select>
				<input type="text" name="type_new" id="type_new" placeholder="Or create new case type" class="border-2 rounded-lg px-4 py-2 w-1/3 ml-4" />
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				@lang('Subject')
			</td>
			<td class="p-2">
				<input type="text" name="subject" id="subject" value="{{ $thecase->subject }}" class="border-2 rounded-lg px-4 py-2 w-2/3 font-bold" />
			</td>
		</tr>

		<tr class="border-b bg-orange-lightest">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				Involves
			</td>
			<td class="p-2">
				<div class="flex flex-wrap">
				@if($thecase->people->count() >0)
					@foreach($thecase->people as $theperson)
						<a href="{{dir}}/constituents/{{ $theperson->id }}">
							<div class="border bg-grey-lighter hover:bg-blue hover:text-white rounded-lg mr-1 px-2 py-1 flex-1 flex-initial">
								{{ $theperson->full_name }}
							</div>
						</a>
					@endforeach
				@endif
				</div>
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right">
				@lang('Opened')
			</td>
			<td class="p-2">
				<input type="text" name="date" value="{{ $thecase->date }}" class="border-2 rounded-lg px-4 py-2 w-1/3" />
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				Notes
			</td>
			<td class="p-2">
				<textarea name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-2/3">{{ $thecase->notes }}</textarea>
			</td>
		</tr>
	</table>

</form>


@endsection

@section('javascript')
<script type="text/javascript">
	function uncheckAll() {
  		$('input[type="checkbox"]:checked').prop('checked',false);
	}
	$("#subject").focus();
</script>
@endsection