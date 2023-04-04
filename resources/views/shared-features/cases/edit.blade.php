@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit Case
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a>

		> <a href="/{{ Auth::user()->team->app_type }}/cases" }}>Cases</a>

@endsection 


@section('style')

	<style>
		.btn.trio {
			background-color:gray;
			opacity:0.5;
			font-size:110%;
			color:#edf2f7;
		}
		.btn.trio.active {
		  opacity:1;
		  font-weight:bold;
		  color:white;
		}
		#priority_high.btn.trio.active  {
		  background-color:#e53e3e;
		}
		#priority_medium.btn.trio.active {
		  background-color:#4299e1;
		}
		#priority_low.btn.trio.active {
		  background-color:#48bb78;
		}
	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/save">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

      <button type="button" data-toggle="modal" data-target="#deleteModal" name="remove" id="remove" class="rounded-lg px-4 py-2 text-red text-center ml-2 text-sm float-right">
        <i class="fas fa-exclamation-triangle mr-2"></i> Delete this Case
      </button>

		<i class="fa fa-folder mr-2"></i> Edit Case
			
	</div>


	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full">

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Type
			</td>
			<td class="p-2 flex">

				@if($available_types->first())
					<select name="type" id="type" class="mr-4">
						<option {{ ($thecase->type == '') ? 'selected' : '' }} value="">--</option>
						@foreach($available_types as $thetype)
							<option {{ ($thecase->type == $thetype) ? 'selected' : '' }} value="{{ $thetype }}">{{ $thetype }}</option>
						@endforeach
					</select>
				@endif

				<input type="text" name="type_new" id="type_new" placeholder="New Case Type" class="border-2 rounded-lg px-4 py-2 w-1/3" />
			</td>
		</tr>

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Subtype
			</td>
			<td class="p-2 flex">

				@if($available_subtypes->first())
					<select name="subtype" id="subtype" class="mr-4">
						<option {{ ($thecase->subtype == '') ? 'selected' : '' }} value="">--</option>
						@foreach($available_subtypes as $thesubtype)
							<option {{ ($thecase->subtype == $thesubtype) ? 'selected' : '' }} value="{{ $thesubtype }}">{{ $thesubtype }}</option>
						@endforeach
					</select>
				@endif

				<input type="text" name="subtype_new" id="subtype_new" placeholder="New Case Subtype" class="border-2 rounded-lg px-4 py-2 w-1/3" />
			</td>
		</tr>

		@if(Auth::user()->permissions->admin || $thecase->user_id == Auth::user()->id)

			<tr class="border-b">
				<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
					Privacy
				</td>
				<td class="p-4 flex">

					<div class="" data-toggle="">

					  <label class=" {{ ($thecase->private == '0') ? 'active' : '' }}" id="private_medium">
					    <input type="radio" name="private" value="0" autocomplete="off" {{ ($thecase->private == '0') ? 'checked' : '' }}> Staff
					  </label>

					  <label class="ml-8 {{ ($thecase->private == '1') ? 'active' : '' }}" id="private_high" >
					    <input type="radio" name="private" value="1" autocomplete="off" {{ ($thecase->private == '1') ? 'checked' : '' }}> Private 
					    (
					    @if(Auth::user()->permissions->admin)
					    	<select name="user_id">
					    		@foreach(Auth::user()->team->usersAll as $user)
					    			<option
					    				@if($thecase->user_id == $user->id)
					    					selected
					    				@endif
					    				value="{{ $user->id }}"
					    			>{{ $user->name }}</option>
					    		@endforeach
					    	</select>
					    @else
					    	{{ $thecase->user->name }}
					    @endif
					    & Admins)
					  </label>
					  
					</div>
				</td>
			</tr>

		@endif

		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right w-1/6 align-top pt-4">
				Priority
			</td>
			<td class="p-2 flex">

				<div class="btn-group btn-group-toggle" data-toggle="buttons">
				  <label class="text-white btn trio {{ ($thecase->priority == 'High') ? 'active' : '' }}" id="priority_high" >
				    <input type="radio" name="priority" value="high" autocomplete="off" {{ ($thecase->priority == 'High') ? 'checked' : '' }}> High
				  </label>
				  <label class="text-white btn trio {{ ($thecase->priority == 'Medium') ? 'active' : '' }}" id="priority_medium">
				    <input type="radio" name="priority" value="medium" autocomplete="off" {{ ($thecase->priority == 'Medium') ? 'checked' : '' }}> Medium
				  </label>
				  <label class="text-white btn trio {{ ($thecase->priority == 'Low') ? 'active' : '' }}" id="priority_low">
				    <input type="radio" name="priority" value="low" autocomplete="off" {{ ($thecase->priority == 'Low') ? 'checked' : '' }}> Low
				  </label>
				</div>
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
						<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}">
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
				<input type="text" name="date" value="{{ \Carbon\Carbon::parse($thecase->date)->format('m/d/Y') }}" class="datepicker border-2 rounded-lg px-4 py-2 w-1/6" />
			</td>
		</tr>
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top">
				Notes
			</td>
			<td class="p-2">
				<textarea name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full">{{ $thecase->notes }}</textarea>
			</td>
		</tr>
	</table>

  <div class="float-right pt-2 text-sm">

		<button formaction="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}/save/close" class="rounded-lg bg-blue-darker hover:bg-blue-darker text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save and Close
		</button>

		<button class="mr-2 rounded-lg bg-blue hover:bg-blue-dark text-white float-right text-sm px-8 py-2 mt-1 shadow">
			Save
		</button>
	
  </div>

</form>




<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this case?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->id }}/cases/{{ $thecase->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

@endsection

@section('javascript')

@endsection