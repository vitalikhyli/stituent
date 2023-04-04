@extends(Auth::user()->team->app_type.'.base')

@section('title')
    New @lang('Constituent')
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">@lang('Constituents')</a>
    > &nbsp;<b>New</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/save">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<div class="float-right text-base">

			@if (Auth::user()->permissions->createconstituents)
				<input type="submit" name="save" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
			@else
				<span class="text-red">
					<i class="fa fa-alert"></i> You do not currently have permission to Create a new Constituent.
				</span>
			@endif
			
			<a href="{{ url()->previous() }}">
				<button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest text-white text-center ml-2"/>
					Cancel
				</button>
			</a>

		</div>


		<span class="text-2xl">
		<i class="fas fa-user-circle mr-2"></i>
		New @lang('Constituent')
		</span>
	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				First Name
			</td>
			<td class="p-2">

				<input name="first_name" placeholder="First Name" value="{{ ucfirst($first_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>

			</td>
		</tr>
	
			<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				Last Name
			</td>
			<td class="p-2">


				<input name="last_name"  placeholder="Last Name" value="{{ ucfirst($last_name) }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>


			</td>
		</tr>

	</table>


	<div class="flex mt-2">
	@foreach ($categories as $thecategory)
	  <div class="mr-4 p-2 cursor-pointer flex-1">

	    <div class="flex text-sm tracking-wide text-left uppercase pb-1 border-b-4 border-blue">
	        {{ $thecategory->name }}
	        ({{ $thecategory->groups->where('team_id',Auth::user()->team->id)->count() }})
	    </div>

	    <div class="mt-2 text-sm">
	    @foreach ($thecategory->groups->where('team_id',Auth::user()->team->id)->sortBy('name') as $thegroup)
	      <div class="w-full flex flex-wrap">

		      	<div class="flex-grow mr-4">
		           <input type="checkbox" name="group_{{ $thegroup->id }}" id="{{ $thegroup->id }}" class="group-checkbox" {!! ($thegroup->cat->has_position) ? 'data-has_position="true"' : '' !!} {!! ($thegroup->cat->has_title) ? 'data-has_title="true"' : '' !!} {!! ($thegroup->cat->has_notes) ? 'data-has_notes="true"' : '' !!} />
		           <label class="ml-2 font-normal" for="{{ $thegroup->id }}">{{ $thegroup->name }}</label>
		        </div>

		        @if($thegroup->cat->has_position)
		            <div id="{{ $thegroup->id }}_position-div" class="hidden float-right">
		           		<select name="position_{{ $thegroup->id }}" id="position_{{ $thegroup->id }}">
		           			<option value="">--</option>
			           		<option value="supports">Supports</option>
			           		<option value="undecided">Undecided</option>
			           		<option value="concerned">Concerned</option>
			           		<option value="opposed">Opposed</option>
		           		</select>
		            </div>
	            @endif

		        @if($thegroup->cat->has_title)
		            <div id="{{ $thegroup->id }}_title-div" class="hidden float-right">
		           		<input type="text" placeholder="Title" name="title_{{ $thegroup->id }}" id="title_{{ $thegroup->id }}" class="border px-2 py-1 {{ (!$loop->first) ? 'mt-1' : '' }}" />
		            </div>
	            @endif

	      </div>

	        @if($thegroup->cat->has_notes)
	            <div id="{{ $thegroup->id }}_notes-div" class="hidden w-full mt-1">
	           		<textarea placeholder="Notes" name="notes_{{ $thegroup->id }}" id="notes_{{ $thegroup->id }}" class="border px-2 py-1  w-full" ></textarea>
	            </div>
            @endif

	    @endforeach
	    </div>

	  </div>
	  @endforeach
	</div>

</form>

<br /><br />


@endsection

@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {

		$(document).on("click", ".group-checkbox", function() {

			id = $(this).attr('id');
			has_position = $(this).data('has_position');
			has_title = $(this).data('has_title');
			has_notes = $(this).data('has_notes');

			if (has_position == true) {
				$('#'+id+'_position-div').toggleClass('hidden');
				if (!$(this).is(':checked')) {
					$('#position_'+id).val('');
				}
			}

			if (has_title == true) {
				$('#'+id+'_title-div').toggleClass('hidden');
				if (!$(this).is(':checked')) {
					$('#title_'+id).val('');
				}
			}

			if (has_notes == true) {
				$('#'+id+'_notes-div').toggleClass('hidden');
				if (!$(this).is(':checked')) {
					$('#notes_'+id).val('');
				}
			}
		});

	});
</script>
@endsection