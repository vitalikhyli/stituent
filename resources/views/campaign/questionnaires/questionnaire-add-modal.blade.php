@if(session('errors'))
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#add-tag').modal('show');
        });
    </script>
@endif


<div id="add-tag" class="modal fade" role="dialog">

	<form id="add-tag-form" action="/{{ Auth::user()->team->app_type }}/questionnaires/" method="POST">

     	@csrf

		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header {{ (session('errors')) ? 'bg-red' : 'bg-blue-dark' }} text-white">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fas fa-tag mr-2"></i> Start a New Questionnaire</h4>
				</div>

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
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Name
						</div>
						<div class="p-2 w-3/4">
							<input required name="name" id="name" type="text" class="w-full border rounded-lg p-2" placeholder="e.g. League of Conservation Voters" value="{{ old('name') }}" />
						</div>
					</div>

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Due on
						</div>
						<div class="p-2">
							<input required autocomplete="off" name="due" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
						</div>
					</div>

				</div>

	


				<!-- Modal footer -->

				<div class="modal-footer bg-grey-light text-white">

					<button type="button" class="btn btn-default" data-dismiss="modal">
						Close
					</button>

					<button type="submit" class="btn btn-primary">
						Save
					</button>

				</div>
			</div>
		</div>

	</form>
	
</div>
