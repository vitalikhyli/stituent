@extends(Auth::user()->team->app_type.'.base')

@section('title')
    New Case
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 

	<a href="/{{ Auth::user()->team->app_type }}/cases/list/all">Cases</a> 
	
    > &nbsp;<b>New Case</b>


@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/cases/save">
	@csrf

	@if(isset($contact))
		<input type="hidden" name="contact_id" value="{{ $contact->id }}" />
	@endif

	@if(isset($person_id))
		<input type="hidden" name="person_id" value="{{ $person_id }}" />
	@endif

	<div class="text-xl font-sans border-b-4 border-blue pb-3">

		<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person_id }}">
			<button type="button" class="ml-4 rounded-lg bg-grey-darkest hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Cancel
			</button>
		</a>


		<button class="rounded-lg bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save
		</button>

		<i class="fa fa-folder mr-2"></i>

		@if(isset($contact))
			Convert to New Case
		@else
			New Case
		@endif
	
	</div>


	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full">


		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				@lang('Subject')
			</td>
			<td class="p-2">
				@if (isset($contact))
				<input type="text" name="subject" id="subject" placeholder="Add Subject" class="border-2 rounded-lg px-4 py-2 w-2/3 font-bold" value="{{ $contact->subject }}" />
				@else
				<input type="text" name="subject" id="subject" placeholder="Add Subject" class="border-2 rounded-lg px-4 py-2 w-2/3 font-bold" value="" />
				@endif
			</td>
		</tr>

		@if(isset($notes))

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6">
				@lang('Notes')
			</td>
			<td class="p-2">
				<textarea name="notes" placeholder="Notes" class="w-full rounded-lg border p-2" rows="4">{{ $notes }}</textarea>
			</td>
		</tr>

		@endif

	</table>

</form>

@if(isset($contact))

	<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/contacts/{{ $contact->id }}/link_to_case">

	@csrf

		@if(isset($contact))
			<input type="hidden" name="contact_id" value="{{ $contact->id }}" />
		@endif

		<div class="text-xl font-sans border-b-4 border-blue pb-3 mt-8">


			<button class="rounded-lg bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
				Save
			</button>

			<i class="fa fa-folder mr-2"></i>

			...or Connect to Existing Case

		</div>

  		<input type="text" id="filter-input" onkeyup="filterCases()" class="border-2 shadow m-2 rounded-lg p-2 font-bold" placeholder="Filter Cases" />

		<div class="text-sm p-2 table">
		@foreach($case_options as $thecase)
			@if (!isset($thecase->id))
				@continue
			@endif
			<div class="group-div table-row hover:bg-orange-lightest p-1 w-full">

				<div class="table-cell {{ ($contact->case_id == $thecase->id) ? 'bg-blue-lightest border-b border-t' : '' }}">
					<label for="case_{{ $thecase->id }}" class="font-normal">

						<input type="radio" {{ ($contact->case_id == $thecase->id) ? 'checked' : '' }} name="case_id" value="{{ $thecase->id }}" id="case_{{ $thecase->id }}" />

						<span class="text-grey-darker px-2">{{ \Carbon\Carbon::parse($thecase->date)->toDateString() }}</span>

						<span class="group-name-div font-bold ">
						{{ $thecase->subject }}</span>

					</label>
				</div>

				<div class="table-cell {{ ($contact->case_id == $thecase->id) ? 'bg-blue-lightest border-b border-t' : '' }}">
				@foreach ($thecase->people as $person)
					<span class="text-grey-darker">{{ $person->name }}</span>
				@endforeach
				</div>

				<div class="table-cell {{ ($contact->case_id == $thecase->id) ? 'bg-blue-lightest border-b border-t' : '' }}">
				@if($contact->case_id == $thecase->id)
					<div class="text-right font-bold text-blue">
						Current Case
					</div>
				@endif
			</div>

			</div>
		@endforeach
		</div>


	</form>
@endif


@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

		$("#subject").focus();

	});



function filterCases()
{
  input = document.getElementById('filter-input');
  filter_string = input.value.toUpperCase();
  lines = document.getElementsByClassName('group-div');
  group_names = document.getElementsByClassName('group-name-div');

  for (i = 0; i < lines.length; i++) {

    group_name = group_names[i].innerHTML.trim().toUpperCase();

    if (group_name.indexOf(filter_string) > -1) {

      lines[i].style.display = "";

    } else {

      lines[i].style.display = "none";

    }
  }

}
</script>
@endsection