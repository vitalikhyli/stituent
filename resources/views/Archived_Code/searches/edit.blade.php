@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Edit Search
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Edit Searches', 'reports_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')


<form action="{{dir}}/search/{{ $list->id }}/update" method="post">

	@csrf

	<div class="border-b-4 border-blue pb-2 w-full text-2xl font-sans">

			<button type="submit" class="text-base mr-2 float-right bg-blue text-white rounded-lg px-4 py-2">
				Save
			</button>

			<button formaction="{{dir}}/search/{{ $list->id }}/update/close" type="submit" class="text-base mr-2 float-right bg-grey-darker text-white rounded-lg px-4 py-2">
				Save and Close
			</button>




			Edit Search
				
	</div>


	<input type="hidden" name="team_id" value="{!! ($list->team_id) ? $list->team_id : Auth::user()->team->id !!}" />


	<table class="w-full border-b">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				Search Name:
			</td>
			<td class="p-2">
				<input type="text" name="name" placeholder="Search Name" class="w-full font-bold rounded-lg px-4 py-2 border" value="{{ ($list->name) ? $list->name : 'New Search Name' }} " />
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				Status
			</td>
			<td class="p-2">

				<label for="archive_0" class="mr-2 font-normal">
					<input type="radio" name="archived" id="archive_0" value="0" {!! (!$list->archived) ? 'checked' : '' !!} /> Active
				</label>

				<label for="archive_1" class="mr-2 font-normal">
					<input type="radio" name="archived" id="archive_1" value="1" {!! ($list->archived) ? 'checked' : '' !!} /> Archived
				</label>

			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				Options
			</td>
			<td class="p-2 w-6" valign="top">
				<a href="{{dir}}/search/{{ $list->id }}/result" class="text-grey-darker">
					<button type="button" class="border shadow text-grey-darker rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1 mr-2">
						<i class="fas fa-glasses mr-2"></i> See List
					</button>
				</a>

				@if ($list->scope_voters == 0)
				<a href="{{dir}}/search/{{ $list->id }}/export" class="text-grey-darker">
					<button type="button" class="border shadow text-grey-darker rounded-lg bg-grey-lighter hover:bg-blue hover:text-white px-2 py-1 mr-2">
						<i class="fa fa-download ml-2"></i> Export to CSV
					</button>
				</a>
				@endif

			</td>
		</tr>

		


	</table>


	@if(Auth::user()->permissions->admin)

	<div class="border-b-4 border-blue pb-1 w-full text-sm font-sans mt-4">
		<i class="fas fa-user-cog m-1"></i> Developer
	</div>

	<table class="w-full border-b text-blue-dark text-sm">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				SQL
			</td>
			<td class="p-2">
				{{ $list->sql }}
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				Terms
			</td>
			<td class="p-2">
				{{ $list->terms }}
			</td>
		</tr>
	</table>
	@endif


	<div class="border-b-4 border-blue pb-2 w-full text-lg mt-8 font-sans">
		Search Terms:
	</div>

	
	<table class="w-full">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter w-1/5">
				<i class="fas fa-caret-square-right w-4 m-2"></i> Search Scope:
			</td>
			<td class="p-2">
				<label for="scope_0" class="mr-2 font-normal">
					<input type="radio" name="scope_voters" id="scope_0" value="0" {!! (!$list->scope_voters) ? 'checked' : '' !!} /> Only Linked People
				</label>

				<label for="scope_1" class="mr-2 font-normal">
					<input type="radio" name="scope_voters" id="scope_1" value="1" {!! ($list->scope_voters) ? 'checked' : '' !!} /> Linked + Voterfile
				</label>
			</td>
		</tr>

		@if($terms)

		<?php $previous = ''; ?>

		@foreach($terms as $theterm)

			<tr class="{{ ($theterm->term != $previous) ? 'border-t' : '' }}">
				<td class="p-2 bg-grey-lighter w-1/5 capitalize">

					@if($theterm->term == $previous)
						<span class="text-white bg-grey-darkest px-1 text-xs mr-2 ml-6">OR</span>  {{ str_replace('_',' ',$theterm->term) }}
					@else
						<i class="fas fa-caret-square-right w-4 m-2"></i> {{ str_replace('_',' ',$theterm->term) }}
					@endif
				</td>
				<td class="p-1">

					@if(in_array($theterm->term, ['full_name', 'full_address', 'address_city', 'party','gender']))

					<div class="flex w-3/4">
						<input type="text" name="term_{{ $theterm->id }}_{{ $theterm->term }}" class="w-full rounded-lg px-4 py-2 border border-grey" value="{{ $theterm->value }}" />
					</div>

					@endif

				</td>
			</tr>

		<?php $previous = $theterm->term; ?>

		@endforeach
		@endif

		@if($new_options)
		<tr class="border-b bg-blue-lighter">
			<td class="p-2 w-1/5 capitalize">
				<select id="new_select" name="new" class="border border-grey-darker w-full">
					<option value="">(ADD NEW)</option>
					@foreach($new_options as $theoption)
						<option value="{{ $theoption }}">{{ $theoption }}</option>
					@endforeach
				</select>
			</td>
			<td class="p-2">

				<div id="new_term">


				</div>

			</td>
		</tr>
		@endif

	</table>
	

</form>


<br />
<br />
@endsection

@section('javascript')

<script type="text/javascript">
	
	$(document).on("change", "#new_select", function() {

		var term = $(this).val();

		switch(term) {

		  case 'x':
		    // code block
		    break;

		  default:
		    input = '<input type="text" name="term_{{ $new_id }}_'+term+'" class="w-full rounded-lg px-4 py-2 border border-grey-darker" placeholder="Add new '+term+' here" />';
		}

		$('#new_term').html(input);

	});

	$(document).on("click", ".clickable-or", function() {

		var term = $(this).data('term');
		switch(term) {

		  case 'x':
		    // code block
		    break;

		  default:
		    input = '<input type="text" name="term_{{ $new_id }}_'+term+'" class="w-full rounded-lg px-4 py-2 border border-grey-darker" placeholder="Add new '+term+' here" />';
		}

		$('#new_term').html(input);

	});

</script>


@endsection
