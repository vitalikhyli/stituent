@extends('campaign.base')

@section('title')
    Edit Tag
@endsection

@section('style')

	@livewireScripts()

@endsection

@section('main')

<div class="w-full">


	<div class="text-3xl font-bold border-b-4 pb-2">

		<i class="text-center fa fa-tag mr-2"></i> 
		{{ $tag->name}}

		<span class="text-blue">: Edit</span>

	 	<div class="float-right text-sm mt-2">
		      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg py-2 px-4 text-red text-center ml-2 bg-grey-lighter font-normal"/>
		        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Tag
		      </button>
	 	 </div>

	</div>

	<form id="update_tag-form" action="/{{ Auth::user()->team->app_type }}/tags/{{ $tag->id }}/update" method="POST">

     	@csrf



				<!-- Modal body-->

				@if(session('errors'))
					<div class="p-2">
						@foreach(session('errors')->messages() as $field => $message)
							<div class="text-red">{{ $message[0] }}</div>
						@endforeach
					</div>
				@endif
					
				<div class="p-1">

					<div class="flex -my-1">
						<div class="text-sm uppercase text-right p-2 pt-4 whitespace-no-wrap w-32">
							Tag Name
						</div>
						<div class="p-2 w-full">
							<input required name="name" id="name" type="text" class="w-1/3 border rounded-lg p-2" placeholder="Tag Name" value="{{ (old('name')) ? old('name') : $tag->name }}" />
						</div>
					</div>

				</div>

	


				<div class="p-1">

					<div class="flex">
						<div class="text-sm uppercase text-right p-2 pt-3 whitespace-no-wrap w-32">
							Lookup
						</div>
						<div class="p-2 w-full" wire:key="connector_tag">
						@livewire('connector', [
												'class' => 'App\Participant',
												'model' => $tag,
												'show_linked' => false,
												])
						</div>
					</div>
					
				</div>




				<!-- Modal footer -->

				<div class="modal-footer bg-grey-lightest text-white">

					<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/tags/{{ $tag->id }}/update/close" class="btn btn-primary">
						Save and Close
					</button>

					<button type="submit" class="btn btn-default">
						Save
					</button>

				</div>



	</form>


<!---------------------------- MODALS ---------------------------->


	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
			</div>
			<div class="modal-body">
			  <div class="text-lg text-left text-red font-bold">
			    Are you sure you want to delete this tag?
			  </div>
			  <div class="text-left font-bold py-2 text-base">
			    This will delete the tag and remove it from tagged participants.
			  </div>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
			  <a href="/{{ Auth::user()->team->app_type }}/tags/{{ $tag->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
			</div>
		</div>
	</div>

<!-------------------------- END MODALS -------------------------->

@endsection


@section('javascript')

	@livewireScripts()

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#name').focus();

		});

	</script>

@endsection